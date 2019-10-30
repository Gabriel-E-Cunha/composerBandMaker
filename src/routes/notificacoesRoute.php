<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->get('/notificacoes/', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/notificacoes/' route");
            $conexao = $container->get('pdo');

            if($_SESSION['banda']) {
                $resultSet = $conexao->query('SELECT titulo, notificacao.descricao FROM notificacao INNER JOIN perfil_banda WHERE perfil_banda.id = '.$_SESSION['loginID'].' AND perfil_banda.id = banda_id')->fetchAll();
            } else {
                $resultSet = $conexao->query('SELECT titulo, descricao FROM notificacao INNER JOIN perfil_pessoa WHERE perfil_pessoa.id = '.$_SESSION['loginID'].' AND perfil_pessoa.id = pessoa_id')->fetchAll();
            }
            $args['notificacoes'] = $resultSet;

        // Render index view
        return $container->get('renderer')->render($response, 'notificacoes.phtml', $args);
    });
};