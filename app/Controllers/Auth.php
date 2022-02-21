<?php
namespace App\Controllers;

use App\Domain\Exception\RecordNotFoundException;
use App\Domain\User\UserLoginRepository;
use App\Domain\User\UserRepository;
use App\Infrastructure\Persistence\Role\SQLUser_roleRepository;
use App\Infrastructure\Persistence\Staff\SQLStaffRepository;
use Config\Services;

class Auth extends BaseController
{

    private $repository;
    private $role_repository;
    private $login_repository;
    private $staff_repository;  
    public function __construct()
    {
        $this->initializeFunction();
        $this->repository       = Services::UserRepository();
        $this->login_repository = Services::UserLoginRepository();
        //$this->role_repository  = new SQLUser_roleRepository();
        //$this->staff_repository = new SQLStaffRepository();
    }

    public function index()
    {
        $this->login();
    }

    public function login()
    {

        if (strtolower($this->reqMethod) == 'post') {
            $data     = $this->getDataFromUrl('json');
            $password = isset($data['password']) ? $data['password'] : null;
            $username = isset($data['username']) ? $data['username'] : null;
            if (!$password || !$username) {
                return $this->message(500, null, 'Username and Password are Required');
            }
            try
            {
                $userData = $this->login_repository->loginCheck($username, md5($password));
                //return $this->message(200, $userData);
            } catch (RecordNotFoundException $e) {
                return $this->message(404, $e->getMessage());
            }
            /*
            if (!empty($userData)) {
                $roleData  = $this->role_repository->findPermissionByUser($userData['user_id']);
                $tokenData = array();
                if (isset($userData['staff_id']) && !empty($userData['staff_emp_code'])) {
                    $userData['staff'] = $this->staff_repository->findBasic($userData['staff_emp_code']);
                }
                $userData['role'] = $roleData;
                //later implenet by ip for more security
                //$tokenData['email_id']     = $userData['email_id'];
                $tokenData['user_id']        = $userData['user_id'];
                $tokenData['user_name']      = $userData['user_name'];
                $tokenData['mobile_no']      = $userData['mobile_no'];
                $tokenData['profile_id'] = $userData['profile_id'];
                $tokenData                   = $this->createToken($tokenData);
                $userData['token']           = $tokenData;
                return $this->message(200, $userData);
            } else {
                return $this->message(500, null, 'Bad Credentials');
            }
            return $this->message(500, null, 'Invalid User');

        } */}else {
            return $this->message(400, null, 'Method Not Allowed');
        }

        //if(lc($this->reqMethod) == 'get' ||  )
    }

    public function update($id)
    {
        return $this->respond([$id]);
    }

    public function delete($id)
    {
        return $this->respond($id);
    }
}
