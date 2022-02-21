<?php

namespace App\Controllers;
class MigrateController extends \CodeIgniter\Controller
{
        public function index()
        {
                $migrate = \Config\Services::migrations();

                try
                {
                        //$migrate->regress(0);
                        $migrate->latest();
                        echo 'migration successfully';
                }
                catch (\Throwable $e)
                {   
                    print_r($e);
                        // Do something with the error here...
                }
        }
}
?>