<?php


namespace app\Controllers;

use App\Controllers\CommonController;
use App\Services\CategorySerice;

class Category extends CommonController
{
	public function __construct()
	{
		$this->commonService = new CategorySerice();
		parent::__construct();

	}

	/**
	 * 分页查询
	 * @return mixed
	 */
	public function getPaginationRes()
	{
		$params = array(
			'query' => '',
			'queryRow' => '',
			'pagenum' => '',
			'pagesize' => '',
		);
		$options = $this->my_fill_options($params);
//		//token校验
//		$header = $this->message->getHeader('Authorization');
//		if ($header == null) {
//			return $this->my_response(PARAMS_FAIL, '参数错误，请重新登录');
//		}
//		$token = $header->getValue();
//		$v_res = $this->check_token($token);
//		if ($v_res == 3) {
//			return $this->my_response(VERIFY_FAIL, 'Token验证失败，请重新登录');
//		} elseif ($v_res == 2) {
//			return $this->my_response(AUTH_LOSE, '没有调用该接口的权限');
//		}

		//模糊查询
		if (isset($options['query']) && isset($options['queryRow'])) {
			$condition['like'] = array($options['queryRow'] => $options['query']);
		}

		// 分页参数
		if (isset($options['pagenum']) && isset($options['pagesize'])) {
			$condition['limit'] = ($options['pagenum'] - 1) * $options['pagesize'];
			$condition['offset'] = $options['pagesize'];
		}
		$condition['where'] = array('level'=>1);
		$list = $this->commonService->getComplete($condition);
		$condition = unsetPage($condition);
		$total = sizeof($this->commonService->getComplete($condition));
		$twoCate = $this->commonService->get(array('level'=>2));
		foreach($list as $key => $value){
			$childrens = array();
			$children = explode(',', $value->children);
			foreach($twoCate as $value1){
				if(in_array($value1->id,$children)){
					array_push($childrens, $value1);
				}
				$list[$key]->childrens = $childrens;
			}
		}
		$data = array(
			'list' => $list,
			'total' => $total,
			'pagenum' => intval($options['pagenum'])
		);
		return $this->my_response(GET_SUCCESS, '获取成功', $data);

	}

	/**
	 *查询条件
	 * @return mixed
	 */
	public function obtain(){
		$params = array(
			'id' => '',
			'level'=>''
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
	 * 添加分类
	 * @return mixed
	 */
	public function addCate()
	{
		$params = array(
			'cateName' => '',
			'children' => '',
			'father' => '',
			'level' => ''
		);
		$options = $this->my_fill_options($params);
		//token校验
//		$header = $this->message->getHeader('Authorization');
//		if ($header == null) {
//			return $this->my_response(PARAMS_FAIL, '参数错误，请重新登录');
//		}
//		$token = $header->getValue();
//		$v_res = $this->check_token($token);
//		if ($v_res == 3) {
//			return $this->my_response(VERIFY_FAIL, 'Token验证失败，请重新登录');
//		} elseif ($v_res == 2) {
//			return $this->my_response(AUTH_LOSE, '没有调用该接口的权限');
//		}
		if (!isset($options['cateName']) || !isset($options['level'])) {
			return $this->my_response(PARAMS_FAIL, '参数错误');
		}
		$res1 = $this->commonService->insert($options);
		if(isset($options['father'])){
			$res2 = $this->commonService->get(array('id'=>$options['father']));
			$children_str = $res2[0]->children;
			$children_str = $children_str.','.$res1;
			$this->commonService->update(array('children'=>$children_str),array('id'=>$options['father']));
		}
		if ($res1) {
			return $this->my_response(BUILD_SUCCESS, '分类创建成功！');
		} else {
			return $this->my_response(BUILD_FAIL, '分类创建失败，请重试！');
		}
	}

	/**
	 * 修改分类
	 * @return mixed
	 */
	public function updateCate(){
		$params = array(
			'id' => '',
			'cateName' => '',
			'level' => '',
			'father' => '',
			'children' => ''
		);
		$options = $this->my_fill_options($params);
		//校验token
//		$header = $this->message->getHeader('Authorization');
//		if ($header == null) {
//			return $this->my_response(PARAMS_FAIL, '参数错误，请重新登录');
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
		if(isset($options['father'])){
			$res1 = $this->commonService->get(array('id'=>$options['id']));
			//判断父分类是否有修改
			if($res1[0]->father != $options['father']){
				//删除原父分类children
				$res2 = $this->commonService->get(array('id'=>$res1[0]->father));
				if($res2 !=null){
					$child_array = 	explode(',',$res2[0]->children);
					$key = array_search($options['id'],$child_array);
					unset($child_array[$key]);
					$child_str = implode(',',$child_array);
					$this->commonService->update(array('children'=>$child_str),array('id'=>$res2[0]->id));
				}
				//为新父分类添加chidren
				$res3 = $this->commonService->get(array('id'=>$options['father']));
				$child_str1 = $res3[0]->children;
				$child_str1 = $child_str1.','.$options['id'];
				$this->commonService->update(array('children'=>$child_str1),array('id'=>$res3[0]->id));
			}

		}
		$where = array('id' => $options['id']);
		unset($options['id']);
		$res = $this->commonService->update($options, $where);
		if ($res) {
			return $this->my_response(OPERATE_SUCCESS, '角色信息修改成功');
		} else {
			return $this->my_response(OPERATE_FAIL, '角色信息修改失败');
		}
	}
}
