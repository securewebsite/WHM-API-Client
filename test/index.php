<?php

// Load Config
$config = parse_ini_file('config.ini');

// Define class autoloader.
spl_autoload_register(function ($class) {
    $filename = '../src/' . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    if (file_exists($filename)) require($filename);
});


$api = new \RCrowt\WHM\ApiClient($config['username'], $config['access_hash'], $config['host']);


foreach ($api->getAccountList() as $act) {
    echo '<pre>';

    foreach (get_class_methods($act) as $method)
        if (substr($method, 0, 1) != '_')
            var_dump([$method, call_user_func_array([$act, $method], [null, null, null])]);

    var_dump($act);

    echo '</pre><hr/>';
}