<?php

use App\Entities\User;
use Core\Routes\Router;
use Database\QueryBuilder;

Router::get('/', function() {
    echo 'Home';
})->name('home');

Router::get('/method', function(QueryBuilder $db) {
    echo $db->table('users')->where('name', '!=', 'R8')->get();

    exit;
})->name('page');

Router::get('/profile/{company}/{user}', function(User $myUser, $company, $user) {
    echo 'Page with variable';
    dd($company, $user, $myUser);
})->name('page-2');
