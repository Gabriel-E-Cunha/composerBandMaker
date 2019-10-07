<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->get('/criarBanda/', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/criarBanda/' route");
        // Render index view
        return $container->get('renderer')->render($response, 'cadastroBanda.phtml', $args);
    });

    $app->post('/criarBanda/', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/criarBanda/' route");

        $conexao = $container->get('pdo');
        $params = $request->getParsedBody();

        

        $resultSet = $conexao->query("INSERT INTO perfil_banda(nome,cidade,estado,telefone,influencias,descricao,imagem) VALUES()");

        
        // Render index view
        return $container->get('renderer')->render($response, 'perfilBanda.phtml', $args);
    });
};
