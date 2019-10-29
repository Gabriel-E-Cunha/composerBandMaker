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

        if ($_SESSION['banda'] == false) {

            $resultSet = $conexao->query('SELECT banda_id FROM vaga INNER JOIN
            perfil_banda WHERE vaga.banda_id = perfil_banda.id')->fetchAll();

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
        }else {            
            $resultSet = $conexao->query('SELECT * FROM vaga
            WHERE vaga.banda_id = '.$_SESSION['banda'])->fetchAll();
            $args['resultado'] = $resultSet;

            $resultSet = $conexao->query('SELECT * FROM perfil_pessoa INNER JOIN
            pedido WHERE pedido.usuario = perfil_pessoa.id')->fetchAll();
            $args['usuario'] = $resultSet;

        }


        // Render index view
        return $container->get('renderer')->render($response, 'vagas.phtml', $args);
    });
    $app->post('/vagas/', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/vagas/' route");

        $conexao = $container->get('pdo');
        $params = $request->getParsedBody();

        if ($_SESSION['banda'] == false) {
            $resultSet = $conexao->query('SELECT * FROM vaga
        WHERE id = "' . $params['vaga'] . '"')->fetchAll();

            $conexao->query('INSERT INTO pedido(usuario,vaga,banda_destino)
        VALUES(' . $_SESSION['loginID'] . ',' . $resultSet[0]['id'] . ',' . $resultSet[0]['banda_id'] . ')');
        } else { }

        // Render index view
        return $container->get('renderer')->render($response, 'vagas.phtml', $args);
    });
};
