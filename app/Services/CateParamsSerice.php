<?php


namespace app\Services;

use App\Services\BaseService;

class CateParamsSerice extends BaseService
{
	function __construct()
	{
		$this->table ='cateParams';
		parent::__construct();
	}
}
