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

/** @var Application $app */
$app = require HOME.'/core/app/bootstrap.php';

$app->run();