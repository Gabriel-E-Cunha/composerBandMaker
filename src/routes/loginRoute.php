<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->get('/login/[{action}]', function (Request $request, Response $response, array $args) use ($container) {
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
        $resultSet = $conexao->query('SELECT * FROM dado_login WHERE nome_usuario = "' . $params['user'] . '" AND senha = "' . md5($params['senha']) . '"')->fetchAll();

        if($resultSet != null) {
            if($resultSet[0]['pessoa_id'] != null) {
                $_SESSION['banda'] = false;
                $_SESSION['loginID'] = $resultSet[0]['pessoa_id'];
            } else {
                $_SESSION['banda'] = true;
                $_SESSION['loginID'] = $resultSet[0]['banda_id'];
            }
            return $response->withRedirect('/');
        } else if($params['user'] == null || $params['senha'] == null){
            return $response->withRedirect('/login/blank-fields');
        } else {
            return $response->withRedirect('/login/user-not-found');
        }


        // Render index view
        return $container->get('renderer')->render($response, 'login.phtml', $args);
    });
};
