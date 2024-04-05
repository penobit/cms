<?php

use App\Collection;
use App\Entities\User;
use Core\Routes\Router;
use Database\QueryBuilder;

Router::get('/', function() {
    return new Collection([
        'page' => 'home',
        'path' => '/',
    ]);
})->name('home');

Router::get('/user', function(QueryBuilder $db) {
    $user = $db->table('users')->where('name', '!=', 'R8')->get();

    return response()->json($user);
})->name('page');

Router::get('/profile/{company}/{user}', function(User $myUser, $company, $user) {
    return response()->json([$company, $user, $myUser]);
})->name('page-2');
