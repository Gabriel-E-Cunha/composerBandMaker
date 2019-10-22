<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->get('/pesquisaBanda/', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/pesquisaBanda/' route");

        $conexao = $container->get('pdo');   

        $resultSet0 = $conexao->query('SELECT genero FROM perfil_banda')->fetchAll();       
        $args['genero'] = $resultSet0;

        if (isset($_GET['busca'])) {                     
            $resultSet01 = $conexao->query('SELECT * FROM perfil_banda WHERE nome_usuario LIKE "%' . $_GET['busca'] . '%"')->fetchAll();
            $args['resultado'] = $resultSet01;            

        }if (isset($_GET['genero']) && $_GET['genero'] != "Gênero") {            
            $resultSet01 = $conexao->query('SELECT * FROM perfil_banda WHERE genero LIKE "%' . $_GET['genero'] . '%"')->fetchAll();
            $args['resultado'] = $resultSet01;  
        }

        // Render index view
        return $container->get('renderer')->render($response, 'buscarBanda.phtml', $args);
    });
};
