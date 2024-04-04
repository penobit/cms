<?php

// composer autoload
require HOME.'/vendor/autoload.php';

use App\Application;
use App\ServiceContainer;
use Core\Routes\Router;

// Register service container
$serviceContainer = new ServiceContainer();
$serviceContainer->register();

// Register a new application
$app = new Application(
    router: new Router(),
    serviceContainer: $serviceContainer,
);

// Check the database connection and if the application is installed
// $app->checkDatabaseConnection()->checkInstallation();

return $app;