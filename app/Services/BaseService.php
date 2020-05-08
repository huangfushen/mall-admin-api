<?php
/**
 * 公共数据库操作方法
 * Created by PhpStorm.
 * User: huangfs
 * Date: 2020/5/4
 * Time: 19:05
 */

namespace App\Services;

use Config\Database;

class BaseService
{
	protected $builder;
	protected $table;
	public $db;

	function __construct()
	{
		$this->db = Database::connect();
		$this->builder = $this->db->table($this->table);
	}

	/**
	 * 增
	 * @param $options
	 * @return object|resource
	 */
	public function insert($options)
	{
		$this->builder->insert($options);
		return $this->db->insertID();
	}

	/**
	 * 删
	 * @param $where
	 * @return int|mixed
	 */
	public function delete($where)
	{
		return $this->builder->delete($where);
	}

	/**
	 * 改
	 * @param $condition
	 * @param $where
	 * @return bool
	 */
	public function update($condition = null, $where)
	{
		$this->builder->where($where);
		$query = $this->builder->update($condition);
		return $query;
	}

	/**
	 * 查
	 * @param $where
	 * @return mixed
	 */
	public function get($where = null)
	{
		$query = $this->builder->getWhere($where);
		return $query->getResult();
	}

}
