<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->get('/[{action}]', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/' route");

        if(isset($args['action']) && $args['action'] == "logout") {
            session_destroy();
            return $response->withRedirect('/');
        }

        $conexao = $container->get('pdo');
        
        if (isset($_SESSION['loginID'])) {
            if($_SESSION['banda']) {
                $args['banda'] = true;
                $resultSet = $conexao->query('SELECT * FROM perfil_banda WHERE id = ' . $_SESSION['loginID'])->fetchAll();
                $notificacoes = $conexao->query('SELECT COUNT(id) FROM notificacao WHERE banda_id = '.$_SESSION['loginID'])->fetchAll();
                $args['notificacao'] = $notificacoes[0]['COUNT(id)'];
            } else {
                $args['banda'] = false;
                $resultSet = $conexao->query('SELECT * FROM perfil_pessoa WHERE id = ' . $_SESSION['loginID'])->fetchAll(); 
                $notificacoes = $conexao->query('SELECT COUNT(id) FROM notificacao WHERE pessoa_id = '.$_SESSION['loginID'])->fetchAll();
                $args['notificacao'] = $notificacoes[0]['COUNT(id)'];
            }
            $args['perfil'] = $resultSet;
        }

        
        // Render index view
        return $container->get('renderer')->render($response, 'index.phtml', $args);
    });
};
