<?php

use Core\Routes\Router;
use Database\QueryBuilder;

Router::get('/', function() {
    echo 'Home';
})->name('home');

Router::get('/method', function(QueryBuilder $db) {
    // $db = new QueryBuilder();

    echo $db->table('users')->where('name', '!=', 'R8')->delete();

    exit;
})->name('page');

Router::get('/profile/{company}/{user}', function() {
    echo 'Page with variable';
})->name('page-2');
