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

        
        $conexao->query('INSERT INTO perfil_banda (nome,cidade,
        cep,estado,email,influencias,descricao) 
        VALUES("' . $params['nome'] . '", "' . $params['cidade'] . '",
         "' . $params['cep'] . '", "' . $params['estado'] . '",
         "' . $params['email'] . '","' . $params['influencias'] . '",
         "' . $params['descricao'] . '")');

        $resultSet = $conexao->query('SELECT * FROM perfil_banda
        WHERE email = "' . $params['email'] . '"')->fetchAll();

        if ($conexao != null) {
            return $response->withRedirect('/' . $resultSet[0]['id']);
        } else {
            return $response->withRedirect('/criarBanda/');
            exit;
        }


        // Render index view
        return $container->get('renderer')->render($response, 'perfilBanda.phtml', $args);
    });
};
