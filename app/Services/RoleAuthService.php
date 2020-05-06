<?php
/**
 * Created by PhpStorm.
 * User: huangfs
 * Date: 2020/5/5
 * Time: 11:52
 */

namespace App\Services;


class RoleAuthService extends BaseService
{
	function __construct()
	{
		$this->table = 'role_auth';
		parent::__construct();
	}

}
