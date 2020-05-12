<?php


namespace app\Controllers;

use App\Controllers\CommonController;
use App\Services\CateParamsSerice;

class CateParams extends CommonController
{
	public function __construct()
	{
		$this->commonService = new CateParamsSerice();
		parent::__construct();
	}

	/**
	 * 根据条件查询分类参数
	 * @param 	id  非必填
	 * @param 	cateId  非必填
	 * @param 	attrSel  非必填
	 * @return mixed
	 */
	public function obtain(){
		$params = array(
			'id' => '',
			'cateId' => '',
			'attrSel' => ''
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
	 * @param 	attrName  必填
	 * @param 	cateId  必填
	 * @param 	attrSel  必填
	 * @param 	attrVals  非必填
	 * @return  mixed
	 */
	public function addParam(){
		$params = array(
			'attrName' => '',
			'cateId' => '',
			'attrSel' => '',
			'attrVals' => ''
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
		if (!isset($options['attrName']) || !isset($options['cateId']) || !isset($options['attrSel'])) {
			return $this->my_response(PARAMS_FAIL, '参数错误');
		}
		$result = $this->commonService->insert($options);
		if ($result != null) {
			return $this->my_response(BUILD_SUCCESS, '新增分类成功');
		} else {
			return $this->my_response(BUILD_FAIL, '新增分类失败，请重试！');
		}
	}

	public function updateParam(){
		$params = array(
			'id' => '',
			'attrName' => '',
			'cateId' => '',
			'attrSel' => '',
			'attrWrite' =>'',
			'attrVals' => ''
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
		if(isset($options['attrVals'])){
			// 与前端约定一个为空的符号
			if($options['attrVals'] == '^'){
				$options['attrVals']='';
			}
		}
		$where = array('id' => $options['id']);
		unset($options['id']);
		$res = $this->commonService->update($options, $where);
		if ($res) {
			return $this->my_response(OPERATE_SUCCESS, '分类参数修改成功');
		} else {
			return $this->my_response(OPERATE_FAIL, '分类参数修改失败');
		}
	}
}
