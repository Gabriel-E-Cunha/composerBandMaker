<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->get('/vagas/', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/vagas/' route");

        $conexao = $container->get('pdo');

        $resultSet0 = $conexao->query('SELECT genero FROM perfil_banda INNER JOIN
        vaga WHERE vaga.banda_id = perfil_banda.id')->fetchAll();
        $args['genero'] = $resultSet0;

        $resultSet00 = $conexao->query('SELECT vaga FROM vaga')->fetchAll();
        $args['vaga'] = $resultSet00;

        if (isset($_GET['busca'])) {
            $resultSet01 = $conexao->query('SELECT * FROM perfil_banda INNER JOIN
            vaga WHERE vaga.banda_id = perfil_banda.id AND
            perfil_banda.nome_usuario LIKE "%' . $_GET['busca'] . '%"')->fetchAll();
            $args['resultado'] = $resultSet01;
        }
        if (isset($_GET['genero']) && $_GET['genero'] != "Gênero") {
            $resultSet01 = $conexao->query('SELECT * FROM perfil_banda INNER JOIN
            vaga WHERE vaga.banda_id = perfil_banda.id AND perfil_banda.genero ="' . $_GET['genero'] . '"')->fetchAll();
            $args['resultado'] = $resultSet01;
        }
        if (isset($_GET['vaga']) && $_GET['vaga'] != "Vaga") {
            $resultSet01 = $conexao->query('SELECT * FROM perfil_banda INNER JOIN
            vaga WHERE vaga.banda_id = perfil_banda.id AND vaga.vaga = "' . $_GET['vaga'] . '"')->fetchAll();
            $args['resultado'] = $resultSet01;
        }

        // Render index view
        return $container->get('renderer')->render($response, 'vagas.phtml', $args);
    });
};
