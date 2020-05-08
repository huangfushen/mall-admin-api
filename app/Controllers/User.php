<?php

namespace App\Controllers;

use App\Services\UserService;
use App\Services\JwtAuth;
use Gregwar\Captcha\CaptchaBuilder;

/**
 * Created by PhpStorm.
 * User: huangfs
 * Date: 2020/4/19
 * Time: 12:12
 */
class User extends BaseController
{

	public function __construct()
	{
		parent::__construct();
		$this->userService = new UserService();

	}

	public function index()
	{

	}

	/**
	 * 获取用户列表
	 * @return mixed
	 */
	public function getUserList()
	{
		$params = array(
			'query' => '',
			'pagenum' => '',
			'pagesize' => '',
		);
		$options = $this->my_fill_options($params);
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
		$where = isset($options['query']) ? array('username' => $options['query']) : null;
		$limit = ($options['pagenum'] - 1) * $options['pagesize'];
		$offset = $options['pagesize'];
		$list = $this->userService->getUsers($where, $limit, $offset);
		$total = sizeof($this->userService->getUsers($where));
		$data = array(
			'userList' => $list,
			'total' => $total,
			'pagenum' => intval($options['pagenum'])
		);
		//var_dump($data);
		return $this->my_response(GET_SUCCESS, '获取成功', $data);
	}

	/**
	 * 登录
	 * @return mixed
	 */
	public function login()
	{
		//todo  1.验证码
		$params = array(
			'username' => '',
			'password' => '',
		);
		$options = $this->my_fill_options($params);
		//校验参数
		if (!isset($options['username']) && !isset($options['username'])) {
			return $this->my_response(PARAMS_FAIL, '请输入用户名密码');
		}
		$condition = array('username' => $options['username']);
		$info = $this->userService->get($condition);
		if ($info == null || $info[0]->password != salt_pass($options['password'], $info[0]->salt)) {
			return $this->my_response(LOGIN_FAIL, '用户名或密码错误');
		}
		$condition = array('last_login' => date('Y-m-d h:i:s'));
		$where = array('id' => $info[0]->id);
		$this->userService->update($condition, $where);
		//生成token
		$jwtAuth = JwtAuth::getInstance();
		$token = $jwtAuth->setUid($info[0]->id)->encode()->getToken();
		//redis 存储token
		$cache = \Config\Services::cache();
		$cache->save($token, $info, 60 * 30);
		$data = array('token' => $token);
		return $this->my_response(LOGIN_SUCCESS, '登录成功', $data);

	}

	/**
	 * 添加用户
	 * @return mixed
	 */
	public function addUser()
	{
		//todo  1.用户名重复
		$params = array(
			'username' => '',
			'password' => '',
			'telphone' => '',
			'email' => ''
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
		//校验参数
		if (!isset($options['telphone'])) {
			return $this->my_response(PARAMS_FAIL, '请输入手机号');
		}
		if (!preg_match('/^\d{11}$/', $options['telphone'])) {
			return $this->my_response(PARAMS_FAIL, '手机格式错误');
		}
		if (!isset($options['email'])) {
			return $this->my_response(PARAMS_FAIL, '请输入邮箱');
		}
		if (!preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/', $options['email'])) {
			return $this->my_response(PARAMS_FAIL, '邮箱格式错误');
		}
		if (!isset($options['username']) && !isset($options['username'])) {
			return $this->my_response(PARAMS_FAIL, '请输入用户名密码');
		}
		$condition = array(
			'username' => $options['username']
		);
		if ($this->userService->get($condition) != null) {
			return $this->my_response(PARAM_REPEAT, '用户名重复');
		}
		$options['mg_state'] = 'true';
		$options['salt'] = crypt($options['username'], $options['password']);
		$options['create_time'] = date('y-m-d h:i:s');
		$options['last_login'] = date('y-m-d h:i:s');
		$options['password'] = salt_pass($options['password'], $options['salt']);
		unset($options['token']);
		$result = $this->userService->insert($options);
		if ($result != null) {
			return $this->my_response(BUILD_SUCCESS, '用户创建成功');
		} else {
			return $this->my_response(REQUEST_FAIL, '用户创建失败，请重试！');
		}

	}

	/**
	 * 修改用户信息
	 * @return mixed
	 */
	public function updateUser()
	{
		$params = array(
			'id' => '',
			'username' => '',
			'password' => '',
			'telphone' => '',
			'email' => '',
			'role_name' => '',
			'type' => '',
			'mg_state' => '',
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
		} elseif ($v_res == 2) {
			return $this->my_response(AUTH_LOSE, '没有调用该接口的权限');
		}
		if (!isset($options['id'])) {
			return $this->my_response(PARAMS_FAIL, '参数错误');
		}
		//构造参数，调用model
		$where = array('id' => $options['id']);
		unset($options['id']);
		$res = $this->userService->update($options, $where);
		if ($res) {
			return $this->my_response(OPERATE_SUCCESS, '用户信息修改成功');
		} else {
			return $this->my_response(OPERATE_FAIL, '用户信息修改失败');
		}
	}

	/**
	 * 根据id获取用户部分信息
	 * @return mixed
	 */
	public function getUserById()
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
		$v_res = $this->check_token($token);
		if ($v_res == 3) {
			return $this->my_response(VERIFY_FAIL, 'Token验证失败，请重新登录');
		} elseif ($v_res == 2) {
			return $this->my_response(AUTH_LOSE, '没有调用该接口的权限');
		}
		if (!isset($options['id'])) {
			return $this->my_response(PARAMS_FAIL, '参数错误');
		}
		$res = $this->userService->get($options);
		$data = array(
			'id' => $res[0]->id,
			'username' => $res[0]->username,
			'email' => $res[0]->email,
			'telphone' => $res[0]->telphone
		);
		return $this->my_response(GET_SUCCESS, '获取成功', $data);

	}

	/**
	 * 用户删除
	 * @return mixed
	 */
	public function deleteUser()
	{
		$params = array(
			'id' => ''
		);
		$options = $this->my_fill_options($params);
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
		if (!isset($options['id'])) {
			return $this->my_response(PARAMS_FAIL, '参数错误');
		}
		$this->userService->delete($options);
		return $this->my_response(OPERATE_SUCCESS, '删除用户成功');
	}

	/**
	 * 登出
	 * @return mixed
	 */
	public function loginout()
	{
		$header = $this->message->getHeader('Authorization');
		if ($header == null) {
			return $this->my_response(PARAMS_FAIL, '参数错误');
		}
		$token = $header->getValue();
//        $v_res = $this->check_token($token);
//		if ($v_res == 3) {
//			return $this->my_response(VERIFY_FAIL, 'Token验证失败，请重新登录');
//		}elseif($v_res == 2){
//			return $this->my_response(AUTH_LOSE,'没有调用该接口的权限');
//		}
		$cache = \Config\Services::cache();
		$cache->delete($token);
		return $this->my_response(OPERATE_SUCCESS, '退出登录成功');
	}

	/**
	 * 图片验证码
	 * @return mixed
	 */
	public function getCaptcha()
	{
		//生成16位随机数作为key值
		$str = str_rand(16);
		$builder = new CaptchaBuilder;
		$builder->build();
		//保存图片
		$builder->save('images/img_' . time() . rand(1, 20) . '.jpg');
		$cache = \Config\Services::cache();
		//获取验证码值
		$code = $builder->getPhrase();
		$cache->save($str, $code, 300);
		$data = array(
			'key' => $str,
			'img_url' => 'images/img_' . time() . rand(1, 20) . '.jpg'
		);
		return $this->my_response(GET_SUCCESS, '获取成功', $data);
	}
}
