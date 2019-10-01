<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->get('/login/', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/login/' route");

        $conexao = $container->get('pdo');

        // Render index view
        return $container->get('renderer')->render($response, 'login.phtml', $args);
    });

    $app->post('/perfil/', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/perfil/' route");

        // $params = $request->getParsedBody();
        // exit;

        // $conexao = $container->get('pdo');

        // $resultSet = $conexao->query('SELECT * FROM perfil_normal WHERE email = ' . $params['email'] . ' and password = ' . $params['senha']);

        // Render index view
            return $container->get('renderer')->render($response, 'perfilNormal.phtml', $args);
       
    });
};
