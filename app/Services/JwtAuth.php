<?php
/**
 * Created by PhpStorm.
 * User: huangfs
 * Date: 2020/4/21
 * Time: 17:05
 */

namespace App\Services;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\ValidationData;
class JwtAuth{
    const EXP = 60 * 60 * 12;//token有效时间
    private $token;
    /**
     * @var 用户传递的decode token
     */
    private $decodeToken;
    private static $_instance;
    private $iss = "test.admin.com"; //签发人
    private $aud = "test";          //接受人
    private $uid;                   //用户id
    private $key = "huang";         //密匙
    private $exp;                   //token失效时间
    private $id = 'huangfs';                    //token id标识

    /**
     * JwtAuth constructor.
     * 私有化construct方法
     */
    private function __construct()
    {
    }

    /**
     * 私有化clone方法
     */
    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    /**
     *  获取jwtauth类实例化对象
     */
    public static function getInstance(){
        if(!(self::$_instance instanceof JwtAuth) ){
            self::$_instance = new JwtAuth();
        }
        return self::$_instance;
    }

    /**
     * 获取token
     * @return string
     */
    public function getToken(){
        return (string)$this->token;
    }

    /**
     * 设置token
     * @param $token
     * @return $this
     */
    public function setToken($token){
        $this->token = $token;
        return $this;
    }

    /**
     * 设置uid
     * @param $uid
     * @return $this
     */
    public function setUid($uid){
        $this->uid = $uid;
        return $this;
    }

    /**
     * 获取uid
     * @return mixed
     */
    public function getUid(){
        return $this->uid;
    }

    /**
     * 获取exp
     * @return mixed
     */
    public function getExp(){
        return $this->exp;
    }

    /**
     * 编码生成jwt token
     */
    public function encode(){
        $signer = new Sha256();
        $time = time();
        $this->token = (new Builder())->issuedBy($this->iss) // 签发人
        ->permittedFor($this->aud) // 接受方
        ->identifiedBy($this->id, true) //id标识
        ->issuedAt($time) // 签发时间
        ->canOnlyBeUsedAfter($time-1) // 生效时间
        ->expiresAt($time + self::EXP) // 过期时间
        ->withClaim('uid', $this->uid) //用户id
        ->getToken($signer,new Key($this->key)); // 生成token ，sha256加密
        return $this;
    }

    /**
     * 解析传递的token
     * @return 用户传递的decode
     */
    public function decode(){
        if(!$this->decodeToken){
            try {
                $this->decodeToken = (new Parser())->parse((string) $this->token);
                $this->uid = $this->decodeToken->getClaim('uid');
                $this->exp = $this->decodeToken->getClaim('exp');
            } catch (\RuntimeException $e) {
                echo json_encode([
                    'rcode' => VERIFY_FAIL,
                    'message' => 'Parameter error'
                ]);
                exit();
            }catch (\InvalidArgumentException $i) {
                echo json_encode([
                    'rcode' => VERIFY_FAIL,
                    'message' => 'Parameter error'
                ]);
                exit();
            }

        }
        return $this->decodeToken;
    }

    /**
     * verify校验token signature串第三个字符串
     * @return mixed
     */
    public function verify(){
        $result = $this->decode()->verify(new Sha256(),$this->key);
        return $result;
    }

    /**
     * 校验传递的token是否有效,校验前两个字符串
     * @return mixed
     */
    public function validate(){
        $data = new ValidationData();
        $data->setAudience($this->aud);
        $data->setIssuer($this->iss);
        $data->setId($this->id);
        return $this->decode()->validate($data);
    }

}
