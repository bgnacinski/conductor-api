<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->view("/", "addTicketer");

$routes->group("api", function($routes){
    $routes->group("gates", ["filter" => "gate-auth"], function($routes){ # dedicated group for gates
        $routes->get("bans", "Api\Card::index/true");
        $routes->get("pubkey", "Api\Card::ca_pubkey");
        $routes->post("log", "Api\Log::store");
    });

    $routes->group("cards", function($routes){
        // admin only access - requires better filtering
        $routes->get("/", "Api\Card::index");
        $routes->get("/(:segment)", "Api\Card::show/$1");
        # ...
        $routes->post("issue", "Api\Card::issue");
    });

    $routes->group("logs", function($routes){
        $routes->post("store", "Api\Log::store");
    });
});