<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->get('/perfil-banda/', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/perfil-banda/' route");

        $conexao = $container->get('pdo');

        $resultSet = $conexao->query('SELECT * FROM perfil_banda WHERE id = ' . $_SESSION['loginID'])->fetchAll();
        $args['perfil'] = $resultSet;
        

        // Render index view
        return $container->get('renderer')->render($response, 'perfilBanda.phtml', $args);
    });

  
};
