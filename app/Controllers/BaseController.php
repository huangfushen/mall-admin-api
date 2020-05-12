<?php

namespace App\Controllers;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 *
 * @package CodeIgniter
 */

use App\Services\RoleAuthService;
use App\Services\MenuService;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Controller;
use App\Services\JwtAuth;
use CodeIgniter\HTTP\Message;

class BaseController extends Controller
{

	/**
	 * An array of helpers to be loaded automatically upon
	 * class instantiation. These helpers will be available
	 * to all other controllers that extend BaseController.
	 *
	 * @var array
	 */
	protected $helpers = [];
	protected $message;
	use  ResponseTrait;

	public function __construct()
	{
		$this->roleAuthService = new RoleAuthService();
		$this->menuService = new MenuService();
		$this->message = new Message();
		$this->message->getHeaders();
		helper(['my_helper']);
	}

	/**
	 * Constructor.
	 */
	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		// Do Not Edit This Line
		parent::initController($request, $response, $logger);
		//--------------------------------------------------------------------
		// Preload any models, libraries, etc, here.
		//--------------------------------------------------------------------
		// E.g.:
		// $this->session = \Config\Services::session();
	}

	/**
	 * 获取请求参数以数组的方式返回
	 * @param array $options
	 * @param string $prefix
	 * @return |null
	 */
	public function my_fill_options($options = array(), $prefix = '')
	{
		$pramas = null;
		foreach ($options as $key => $val) {
			$options[$key] = trim($this->request->getGetPost("{$prefix}{$key}"));
		}
		foreach ($options as $key => $value) {
			if (!empty($value)) {
				$pramas[$key] = $value;
			}
		}
		return $pramas;
	}

	/**
	 * 请求返回
	 * @param $status
	 * @param $message
	 * @param null $data
	 * @return mixed
	 */
	public function my_response($status, $message, $data = null)
	{
		$return_data = array(
			'rcode' => $status,
			'message' => $message,
		);
		if ($data != null) {
			$return_data['data'] = $data;
		}
		return $this->respond($return_data, 200);
	}

	/**
	 *
	 * 验证Token
	 * @param $token
	 * @return int  1：通过 2：权限不足  3：token校验失败
	 */
	public function check_token($token)
	{
		$cache = \Config\Services::cache();
		if ($cache->get($token) == false) {
			return 3;
		}
		$jwtAuth = JwtAuth::getInstance();
		$jwtAuth->setToken($token);
		if ($jwtAuth->validate() && $jwtAuth->verify()) {
			$cache->updateTime($token, 60 * 30);//续期半小时
			return $this->check_auth($jwtAuth->getUid());
		} else {
			return 3;
		}
	}

	/**
	 * 权限校验
	 * @param $id
	 * @return int
	 */
	private function check_auth($id){
		$path = $this->request->uri->getPath();
		$auth = $this->roleAuthService->get(array('rid' => $id));
		if($auth ==null ){
			return 2;
		}
		$pid = array();
		foreach ($auth as $value) {
			array_push($pid, $value->pid);
		}
		$auth = $this->menuService->getMenuById($pid);
		foreach($auth as $key=>$value){
			if($value->level == 2){
				if(strcmp($value->path,$path)==0){
					return 1;
				}

			}
		}
		return 2;
	}

}
