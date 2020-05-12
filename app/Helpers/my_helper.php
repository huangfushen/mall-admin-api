<?php
/**
 * Created by PhpStorm.
 * User: huangfs
 * Date: 2020/4/22
 * Time: 10:25
 */
use CodeIgniter\HTTP\RequestInterface;

/**
 * 将请求的参数进行获取并返回数组
 * @param array $options
 * @param string $prefix
 * @return mixed
 */
    function my_fill_options ($options = array(), $prefix = '')
    {
        $request = RequestInterface();
        foreach ($options as $key=>$val)
        {
            $options[$key] = trim($request->getGetPost("{$prefix}{$key}"));
        }
        foreach($options as $key => $value) {
            if(!empty($value)){
                $pramas[$key] = $value;
            }
        }
        return $pramas;
    }

/**
 * 密码加盐
 * @param $pass
 * @param $salt
 * @return string
 */
    function salt_pass($pass=null,$salt=null){
        return md5($pass.$salt);
    }

/**
 * 随机字符串
 * @param int $length
 * @param string $char
 * @return bool|string
 */
    function str_rand($length = 32, $char = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
        if(!is_int($length) || $length < 0) {
            return false;
        }
        $string = '';
        for($i = $length; $i > 0; $i--) {
        $string .= $char[mt_rand(0, strlen($char) - 1)];
            }
        return $string;
    }

/**
 * 分页total 参数设置
 * @param $arr
 */
    function unsetPage($arr){
		unset($arr['limit']);
		unset($arr['offset']);
		return $arr;
	}
