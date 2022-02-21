<?php
function getIp()
{
    $request = \Config\Services::request();
    return $request->getIPAddress();
}

function getUserAgent($jsonStringfy=false)
{
    $request = \Config\Services::request();
    $agent   = $request->getUserAgent();
    $currentAgent = (object) array();
    if ($agent->isBrowser()) {
        $currentAgent = $agent->getBrowser() . ' ' . $agent->getVersion();
    } else if ($agent->isRobot()) {
        $currentAgent = $agent->robot();
    } else if ($agent->isMobile()) {
        $currentAgent = $agent->getMobile();
    }

    if($jsonStringfy){
        $currentAgent =json_encode($currentAgent);
        //$currentAgent = jsonStringfy($currentAgent);
    }

    return $currentAgent;
}

function getPlatform()
{
    $request = \Config\Services::request();
    $agent   = $request->getUserAgent();
    return $agent->getPlatform(); // Platform info (Windows, Linux, Mac, etc.)
}

function isValidIp($ip) {
	$request = \Config\Services::request();
	return $request->isValidIP($ip);
}
