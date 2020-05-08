<?php
/**
 * Created by PhpStorm.
 * User: huangfs
 * Date: 2020/4/22
 * Time: 9:07
 */

namespace App\Services;
class UserService extends BaseService
{
	function __construct()
	{
		$this->table = 'users';
		parent::__construct();
	}

	/**
	 * 获取用户列表(分页)
	 * @return array|mixed
	 */
	function getUsers($where = null, $limit = null, $offset = null)
	{
		if ($where != null) {
			$this->builder->like($where);
		}
		$query = $this->builder->getWhere(null, $offset, $limit);
		$result = $query->getResult();
		return $result;
	}


}
