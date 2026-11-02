<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->view("/", "addTicketer");

$routes->group("api", ["filter" => "auth"], function($routes){
    $routes->post("log", "Api\Log::store");
});