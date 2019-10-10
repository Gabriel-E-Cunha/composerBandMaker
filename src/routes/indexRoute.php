<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->get('/[{action}]', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/' route");

        if($args['action'] == "logout") {
            session_destroy();
            return $response->withRedirect('/');
        }

        $conexao = $container->get('pdo');
        
        if (isset($_SESSION['loginID'])) {
            $resultSet = $conexao->query('SELECT * FROM perfil_normal WHERE id = ' . $_SESSION['loginID'])->fetchAll(); 
        } else {
            $resultSet = "";
        }

        $args['perfil_normal'] = $resultSet;
        // Render index view
        return $container->get('renderer')->render($response, 'index.phtml', $args);
    });
};
