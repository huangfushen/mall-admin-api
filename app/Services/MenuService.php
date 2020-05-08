<?php
/**
 * Created by PhpStorm.
 * User: huangfs
 * Date: 2020/4/28
 * Time: 16:29
 */

namespace App\Services;

class MenuService extends BaseService
{
    function __construct()
    {
        $this->table ='menus';
        parent::__construct();
    }

    /**
     * 获取等级菜单
     * @return mixed
     */
    function getLevelMenu($level){
        $this->builder->orderBy('order', 'ESC');
        $query  = $this->builder->getWhere(array('level'=>$level));
        $result =$query->getResult();
        return $result;
    }
    /**
     * 根据id获取菜单权限
     * @return mixed
     */
    function getMenuById($ids = null){
        $this->builder->orderBy('order', 'ESC');
        if($ids == null){
            return null;
        }
        $this->builder->whereIn('id',$ids);
        $query  = $this->builder->get();
        $result =$query->getResult();
        return $result;
    }
	/**
	 * 获取用户列表（分页）
	 * @return array|mixed
	 */
	function getMenus($where = null, $limit = null, $offset = null)
	{
		if ($where != null) {
			$this->builder->like($where);
		}
		$this->builder->orderBy('level','ESC');
		$query = $this->builder->getWhere(null, $offset, $limit);
		$result = $query->getResult();
		return $result;
	}


}
