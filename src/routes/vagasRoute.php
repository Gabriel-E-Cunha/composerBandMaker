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

        $resultSet = $conexao->query('SELECT genero FROM perfil_banda')->fetchAll();       
        $args['genero'] = $resultSet;


        if ($_SESSION['banda'] == false) {

            if (isset($_GET['busca'])) {
                $resultSet = $conexao->query('SELECT * FROM perfil_banda INNER JOIN
                vaga WHERE vaga.banda_id = perfil_banda.id AND
                perfil_banda.nome_usuario LIKE "%' . $_GET['busca'] . '%"  AND vaga.id NOT IN
                (SELECT vaga_id FROM pedido WHERE usuario = '. $_SESSION['loginID'] .')')->fetchAll();
                $args['resultado'] = $resultSet;
            }
            if (isset($_GET['busca']) && $_GET['genero'] != "Gênero") {
                $resultSet = $conexao->query('SELECT * FROM perfil_banda INNER JOIN
                vaga WHERE vaga.banda_id = perfil_banda.id AND perfil_banda.genero ="' . $_GET['genero'] . '" AND vaga.id NOT IN
                (SELECT vaga_id FROM pedido WHERE usuario = '. $_SESSION['loginID'] .')')->fetchAll();
                $args['resultado'] = $resultSet;
            }
            if (isset($_GET['busca']) && $_GET['vaga'] != "Vaga") {
                $resultSet = $conexao->query('SELECT * FROM perfil_banda INNER JOIN
                vaga WHERE vaga.banda_id = perfil_banda.id AND vaga.vaga = "' . $_GET['vaga'] . '" AND vaga.id NOT IN
                (SELECT vaga_id FROM pedido WHERE usuario = '. $_SESSION['loginID'] .')')->fetchAll();
                $args['resultado'] = $resultSet;
            }
        }else {            
            $resultSet = $conexao->query('SELECT * FROM vaga WHERE vaga.banda_id = '.$_SESSION['loginID'])->fetchAll();
            $args['resultado'] = $resultSet;
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

            $conexao->query('INSERT INTO pedido(usuario,vaga_id,banda_destino)
        VALUES(' . $_SESSION['loginID'] . ',' . $resultSet[0]['id'] . ',' . $resultSet[0]['banda_id'] . ')');
        } else {
            if($params['vaga'] == null) {
                $conexao->query('DELETE FROM pedido WHERE vaga_id = '.$params['vaga_id'])->fetchAll();
                $conexao->query('DELETE FROM vaga WHERE id = '.$params['vaga_id'])->fetchAll();
            } else {
                $conexao->query('INSERT INTO vaga(vaga, banda_id) VALUES("'. $params['vaga'] .'",'.$_SESSION['loginID'].')')->fetchAll();
            }
            return $response->withRedirect('/vagas/');
        }

        // Render index view
        return $container->get('renderer')->render($response, 'vagas.phtml', $args);
    });
};
