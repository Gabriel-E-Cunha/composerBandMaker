<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->get('/eventos/[{id}]', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/eventos/' route");
        
        $conexao = $container->get('pdo');
        if (!isset($args['id'])) {
            $resultSet = "";
        } else {
            $resultSet = $conexao->query('SELECT * FROM perfil_normal WHERE id = ' . $args['id'])->fetchAll();
        }

        $args['perfil_normal'] = $resultSet;

        // Render index view
        return $container->get('renderer')->render($response, 'eventos.phtml', $args);
    });
};
