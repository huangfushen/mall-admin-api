<?php


namespace App\Controllers;


class CommonController extends BaseController
{
	protected $commonService;
	public function __construct()
	{
		parent::__construct();

	}

	/**
	 * 删
	 * @return mixed
	 */
	public function remove(){
		$params = array(
			'id' => ''
		);
		$options = $this->my_fill_options($params);
		//token校验
//		$header = $this->message->getHeader('Authorization');
//		if($header == null){
//			return $this->my_response(PARAMS_FAIL,'参数错误，请重新登录');
//		}
//		$token = $header->getValue();
//		$v_res = $this->check_token($token);
//		if ($v_res == 3) {
//			return $this->my_response(VERIFY_FAIL, 'Token验证失败，请重新登录');
//		}elseif($v_res == 2){
//			return $this->my_response(AUTH_LOSE,'没有调用该接口的权限');
//		}
		if (!isset($options['id'])) {
			return $this->my_response(PARAMS_FAIL, '参数错误');
		}
		$this->commonService->delete($options);
		return $this->my_response(OPERATE_SUCCESS, '操作成功');
	}


	/**
	 * 查
	 * @return mixed
	 */
	public function obtain(){
		$params = array(
			'id' => ''
		);
		$options = $this->my_fill_options($params);
		//token校验
//		$header = $this->message->getHeader('Authorization');
//		if($header == null){
//			return $this->my_response(PARAMS_FAIL,'参数错误，请重新登录');
//		}
//		$token = $header->getValue();
//		$v_res = $this->check_token($token);
//		if ($v_res == 3) {
//			return $this->my_response(VERIFY_FAIL, 'Token验证失败，请重新登录');
//		}elseif($v_res == 2){
//			return $this->my_response(AUTH_LOSE,'没有调用该接口的权限');
//		}
		if (!isset($options)) {
			$list = $this->commonService->get();
		}else{
			$list = $this->commonService->get($options);
		}
		$data = array(
			'list'=>$list
		);
		return $this->my_response(GET_SUCCESS, '获取成功', $data);
	}

	/**
	 * 分页显示查询列表
	 * @return mixed
	 */
	public function getPaginationRes(){
		$params = array(
			'query' => '',
			'queryRow'	=>	'',
			'pagenum' => '',
			'pagesize' => '',
		);
		$options = $this->my_fill_options($params);
		//token校验
		$header = $this->message->getHeader('Authorization');
		if ($header == null) {
			return $this->my_response(PARAMS_FAIL, '参数错误，请重新登录');
		}
		$token = $header->getValue();
		$v_res = $this->check_token($token);
		if ($v_res == 3) {
			return $this->my_response(VERIFY_FAIL, 'Token验证失败，请重新登录');
		} elseif ($v_res == 2) {
			return $this->my_response(AUTH_LOSE, '没有调用该接口的权限');
		}

		//模糊查询
		if (isset($options['query']) && isset($options['queryRow'])) {
			$condition['like'] = array($options['queryRow'] => $options['query']);
		}

		// 分页参数
		if (isset($options['pagenum']) && isset($options['pagesize'])) {
			$condition['limit'] = ($options['pagenum'] - 1) * $options['pagesize'];
			$condition['offset'] = $options['pagesize'];
		}

		$list = $this->commonService->getComplete($condition);
		$condition = unsetPage($condition);
		$total = sizeof($this->commonService->getComplete($condition));
		$data = array(
			'List' => $list,
			'total' => $total,
			'pagenum' => intval($options['pagenum'])
		);
		return $this->my_response(GET_SUCCESS, '获取成功', $data);
	}
}
