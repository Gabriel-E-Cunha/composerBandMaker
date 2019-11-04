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

        if ($_SESSION['banda']) {
            $resultSet = $conexao->query('SELECT titulo, notificacao.descricao, pedido_id FROM notificacao INNER JOIN perfil_banda WHERE perfil_banda.id = ' . $_SESSION['loginID'] . ' AND perfil_banda.id = banda_id')->fetchAll();
        } else {
            $resultSet = $conexao->query('SELECT titulo, descricao, pedido_id FROM notificacao INNER JOIN perfil_pessoa WHERE perfil_pessoa.id = ' . $_SESSION['loginID'] . ' AND perfil_pessoa.id = pessoa_id')->fetchAll();
        }
        $args['notificacoes'] = $resultSet;

        // Render index view
        return $container->get('renderer')->render($response, 'notificacoes.phtml', $args);
    });

    $app->delete('/notificacoes/', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/notificacoes/' route");
        $conexao = $container->get('pdo');
        $params = $request->getParsedBody();



        // Render index view
        return $container->get('renderer')->render($response, 'notificacoes.phtml', $args);
    });

    $app->post('/notificacoes/', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/notificacoes/' route");
        $conexao = $container->get('pdo');
        $params = $request->getParsedBody();

        if ($params['botao'] == 'aceitar') {
            $resultSet = $conexao->query('SELECT vaga_id,usuario FROM pedido WHERE id = ' . $params['pedido_id'])->fetchAll();
            //Coloca o usuário na Banda
            $conexao->query('UPDATE perfil_pessoa SET banda_id = ' . $_SESSION['loginID'] . ' WHERE id = ' . $resultSet[0]['usuario'])->fetchAll();

            //Deleta todas as notificações e pedidos envolvidos com a vaga
            $conexao->query('DELETE FROM notificacao WHERE pedido_id IN(SELECT id FROM pedido WHERE usuario = '.$resultSet[0]['usuario'].')')->fetchAll();
            $conexao->query('DELETE FROM notificacao WHERE pedido_id IN(SELECT id FROM pedido WHERE vaga_id = '.$resultSet[0]['vaga_id'].')')->fetchAll();
            $conexao->query('DELETE FROM pedido WHERE usuario = ' . $resultSet[0]['usuario'])->fetchAll();
            $conexao->query('DELETE FROM pedido WHERE vaga_id = ' . $resultSet[0]['vaga_id'])->fetchAll();
            //Deleta a vaga
            $conexao->query('DELETE FROM vaga WHERE id = ' . $resultSet[0]['vaga_id'])->fetchAll();

        } else if($params['botao'] == 'recusar'){
            //Deleta a notificação e o pedido
            $conexao->query('DELETE FROM notificacao WHERE pedido_id = ' . $params['pedido_id'])->fetchAll();
            $conexao->query('DELETE FROM pedido WHERE id = ' . $params['pedido_id'])->fetchAll();
        }

        return $response->withRedirect('/notificacoes/');

    });
};
