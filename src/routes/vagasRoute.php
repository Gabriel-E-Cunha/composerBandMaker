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

        //Envia as info para o header
        if ($_SESSION['banda']) {
            $args['banda'] = true;
            $resultSet = $conexao->query('SELECT id,nome_usuario FROM perfil_banda WHERE id = ' . $_SESSION['loginID'])->fetchAll();
        } else {
            $args['banda'] = false;
            $resultSet = $conexao->query('SELECT nome_usuario,banda_id FROM perfil_pessoa WHERE id = ' . $_SESSION['loginID'])->fetchAll();
        }
        $args['perfil'] = $resultSet;

        //Cria uma busca caso não seja uma banda logada
        if ($_SESSION['banda'] == false) {

            if (isset($_GET['busca'])) {
                $resultSet = $conexao->query('SELECT * FROM perfil_banda INNER JOIN
                vaga WHERE vaga.banda_id = perfil_banda.id AND
                perfil_banda.nome_usuario LIKE "%' . $_GET['busca'] . '%"  AND vaga.id NOT IN
                (SELECT vaga_id FROM pedido WHERE usuario = ' . $_SESSION['loginID'] . ')')->fetchAll();
                $args['resultado'] = $resultSet;
            }
            if (isset($_GET['busca']) && $_GET['genero'] != "Gênero") {
                $resultSet = $conexao->query('SELECT * FROM perfil_banda INNER JOIN
                vaga WHERE vaga.banda_id = perfil_banda.id AND perfil_banda.genero ="' . $_GET['genero'] . '" AND vaga.id NOT IN
                (SELECT vaga_id FROM pedido WHERE usuario = ' . $_SESSION['loginID'] . ')')->fetchAll();
                $args['resultado'] = $resultSet;
            }
            if (isset($_GET['busca']) && $_GET['vaga'] != "Vaga") {
                $resultSet = $conexao->query('SELECT * FROM perfil_banda INNER JOIN
                vaga WHERE vaga.banda_id = perfil_banda.id AND vaga.vaga = "' . $_GET['vaga'] . '" AND vaga.id NOT IN
                (SELECT vaga_id FROM pedido WHERE usuario = ' . $_SESSION['loginID'] . ')')->fetchAll();
                $args['resultado'] = $resultSet;
            }
        } else {
            $resultSet = $conexao->query('SELECT * FROM vaga WHERE vaga.banda_id = ' . $_SESSION['loginID'])->fetchAll();
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
            //Consulta dos valores da vaga selecionada pelo usuário no formulário
            $resultSet = $conexao->query('SELECT vaga.id,vaga,vaga.banda_id  FROM vaga INNER JOIN perfil_pessoa
            WHERE vaga.id = "' . $params['vaga_id'] . '"')->fetchAll();

            //Consulta do nome do usuário logado, vai ser necessária para escrever na notificação
            $nome_usuario = $conexao->query('SELECT nome_usuario FROM perfil_pessoa WHERE id = ' . $_SESSION['loginID'])->fetchAll();

            //Criando o pedido com base na vaga selecionada e no usuário logado
            $conexao->query('INSERT INTO pedido(usuario,vaga_id,banda_destino)
            VALUES(' . $_SESSION['loginID'] . ',' . $resultSet[0]['id'] . ',' . $resultSet[0]['banda_id'] . ')');

            //Consulta do id do pedido que acabou de ser criado, vai ser necessário para criar a notificação
            $pedido_id = $conexao->query('SELECT pedido.id FROM pedido INNER JOIN vaga WHERE vaga.id = vaga_id AND vaga_id =  ' . $resultSet[0]['id'] . ' AND usuario = ' . $_SESSION['loginID'])->fetchAll();

            //Criando notificação que deve chegar para a banda que criou a vaga pedida
            $conexao->query('INSERT INTO notificacao(titulo,descricao,banda_id,pedido_id)
            VALUES("Convite","O usuário ' . $nome_usuario[0]['nome_usuario'] . ' quer preencher a vaga de ' . $resultSet[0]['vaga'] . '",' . $resultSet[0]['banda_id'] . ',' . $pedido_id[0]['id'] . ')');

            return $response->withRedirect('/vagas/');
        } else {
            if ($params['vaga'] == null) {
                $conexao->query('DELETE FROM pedido WHERE vaga_id = ' . $params['vaga_id'])->fetchAll();
                $conexao->query('DELETE FROM vaga WHERE id = ' . $params['vaga_id'])->fetchAll();
            } else {
                $conexao->query('INSERT INTO vaga(vaga, banda_id) VALUES("' . $params['vaga'] . '",' . $_SESSION['loginID'] . ')')->fetchAll();
            }
            return $response->withRedirect('/vagas/');
        }

        // Render index view
        return $container->get('renderer')->render($response, 'vagas.phtml', $args);
    });
};
