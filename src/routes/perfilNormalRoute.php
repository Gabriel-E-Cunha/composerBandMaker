<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();


    $app->get('/perfil/[{id}]', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/perfil/' route");

        $conexao = $container->get('pdo');
        if(isset($_SESSION['loginID'])) {
            $resultSetPerfil = $conexao->query('SELECT * FROM perfil_pessoa WHERE id = ' . $_SESSION['loginID'])->fetchAll();
            $resultSetBanda = $conexao->query('SELECT perfil_banda.nome_usuario FROM perfil_banda INNER JOIN perfil_pessoa WHERE perfil_pessoa.banda_id = perfil_banda.id AND perfil_pessoa.id = '. $_SESSION['loginID'])->fetchAll();
        }

        $args['perfil'] = $resultSetPerfil;
        $args['banda'] = $resultSetBanda;



        // Render index view
        return $container->get('renderer')->render($response, 'perfilNormal.phtml', $args);
    });
};
