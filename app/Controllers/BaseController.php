<?php
namespace App\Controllers;

require_once APPPATH . 'Auth/jwt.php';
require_once APPPATH . 'Auth/authorization.php';

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 * class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 *
 * @package CodeIgniter
 */

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Controller;

//require_once(APPPATH.'Helpers/jwt_helper.php');

class BaseController extends Controller
{

    use ResponseTrait;

    private $timestamp;
    private $email;
    private $user_id;
    protected $reqMethod;
    protected $userData;
/**
 * An array of helpers to be loaded automatically upon
 * class instantiation. These helpers will be available
 * to all other controllers that extend BaseController.
 *
 * @var array
 */

    protected $helpers = ['Utility', 'validation', 'form', 'url'];

/**
 * Constructor.
 */
    public $AuthObj;
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);
        $AuthObj = new \AUTHORIZATION(\Config\Services::request());
        // parent::getReqMethod();
        //helper([]);
        //--------------------------------------------------------------------
        // Preload any models, libraries, etc, here.
        //--------------------------------------------------------------------
        // E.g.:
        // $this->session = \Config\Services::session();
    }

    public function initializeFunction()
    {
        $this->getReqMethod();
        $this->getTokenData();
    }

    public function getReqMethod()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            $this->reqMethod = strtolower($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']);
            return strtolower($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']);
        } else {
            $this->reqMethod = strtolower($_SERVER['REQUEST_METHOD']);
            return strtolower($_SERVER['REQUEST_METHOD']);
        }

    }
    public function getTokenData()
    {
        $this->userData = \AUTHORIZATION::checkAuthorized();
    }

    public function message($status = 200, $data = null, $message = "")
    {
        $message = array(
            'statusCode' => $status,
            'message'    => $message,
            'result'     => $data);
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            return $this->response->setStatusCode(200)->setJSON($message);
        } else {
            return $this->response->setStatusCode($status)->setJSON($message);
        }

    }

    public function createToken($tokenData)
    {
        $CI = new \Config\AppConstant();
        //$tokenData['expire']    = time() + ($CI->expireTimeInteravel);
        //$tokenData['expire']    = strtotime('+ 13 hours');
        $tokenData['expire']    = strtotime('+ 1 days');
        $tokenData['create_at'] = time();
        return \AUTHORIZATION::generateToken($tokenData);
    }

    public function getDataFromUrl($methods, $variableName = false)
    {
        $request = \Config\Services::request();
        switch ($methods) {
            case 'json':
                $data = $request->getJSON(true);
                break;
            case 'get':
                $data = $request->getGet();
                break;
            default:
                $data = $request->getVar($variableName);
                break;
        }

        return $data;
    }

}
