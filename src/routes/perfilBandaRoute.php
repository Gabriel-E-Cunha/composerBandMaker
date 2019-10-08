<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->get('/perfilBanda/', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/perfilBanda/' route");
        // Render index view
        return $container->get('renderer')->render($response, 'perfilBanda.phtml', $args);
    });

  
};
