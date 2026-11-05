<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->view("/", "addTicketer");

$routes->group("api", ["filter" => "auth"], function($routes){
    $routes->get("pubkey", "Api\Pubkey::__invoke");
    $routes->get("bans", "Api\Bans::index");

    $routes->post("log", "Api\Log::store");
});