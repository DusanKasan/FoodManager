<?php

// uncomment this line if you must temporarily take down your site for maintenance
// require '.maintenance.php';

// absolute filesystem path to this web root
define('WWW_DIR', __DIR__);

// absolute filesystem path to the application root
define('APP_DIR', WWW_DIR . '/../app');

// absolute filesystem path to the libraries
define('LIBS_DIR', WWW_DIR . '/../libs');

// absolute filesystem path to the upload directory
define('UPLOAD_DIR', WWW_DIR . '/uploads');

//var_dump(dirname(DIRECTORY_SEPARATOR));
//var_dump(dirname('e:/www/test'));die;

// load bootstrap file
require APP_DIR . '/bootstrap.php';
