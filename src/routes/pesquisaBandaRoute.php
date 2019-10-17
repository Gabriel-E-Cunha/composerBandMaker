<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->get('/pesquisaBanda/', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/pesquisaBanda/' route");


        if (isset($_GET['busca'])) {

            $conexao = $container->get('pdo');

            $resultSet = $conexao->query('SELECT * FROM perfil_banda WHERE nome_usuario LIKE "%' . $_GET['busca'] . '%"')->fetchAll();


            $args['resultado'] = $resultSet;
        }

        // Render index view
        return $container->get('renderer')->render($response, 'buscarBanda.phtml', $args);
    });
};
