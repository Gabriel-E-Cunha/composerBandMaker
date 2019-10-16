<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->get('/eventos/', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/eventos/' route");

        $conexao = $container->get('pdo');
        
            if($_SESSION['banda']) {
                $args['banda'] = true;
                $resultSet = $conexao->query('SELECT * FROM perfil_banda WHERE id = ' . $_SESSION['loginID'])->fetchAll();
                $args['eventos'] = $conexao->query('SELECT * FROM evento WHERE banda_id = ' . $resultSet[0]['id']);
            } else {
                $args['banda'] = false;
                $resultSet = $conexao->query('SELECT * FROM perfil_pessoa WHERE id = ' . $_SESSION['loginID'])->fetchAll();
                $args['eventos'] = $conexao->query('SELECT * FROM evento WHERE banda_id = ' . $resultSet[0]['banda_id']); 
            }
            $args['perfil'] = $resultSet;
            

        // Render index view
        return $container->get('renderer')->render($response, 'eventos.phtml', $args);
    });
};
