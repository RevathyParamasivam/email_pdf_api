<?php
namespace Config;

use CodeIgniter\Config\BaseConfig;

class AppConstant extends BaseConfig
{

    public $password_encrept_key   = 'abcdefghtjklmnopqrstuvwxyz1234567890';
    public $jwt_key                = 'ddfgdjgjiodniduo';
    public $expireTimeInteravel    = 12 * 3600; // 4h
    public $paginationPerPage      = 10;
    public $staffProfileUploadPath = 'uploads/staff/profile';
    public $staffFamilyUploadPath  = 'uploads/staff/family';
    public $staffSupportUploadPath = 'uploads/staff/support';
    public $staffEduDocument       = 'uploads/staff/document/education/';
    public $staffTrainingDocument  = 'uploads/staff/document/training';
    public $staffExpDocument       = 'uploads/staff/document/experience';
    public $uploadPath             = 'uploads/';
    public $serverPathUpload;
    public $TEM_PATH            = 'uploads/temp/';
    public $TOKEN_REMOTE_ACCESS = 'YMsIVVsthyRIknXFpOnqkxnqqOYxW6nv_duANIEdB_g';
    public $LOG_STATUS          = array('ERROR' => '0', 'INFO' => '1', 'CRITICAL_ERROR' => '4', 'ALERT' => '3', 'WARNING' => '2');
    public $ERROR_LOG_ENABLE    = true;
    public $INFO_LOG_ENABLE     = true;
    public $WARNING_LOG_ENABLE  = true;
    public $TOKEN_APP_SERVER    = '283fe2d3d1a409b2e2b8d0098caf20dc344fa595185c437e0414648b3493aebdd23d7c27b02be4617deae1f41e4add01877bfb6dc359bcd11184256d04968f8e';
    public $APP_SERVER_API_URL  = 'https://followwork.ddns.net/api/v1/';

    

}
