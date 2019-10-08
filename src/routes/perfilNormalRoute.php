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
        if(isset($args['id'])) {
            $resultSetPerfil = $conexao->query('SELECT * FROM perfil_normal WHERE id = ' . $args['id'])->fetchAll();
            $resultSetBanda = $conexao->query('SELECT perfil_banda.nome FROM perfil_banda INNER JOIN perfil_normal WHERE perfil_normal.banda_id = perfil_banda.id AND perfil_normal.id = '. $args['id'])->fetchAll();
        }

        $args['perfil'] = $resultSetPerfil;
        $args['banda'] = $resultSetBanda;


        // Render index view
        return $container->get('renderer')->render($response, 'perfilNormal.phtml', $args);
    });
};
