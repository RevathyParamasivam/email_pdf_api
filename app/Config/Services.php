<?php namespace Config;

use App\Infrastructure\Persistence\User\SQLUserLoginRepository;
use App\Infrastructure\Persistence\User\SQLUserRepository;
use App\Infrastructure\Persistence\User\SQLOrgRepository;
use App\Infrastructure\Persistence\User\SQLTempOrgRepository;
use App\Models;
use CodeIgniter\Config\Services as CoreServices;

require_once SYSTEMPATH . 'Config/Services.php';
/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 */
class Services extends CoreServices
{

    public static function UserRepository($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('UserRepository');
        }

        return new SQLUserRepository(model(Models\UserModel::class));
    }

    public static function UserLoginRepository($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('UserLoginRepository');
        }
        return new SQLUserLoginRepository(model(Models\UserLoginModel::class));
    }
    public static function OrgRepository($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('OrgRepository');
        }
        return new SQLOrgRepository(model(Models\OrgModel::class));
    }
    public static function TempOrgRepository($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('TempOrgRepository');
        }
        return new SQLTempOrgRepository(model(Models\TempOrgModel::class));
    }
  

}
