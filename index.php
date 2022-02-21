<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
// Valid PHP Version?
date_default_timezone_set('Asia/Kolkata');
$minPHPVersion = '7.2';
if (phpversion() < $minPHPVersion) {
    die("Your PHP version must be {$minPHPVersion} or higher to run CodeIgniter. Current version: " . phpversion());
}
unset($minPHPVersion);
// Path to the front controller (this file)
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
//print_r($_REQUEST['REQUEST_METHOD']) . 'method';
//return;
// Location of the Paths config file.
// This is the line that might need to be changed, depending on your folder structure.

$pathsPath = FCPATH . 'app/Config/Paths.php';
// ^^^ Change this if you move your application folder
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    die();
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, PATCH");
    }

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    }

}
$isHttps =
    (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
    || (isset($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] === 'https')
    || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
;
$protocol = $isHttps ? "https://" : "http://";
$base     = $protocol . $_SERVER['HTTP_HOST'];
$base .= str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);

define("ENVIRONMENT", "testing");
define('BASEURL', $base);
switch (ENVIRONMENT) {
    case 'development':
         define('DB_USERNAME', 'root');
         define('DB_PASSWORD', '');
         define('DB_DATABASE', 'sendemaildata');
         define('DB_HOST', 'localhost');
        //define('DB_USERNAME', 'ssamtorg_revathy');
        //define('DB_PASSWORD', 'revathy@123');
        //define('DB_DATABASE', 'ssamtorg_emailparse');
        //define('DB_HOST', 'localhost');
        break;

   case 'testing':
        define('DB_USERNAME', 'ssamtorg_revathy');
        define('DB_PASSWORD', 'revathy@123');
        define('DB_DATABASE', 'ssamtorg_emailparse');
        define('DB_HOST', 'localhost');
        break;

case 'production':
        define('DB_USERNAME', '');
        define('DB_PASSWORD', '');
        define('DB_DATABASE', 'fw_admin');
        define('DB_HOST', 'localhost');
        break;

    default:
        define('DB_USERNAME', 'root');
        define('DB_PASSWORD', '');
        define('DB_DATABASE', 'fw_admin');
        define('DB_HOST', 'localhost');
        break;
}

/*
 *---------------------------------------------------------------
 * BOOTSTRAP THE APPLICATION
 *---------------------------------------------------------------
 * This process sets up the path constants, loads and registers
 * our autoloader, along with Composer's, loads our constants
 * and fires up an environment-specific bootstrapping.
 */
// Ensure the current directory is pointing to the front controller's directory
chdir(__DIR__);
// Load our paths config file
require $pathsPath;
$paths = new Config\Paths();

// Location of the framework bootstrap file.
$app = require rtrim($paths->systemDirectory, '/ ') . '/bootstrap.php';
/*
 *---------------------------------------------------------------
 * LAUNCH THE APPLICATION
 *---------------------------------------------------------------
 * Now that everything is setup, it's time to actually fire
 * up the engines and make this app do its thang.
 */
$app->run();
