<?php
/**
 * Created by PhpStorm.
 * User: huangfs
 * Date: 2020/4/28
 * Time: 16:28
 */

namespace App\Controllers;

use App\Services\MenuService;

class Menu extends BaseController
{

	public function __construct()
	{
		parent::__construct();
		$this->MenuService = new MenuService();
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
		if (!$this->check_token($token)) {
			return $this->my_response(VERIFY_FAIL, 'Token验证失败，请重新登录');
		}
		//获取菜单列表
		$menuList = $this->MenuService->getLevelMenu(0);
		foreach ($menuList as $key => $item) {
			$child_str = $item->children;
			$child_array = explode(',', $child_str);
			$children = $this->MenuService->getMenuById($child_array);
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
		//todo 分页
		//token校验
		$header = $this->message->getHeader('Authorization');
		if ($header == null) {
			return $this->my_response(PARAMS_FAIL, '参数错误，请重新登录');
		}
		$token = $header->getValue();
		if (!$this->check_token($token)) {
			return $this->my_response(VERIFY_FAIL, 'Token验证失败，请重新登录');
		}
		$menuList = $this->MenuService->get();
		$data = array('menuList' => $menuList);
		return $this->my_response(GET_SUCCESS, '获取成功', $data);
	}
}
