<?php
namespace App\Controllers;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
use Gregwar\Captcha\CaptchaBuilder;
class Home extends BaseController
{

    public function test(){
        $builder = new CaptchaBuilder;
        $builder->build();
        $builder->save('images/test.jpg');
        header('Content-type: text/json');
        $builder->output();
    }
	public function index()
    {
        $time = time();
        $token = (new Builder())->issuedBy('http://example.com') // Configures the issuer (iss claim)签发地址
        ->permittedFor('http://example.org') // Configures the audience (aud claim)签发人
        ->identifiedBy('4f1g23a12aa', true) // Configures the id (jti claim), replicating as a header item  id
        ->issuedAt($time) // Configures the time that the token was issue (iat claim) 签发时间
        ->canOnlyBeUsedAfter($time + 60) // Configures the time that the token can be used (nbf claim)生效时间
        ->expiresAt($time + 3600) // Configures the expiration time of the token (exp claim)过期时间
        ->withClaim('uid', 1) // Configures a new claim, called "uid"
        ->getToken(); // Retrieves the generated token


        $token->getHeaders(); // Retrieves the token headers
        $token->getClaims(); // Retrieves the token claims

        echo $token->getHeader('jti').'<br/>'; // will print "4f1g23a12aa"
        echo $token->getClaim('iss').'<br/>'; // will print "http://example.com"
        echo $token->getClaim('uid').'<br/>'; // will print "1"
        echo $token; // The string representation of the object is a JWT string (pretty easy, right?)

        echo "---------------------------------------------------------<br/>";

        $token = (new Parser())->parse((string) $token); // Parses from a string
        $token->getHeaders(); // Retrieves the token header
        $token->getClaims(); // Retrieves the token claims

        echo $token->getHeader('jti').'<br/>'; // will print "4f1g23a12aa"
        echo $token->getClaim('iss').'<br/>'; // will print "http://example.com"
        echo $token->getClaim('uid').'<br/>'; // will print "1"
        echo "---------------------------------------------------------<br/>";

        $data = new ValidationData(); // It will use the current time to validate (iat, nbf and exp)
        $data->setIssuer('http://example.com');
        $data->setAudience('http://example.org');
       // $data->setId('4f1g23a12aa');
        $data->setCurrentTime($time);
//        var_dump(date('h:i:s',$time));
        var_dump($token->validate($data)); // false, because token cannot be used before now() + 60

        $data->setCurrentTime($time + 61); // changing the validation time to future

        var_dump($token->validate($data)); // true, because current time is between "nbf" and "exp" claims

        $data->setCurrentTime($time + 4000); // changing the validation time to future

        var_dump($token->validate($data)); // false, because token is expired since current time is greater than exp

// We can also use the $leeway parameter to deal with clock skew (see notes below)
// If token's claimed time is invalid but the difference between that and the validation time is less than $leeway,
// then token is still considered valid
        $dataWithLeeway = new ValidationData($time, 20);
        $dataWithLeeway->setIssuer('http://example.com');
        $dataWithLeeway->setAudience('http://example.org');
        $dataWithLeeway->setId('4f1g23a12aa');

        var_dump($token->validate($dataWithLeeway)); // false, because token can't be used before now() + 60, not within leeway

        $dataWithLeeway->setCurrentTime($time + 51); // changing the validation time to future

        var_dump($token->validate($dataWithLeeway)); // true, because current time plus leeway is between "nbf" and "exp" claims

        $dataWithLeeway->setCurrentTime($time + 3610); // changing the validation time to future but within leeway

        var_dump($token->validate($dataWithLeeway)); // true, because current time - 20 seconds leeway is less than exp

        $dataWithLeeway->setCurrentTime($time + 4000); // changing the validation time to future outside of leeway

        var_dump($token->validate($dataWithLeeway)); // false, because token is expired since current time is greater than exp


    }

}
