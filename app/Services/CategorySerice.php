<?php


namespace app\Services;
use App\Services\BaseService;
class CategorySerice extends BaseService
{
	function __construct()
	{
		$this->table ='categories';
		parent::__construct();
	}


}
