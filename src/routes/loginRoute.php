<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();
    $app->get('/login/', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/login/' route");
        // Render index view
        return $container->get('renderer')->render($response, 'login.phtml', $args);
    });
    $app->post('/login/', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/login/' route");
        $conexao = $container->get('pdo');
        $params = $request->getParsedBody();
        $resultSet = $conexao->query('SELECT * FROM perfil_normal
         WHERE email = "' . $params['email'] . '"
          AND senha = "' . md5($params['senha']) . '"')->fetchAll();

        if (($resultSet) == 1) {
            return $response->withRedirect('/' . $resultSet[0]['id']);
        } else {
            return $response->withRedirect('/login/');
            exit;
        }
        // Render index view
        return $container->get('renderer')->render($response, 'login.phtml', $args);
    });
};
