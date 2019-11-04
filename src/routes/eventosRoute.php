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
        
        //Caso o usuário tenha saído da tela de cadastro, apaga todos os inputs salvos.
        unset($_SESSION['bandValues']);
        unset($_SESSION['personValues']);

            if($_SESSION['banda']) {
                $args['banda'] = true;
                $resultSet = $conexao->query('SELECT * FROM perfil_banda WHERE id = ' . $_SESSION['loginID'])->fetchAll();
                $args['eventos'] = $conexao->query('SELECT * FROM evento WHERE banda_id = ' . $resultSet[0]['id'])->fetchAll();
                $args['nome_banda'] = $resultSet[0]['nome_usuario'];
            } else {
                $args['banda'] = false;
                $resultSet = $conexao->query('SELECT * FROM perfil_pessoa WHERE id = ' . $_SESSION['loginID'])->fetchAll();
                $args['eventos'] = $conexao->query('SELECT * FROM evento WHERE banda_id = ' . $resultSet[0]['banda_id'])->fetchAll(); 
                $resultNomeBanda = $conexao->query('SELECT nome_usuario FROM perfil_banda WHERE id = ' . $resultSet[0]['banda_id'])->fetchAll();
                $args['nome_banda'] = $resultNomeBanda[0]['nome_usuario'];
            }
            $args['perfil'] = $resultSet;
            

        // Render index view
        return $container->get('renderer')->render($response, 'eventos.phtml', $args);
    });


};
