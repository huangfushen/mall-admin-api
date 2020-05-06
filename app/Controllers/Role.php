<?php
/**
 * Created by PhpStorm.
 * User: huangfs
 * Date: 2020/5/1
 * Time: 3:16
 */

namespace App\Controllers;

use App\Services\RoleService;
use App\Services\MenuService;
use App\Services\RoleAuthService;


class Role extends BaseController
{
	public function __construct()
	{
		parent::__construct();
		$this->RoleService = new RoleService();
		$this->RoleAuthService = new RoleAuthService();
		$this->MenuService = new MenuService();
	}

	public function index()
	{
		echo 'success';
	}


	/**
	 * 获取所有角色及权限(树结构)
	 */
	public function getRoleRightList()
	{
        //token校验
        $header = $this->message->getHeader('Authorization');
        if($header == null){
            return $this->my_response(PARAMS_FAIL,'参数错误，请重新登录');
        }
        $token = $header->getValue();
        if(!$this->check_token($token)){
            return $this->my_response(VERIFY_FAIL,'Token验证失败，请重新登录');
        }
		$roles = $this->RoleService->get();
		$auth = $this->RoleAuthService->get();
		$menuOne = $this->MenuService->getLevelMenu(0);
		$menuTwo = $this->MenuService->getLevelMenu(1);
		$menuThird = $this->MenuService->getLevelMenu(2);
		//$menuList = $this->MenuService->getLevelMenu(0);
		foreach ($roles as $key => $role) {
			$pid = array();
			foreach ($auth as $value) {
				if ($role->id == $value->rid) {
					array_push($pid, $value->pid);
				}
			}
			foreach ($menuTwo as &$value2) {
				$this->setChild($value2, $menuThird, $pid);
			}
			unset($value2);
			foreach ($menuOne as &$value1) {
				$this->setChild($value1, $menuTwo, $pid);
			}
			unset($value1);
			$roles[$key]->children = implode(',', $pid);
			$this->setChild($roles[$key], $menuOne, $pid);

		}

		$data = array('roleList' => $roles);
		return $this->my_response(GET_SUCCESS, '获取成功', $data);
	}

	/**
	 * 根据id获取角色信息
	 * @return mixed
	 */
	public function getRoleRightById()
	{
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
		if (!$this->check_token($token)) {
			return $this->my_response(VERIFY_FAIL, 'Token验证失败，请重新登录');
		}
		if (!isset($options['id'])) {
			return $this->my_response(PARAMS_FAIL, '参数错误');
		}
		$res = $this->RoleService->get($options);
		$data = array(
			'id' => $res[0]->id,
			'roleName' => $res[0]->roleName,
			'roleDesc' => $res[0]->roleDesc
		);
		return $this->my_response(GET_SUCCESS, '获取成功', $data);
	}

	/**
	 * 删除角色权限
	 * @return mixed
	 */
	public function delRoleRight()
	{
		$params = array(
			'rid' => '',
			'pid' => '',
		);
		$options = $this->my_fill_options($params);
		//token校验
		$header = $this->message->getHeader('Authorization');
		if ($header == null) {
			return $this->my_response(PARAMS_FAIL, '参数错误，请重新登录');
		}
		$token = $header->getValue();
		if (!$this->check_token($token)) {
			return $this->my_response(VERIFY_FAIL, 'Token验证失败，请重新登录');
		}
		if (!isset($options['rid']) || !isset($options['pid'])) {
			return $this->my_response(PARAMS_FAIL, '参数错误');
		}
		$this->RoleAuthService->delete($options);

		//权限列表
		$auth = $this->RoleAuthService->get(array('rid' => $options['rid']));
		$menuOne = $this->MenuService->getLevelMenu(0);
		$menuTwo = $this->MenuService->getLevelMenu(1);
		$menuThird = $this->MenuService->getLevelMenu(2);
		$pid = array();
		foreach ($auth as $value) {
			array_push($pid, $value->pid);
		}
		foreach ($menuTwo as $key2 => $value2) {
			$this->setChild($menuTwo[$key2], $menuThird, $pid);
		}
		foreach ($menuOne as $key1 => $value1) {
			$this->setChild($value1, $menuTwo, $pid);
			if (!in_array($value1->id, $pid)) {
				unset($menuOne[$key1]);
			}
		}
		$data = array('roleAuth' => $menuOne);
		return $this->my_response(OPERATE_SUCCESS, '操作成功', $data);
	}

	public function getRightList(){
		//token校验
		$header = $this->message->getHeader('Authorization');
		if ($header == null) {
			return $this->my_response(PARAMS_FAIL, '参数错误，请重新登录');
		}
		$token = $header->getValue();
		if (!$this->check_token($token)) {
			return $this->my_response(VERIFY_FAIL, 'Token验证失败，请重新登录');
		}
		$menuOne = $this->MenuService->getLevelMenu(0);
		$menuTwo = $this->MenuService->getLevelMenu(1);
		$menuThird = $this->MenuService->getLevelMenu(2);
		foreach ($menuTwo as $key2 => $value2) {
			$this->setChild($menuTwo[$key2], $menuThird);
		}
		foreach ($menuOne as $key1 => $value1) {
			$this->setChild($value1, $menuTwo);
		}
		$data = array('rightList' => $menuOne);
		return $this->my_response(GET_SUCCESS, '获取成功', $data);

	}


	/**
	 * 根据id获取角色权限
	 * @return mixed
	 */
	public function getRoleRight()
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
		if (!$this->check_token($token)) {
			return $this->my_response(VERIFY_FAIL, 'Token验证失败，请重新登录');
		}
		if (!isset($options['id'])) {
			return $this->my_response(PARAMS_FAIL, '参数错误');
		}
		//权限列表
		$auth = $this->RoleAuthService->get(array('id' => $options['id']));
		$menuOne = $this->MenuService->getLevelMenu(0);
		$menuTwo = $this->MenuService->getLevelMenu(1);
		$menuThird = $this->MenuService->getLevelMenu(2);
		$pid = array();
		foreach ($auth as $value) {
			array_push($pid, $value->pid);
		}
		foreach ($menuTwo as $key2 => $value2) {
			$this->setChild($menuTwo[$key2], $menuThird, $pid);
		}
		foreach ($menuOne as $key1 => $value1) {
			$this->setChild($value1, $menuTwo, $pid);
			if (!in_array($value1->id, $pid)) {
				unset($menuOne[$key1]);
			}
		}
		$data = array('roleAuth' => $menuOne);
		return $this->my_response(OPERATE_SUCCESS, '操作成功', $data);
	}

	/**
	 * 权限分配
	 * @return mixed
	 */
	public function setRoleRight(){
		$params = array(
			'rid'	=>	'',
			'pid'	=>	''
		);
		$options = $this->my_fill_options($params);
		//token校验
		$header = $this->message->getHeader('Authorization');
		if ($header == null) {
			return $this->my_response(PARAMS_FAIL, '参数错误，请重新登录');
		}
		$token = $header->getValue();
		if (!$this->check_token($token)) {
			return $this->my_response(VERIFY_FAIL, 'Token验证失败，请重新登录');
		}
		if (!isset($options['rid'])) {
			return $this->my_response(PARAMS_FAIL, '参数错误');
		}
		if(!isset($options['pid'])){
			$this->RoleAuthService->delete(array('rid'=>$options['rid']));
			return $this->my_response(OPERATE_SUCCESS, '操作成功');
		}
		$arr = explode(',', $options['pid']);
		$auth = $this->RoleAuthService->get(array('rid'=>$options['rid']));
		$pid = array();
		foreach ($auth as $value) {
			array_push($pid, $value->pid);
		}
		foreach($arr as $v){
			if(!is_numeric($v)){
				return $this->my_response(PARAMS_FAIL, '参数错误');
			}
			if(!in_array($v,$pid)){
				$this->RoleAuthService->insert(array('rid'=>$options['rid'],'pid'=>$v));
			}
		}
		foreach($pid as $v1){
			if(!in_array($v1,$arr)){
				$this->RoleAuthService->delete(array('rid'=>$options['rid'],'pid'=>$v1));
			}
		}
		return $this->my_response(OPERATE_SUCCESS, '操作成功');

	}

	/**
	 * 获取角色列表
	 * @return mixed
	 */
	public function getRoleList(){
		//token校验
		$header = $this->message->getHeader('Authorization');
		if($header == null){
			return $this->my_response(PARAMS_FAIL,'参数错误，请重新登录');
		}
		$token = $header->getValue();
		if(!$this->check_token($token)){
			return $this->my_response(VERIFY_FAIL,'Token验证失败，请重新登录');
		}
		$roles = $this->RoleService->get();
		$data = array('roleList' => $roles);
		return $this->my_response(GET_SUCCESS, '获取成功', $data);
	}
	/**
	 * 删除角色
	 * @return mixed
	 */
	public function delRole()
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
		if (!$this->check_token($token)) {
			return $this->my_response(VERIFY_FAIL, 'Token验证失败，请重新登录');
		}
		if (!isset($options['id'])) {
			return $this->my_response(PARAMS_FAIL, '参数错误');
		}
		$this->RoleService->delete($options);
		return $this->my_response(OPERATE_SUCCESS, '操作成功');
	}

	/**
	 * 添加新角色
	 * @return mixed
	 */
	public function addRole()
	{
		$params = array(
			'roleName' => '',
			'roleDesc' => ''
		);
		$options = $this->my_fill_options($params);
		//token校验
		$header = $this->message->getHeader('Authorization');
		if ($header == null) {
			return $this->my_response(PARAMS_FAIL, '参数错误，请重新登录');
		}
		$token = $header->getValue();
		if (!$this->check_token($token)) {
			return $this->my_response(VERIFY_FAIL, 'Token验证失败，请重新登录');
		}
		if (!isset($options['roleName']) || !isset($options['roleDesc'])) {
			return $this->my_response(PARAMS_FAIL, '参数错误');
		}
		$result = $this->RoleService->insert($options);
		if ($result != true) {
			return $this->my_response(REQUEST_FAIL, '角色创建失败，请重试！');
		} else {
			return $this->my_response(BUILD_SUCCESS, '角色创建成功');
		}
	}


	/**
	 * 修改角色信息
	 * @return mixed
	 */
	public function updateRole()
	{
		$params = array(
			'id' => '',
			'roleName' => '',
			'roleDesc' => ''
		);
		$options = $this->my_fill_options($params);
		//校验token
		$header = $this->message->getHeader('Authorization');
		if ($header == null) {
			return $this->my_response(PARAMS_FAIL, '参数错误，请重新登录');
		}
		$token = $header->getValue();
		if (!$this->check_token($token)) {
			return $this->my_response(VERIFY_FAIL, 'Token验证失败，请重新登录');
		}
		if (!isset($options['id'])) {
			return $this->my_response(PARAMS_FAIL, '参数错误');
		}
		if (!isset($options['roleName']) && !isset($options['roleDesc'])) {
			return $this->my_response(PARAMS_FAIL, '参数错误');
		}
		$where = array('id' => $options['id']);
		unset($options['id']);
		$res = $this->RoleService->update($options, $where);
		if ($res) {
			return $this->my_response(OPERATE_SUCCESS, '角色信息修改成功');
		} else {
			return $this->my_response(OPERATE_FAIL, '角色信息修改失败');
		}
	}

	/**
	 * 设置子权限
	 * @param $father
	 * @param $child
	 * @param $pid
	 */
	private function setChild($father, $child, $pid = null)
	{
		$childrens = array();
		$cid = $father->children;
		$child_arr = explode(',', $cid);
		if($pid != null){
			$parr = array_intersect($child_arr, $pid);
		}else{
			$parr = $child_arr;
		}
		foreach ($child as $value) {
			if (in_array($value->id, $parr)) {
				array_push($childrens, $value);
			}
			$father->childrens = $childrens;
		}
	}
}
