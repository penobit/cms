<?php

use App\Application;

/*
 * Penobit CMS
 * Author: R8
 * Author URL: https://penobit.com
 * License: MIT
 * License URL: https://opensource.org/licenses/MIT
 * Version: 0.1.
 */

define('PENOBIT', true);
define('HOME', dirname(__FILE__));
define('DB_PATH', HOME.'/database');
define('CONFIG_PATH', HOME.'/configs');
define('CONTENT_PATH', HOME.'/content');
define('THEMES_PATH', CONTENT_PATH.'/themes');
define('APP_PATH', HOME.'/core/app');
define('STORAGE_PATH', HOME.'/core/app/storage');
define('LOGS_PATH', HOME.'/core/app/storage/logs');
define('CMS_TEMPLATE_PATH', STORAGE_PATH.'/templates');
define('CACHE_PATH', STORAGE_PATH.'/cache');
define('TEMPLATE_CACHE_PATH', CACHE_PATH.'/templates');

/** @var Application $app */
$app = require HOME.'/core/app/bootstrap.php';

$app->run();