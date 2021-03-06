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

	/**
	 * @param $options[select,like,where,orderBy,order,offset,limit]
	 * @return array|mixed
	 */
	function getComplete($options)
	{
		if(isset($options['select'])){
			$this->builder->select($options['select']);
		}
		if (isset($options['like'])) {
			$this->builder->like($options['like']);
		}
		if (isset($options['where'])) {
			$this->builder->where($options['where']);
		}
		if(isset($options['orderBy']) && isset($options['order'])){
			$this->builder->orderBy($options['orderBy'],$options['order']);
		}
		if(isset($options['offset']) && isset($options['limit'])){
			$this->builder->limit( $options['offset'], $options['limit']);
		}
		$query = $this->builder->get();
		$result = $query->getResult();
		return $result;
	}

}
