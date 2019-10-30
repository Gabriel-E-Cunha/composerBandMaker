<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->get('/perfil-banda/[{action}]', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/perfil-banda/' route");

        $conexao = $container->get('pdo');

        $resultSet = $conexao->query('SELECT * FROM perfil_banda WHERE nome_usuario = "' . $args['action'] . '"')->fetchAll();
        $args['perfil'] = $resultSet;

        $resultSet = $conexao->query('SELECT * FROM perfil_pessoa WHERE banda_id = ' . $resultSet[0]['id'])->fetchAll();
        $args['integrante']  = $resultSet;

        // Render index view
        return $container->get('renderer')->render($response, 'perfilBanda.phtml', $args);
    });

    $app->post('/perfil-banda/[{action}]', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/perfil-banda/' route");

        $conexao = $container->get('pdo');

        $resultSet = $conexao->query('SELECT * FROM perfil_banda WHERE nome_usuario = "' . $args['action'] . '"')->fetchAll();
        $args['perfil'] = $resultSet;

        return $response->withRedirect('/perfil-banda/'.$resultSet[0]['nome_usuario']);       
    });
};
