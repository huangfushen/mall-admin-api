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
	public static $redisConn = '';
	use  ResponseTrait;

	public function __construct()
	{
		$this->message = new Message();
		$this->message->getHeaders();
		//$this->helpers = array_merge($this->helpers, ['my_helper']);
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
	 * 验证Token
	 * @param $token
	 * @return bool
	 */
	public function check_token($token)
	{
		$cache = \Config\Services::cache();
		if ($cache->get($token) == false) {
			return false;
		}
		$jwtAuth = JwtAuth::getInstance();
		$jwtAuth->setToken($token);
		if ($jwtAuth->validate() && $jwtAuth->verify()) {
			$cache->updateTime($token, 60 * 30);//续期半小时
			return true;
		} else {
			return false;
		}
	}

}
