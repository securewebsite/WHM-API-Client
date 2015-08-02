<?php

// Define class autoloader.
spl_autoload_register(function ($class) {
    $filename = '../src/' . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    if (file_exists($filename)) require($filename);
});

// Define the testing class.
function testWhmObjects(\RCrowt\WHM\ApiClient\CPanelObject $object, $prefixes = ['is', 'get', 'jsonSerialize'])
{
    $out = [];
    $reflection = new ReflectionObject($object);

    foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {

        // Skip Methods with Parameters
        if ($method->getNumberOfRequiredParameters() > 0) continue;

        // Call methods with defined prefixes.
        foreach ($prefixes as $p) {
            if (substr($method->name, 0, strlen($p)) == $p) {
                $out[$method->name] = call_user_func([$object, $method->name]);
            }
        }
    }

    return $out;
}

// Connect to the API and define the login credentials.
$config = parse_ini_file('config.ini');
$api = new \RCrowt\WHM\ApiClient($config['username'], $config['access_hash'], $config['host']);

$debug = [];

// Test account listings.
$debug['getAccountList'] = [];
foreach ($api->getAccountList() as $act)
    $debug['getAccountList'][] = testWhmObjects($act);

// Test package listings
$debug['getPackageList'] = [];
foreach ($api->getPackageList() as $pack)
    $debug['getPackageList'][] = testWhmObjects($pack);

// Output Results
exit(json_encode($debug));