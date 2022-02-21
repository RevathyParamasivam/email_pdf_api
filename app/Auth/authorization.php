<?php
use CodeIgniter\HTTP\RequestInterface;

require_once APPPATH . 'Auth/jwt.php';

class AUTHORIZATION
{

    static $request;
    static $response;

    public function __construct(RequestInterface $request)
    {
        self::$request  = \Config\Services::request();
        self::$response = \Config\Services::response();
    }

    public static function validateTimestamp($token)
    {
        //foreach ($token as $key => $val) {
        //  if (property_exists(__CLASS__, $key)) {
        //    $this->$key = $val;
        //}
        if ($token && (time() < $token->expire)) {
            return $token;
        }
        return false;
        //}
    }

    public static function validateToken($token)
    {
        $authConfig = new \Config\AppConstant();
        if ($authConfig->TOKEN_REMOTE_ACCESS === $token) {
            return true;
        }
        $token = self::decodeToken($token);
        if (isset($token->user_id) && isset($token->expire)) {
            return self::validateTimestamp($token);
        } else {
            return false;
        }

    }

    public static function decodeToken($token)
    {
        $authConfig = new \Config\AppConstant();
        return JWT::decode($token, $authConfig->jwt_key);
    }

    public static function generateToken($data)
    {

        $authConfig = new \Config\AppConstant();
        return JWT::encode($data, $authConfig->jwt_key);
    }

    public static function checkAuthorized()
    {
        $request = \Config\Services::request();
        $headers = $request->getHeaders();
        //print_r($headers);
        if (array_key_exists('Authorization', $headers) && !empty($headers['Authorization'])) {
            list($jwt) = sscanf($headers['Authorization'], 'Authorization: Bearer %s');
            return self::validateToken($jwt);
        } else {
            return false;
        }

    }

}
