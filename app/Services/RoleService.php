<?php
/**
 * Created by PhpStorm.
 * User: huangfs
 * Date: 2020/5/1
 * Time: 3:18
 */

namespace App\Services;

class RoleService extends BaseService
{
    function __construct()
    {
        $this->table ='roles';
        parent::__construct();
    }


}