<?php
/**
 * Created by PhpStorm.
 * User: huangfs
 * Date: 2020/4/28
 * Time: 16:28
 */

namespace App\Controllers;



class Menu extends BaseController
{
	protected $menuService;

	public function __construct()
	{

		parent::__construct();

	}

	public function index()
	{
		echo 'success';
	}

	/**
	 * 获取（一级二级）菜单列表(树形形式)
	 * @return mixed
	 */
	public function getMenuList()
	{
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
		//获取菜单列表
		$menuList = $this->menuService->getLevelMenu(0);
		foreach ($menuList as $key => $item) {
			$child_str = $item->children;
			$child_array = explode(',', $child_str);
			$children = $this->menuService->getMenuById($child_array);
			$menuList[$key]->children = $children;
		}
		$data = array('menuList' => $menuList);
		return $this->my_response(GET_SUCCESS, '获取成功', $data);
	}

	/**
	 * 获取菜单列表（权限）
	 * @return mixed
	 */
	public function getAllMenu()
	{
		$params = array(
			'query' => '',
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
		if (!isset($options['pagenum']) || !isset($options['pagesize'])) {
			return $this->my_response(PARAMS_FAIL, '参数错误');
		}
		// 分页
		$where = isset($options['query']) ? array('authName' => $options['query']) : null;
		$limit = ($options['pagenum'] - 1) * $options['pagesize'];
		$offset = $options['pagesize'];
		$list = $this->menuService->getMenus($where, $limit, $offset);
		$total = sizeof($this->menuService->getMenus($where));
		$data = array(
			'menuList' => $list,
			'total' => $total,
			'pagenum' => intval($options['pagenum'])
		);
		return $this->my_response(GET_SUCCESS, '获取成功', $data);
	}

	/**
	 * 获取等级菜单
	 * @return mixed
	 */
	public function getLevelMenu()
	{
		$params = array(
			'level' => ''
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
		}elseif($v_res == 2){
			return $this->my_response(AUTH_LOSE,'没有调用该接口的权限');
		}
		if($options['level'] == null){
			$options['level'] = 0 ;
		}elseif ($options['level'] == -1){
			$options['level'] = 0 ;
		}elseif ($options['level'] == 3){
			$options['level'] = 0 ;
		}
		if ($options['level'] == 0 || $options['level'] == 1 || $options['level'] == 2) {

			$levelMenu = $this->menuService->getLevelMenu($options['level']);
			$data = array(
				'levelMenu' => $levelMenu
			);
			return $this->my_response(GET_SUCCESS, '获取成功', $data);
		}
		return $this->my_response(PARAMS_FAIL, '参数错误');
	}

	public function getMenuById(){
		$params = array(
			'id' => ''
		);
		$options = $this->my_fill_options($params);
		//校验token
		$header = $this->message->getHeader('Authorization');
		if ($header == null) {
			return $this->my_response(PARAMS_FAIL, '参数错误，请重新登录');
		}
		$token = $header->getValue();
		$v_res = $this->check_token($token);
		if ($v_res == 3) {
			return $this->my_response(VERIFY_FAIL, 'Token验证失败，请重新登录');
		}elseif($v_res == 2){
			return $this->my_response(AUTH_LOSE,'没有调用该接口的权限');
		}
		if (!isset($options['id'])) {
			return $this->my_response(PARAMS_FAIL, '参数错误');
		}
		$res = $this->menuService->get($options);
		if($res[0]->children == null){
			$child = null;
		}else{
			$child = explode(',',$res[0]->children);
		}

		$data = array(
			'id' => intval($res[0]->id),
			'authName' => $res[0]->authName,
			'path' => $res[0]->path,
			'children' => $child,
			'father' => $res[0]->father,
			'order' => $res[0]->order,
			'level' => intval($res[0]->level),
		);
		return $this->my_response(GET_SUCCESS, '获取成功', $data);
	}

	/**
	 * 修改菜单权限
	 * @return mixed
	 */
	public function updateMenu(){
		$params = array(
			'id' => '',
			'authName' => '',
			'path' => '',
			'level' => '',
			'father' => '',
			'children' => '',
			'order' => ''
		);
		$options = $this->my_fill_options($params);
		//校验token
		$header = $this->message->getHeader('Authorization');
		if ($header == null) {
			return $this->my_response(PARAMS_FAIL, '参数错误，请重新登录');
		}
		$token = $header->getValue();
		$v_res = $this->check_token($token);
		if ($v_res == 3) {
			return $this->my_response(VERIFY_FAIL, 'Token验证失败，请重新登录');
		}elseif($v_res == 2){
			return $this->my_response(AUTH_LOSE,'没有调用该接口的权限');
		}
		if (!isset($options['id'])) {
			return $this->my_response(PARAMS_FAIL, '参数错误');
		}
		if(isset($options['father'])){
			$res1 = $this->menuService->get(array('id'=>$options['id']));
				//判断父权限是否有修改
				if($res1[0]->father != $options['father']){
					//删除原父权限children
					$res2 = $this->menuService->get(array('id'=>$res1[0]->father));
					if($res2 !=null){
						$child_array = 	explode(',',$res2[0]->children);
						$key = array_search($options['id'],$child_array);
						unset($child_array[$key]);
						$child_str = implode(',',$child_array);
						$this->menuService->update(array('children'=>$child_str),array('id'=>$res2[0]->id));
					}
					//为新父权限添加chidren
					$res3 = $this->menuService->get(array('id'=>$options['father']));
					$child_str1 = $res3[0]->children;
					$child_str1 = $child_str1.','.$options['id'];
					$this->menuService->update(array('children'=>$child_str1),array('id'=>$res3[0]->id));
				}

		}
		$where = array('id' => $options['id']);
		unset($options['id']);
		$res = $this->menuService->update($options, $where);
		if ($res) {
			return $this->my_response(OPERATE_SUCCESS, '角色信息修改成功');
		} else {
			return $this->my_response(OPERATE_FAIL, '角色信息修改失败');
		}
	}
	/**
	 * 添加菜单
	 * @return mixed
	 */
	public function addMenu()
	{
		$params = array(
			'authName' => '',
			'path' => '',
			'children' => '',
			'father' => '',
			'order' => '',
			'level' => ''
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
		if (!isset($options['authName']) || !isset($options['path']) || !isset($options['order'])) {
			return $this->my_response(PARAMS_FAIL, '参数错误');
		}
		if(!isset($options['level'])){
			$options['level'] = 0;
		}
		$res1 = $this->menuService->insert($options);
		if(isset($options['father'])){
			$res2 = $this->menuService->get(array('id'=>$options['father']));
			$children_str = $res2[0]->children;
			$children_str = $children_str.','.$res1;
			$res3 = $this->menuService->update(array('children'=>$children_str),array('id'=>$options['father']));
		}
		if ($res1) {
			return $this->my_response(BUILD_SUCCESS, '菜单权限创建成功！');
		} else {
			return $this->my_response(BUILD_FAIL, '菜单权限创建失败，请重试！');
		}
	}
	public function delMenu()
	{
		$params = array(
			'id' => ''
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
		}elseif($v_res == 2){
			return $this->my_response(AUTH_LOSE,'没有调用该接口的权限');
		}
		if (!isset($options['id'])) {
			return $this->my_response(PARAMS_FAIL, '参数错误');
		}
		$this->menuService->delete($options);
		return $this->my_response(OPERATE_SUCCESS, '操作成功');
	}
}
