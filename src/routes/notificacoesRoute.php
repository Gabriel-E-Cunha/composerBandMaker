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

        //Envia as info para o header e as notificações para a table
        if ($_SESSION['banda']) {
            $args['banda'] = true;
            $resultSet = $conexao->query('SELECT id,nome_usuario FROM perfil_banda WHERE id = ' . $_SESSION['loginID'])->fetchAll();
            $notificacoes = $conexao->query('SELECT notificacao.id,titulo, notificacao.descricao, pedido_id FROM notificacao INNER JOIN perfil_banda WHERE perfil_banda.id = ' . $_SESSION['loginID'] . ' AND perfil_banda.id = banda_id')->fetchAll();
            $notificacao = $conexao->query('SELECT COUNT(id) FROM notificacao WHERE banda_id = '.$_SESSION['loginID'])->fetchAll();
                $args['notificacao'] = $notificacao[0]['COUNT(id)'];
            
        } else {
            $args['banda'] = false;
            $resultSet = $conexao->query('SELECT nome_usuario,banda_id FROM perfil_pessoa WHERE id = ' . $_SESSION['loginID'])->fetchAll();
            $notificacoes = $conexao->query('SELECT notificacao.id, titulo, descricao, pedido_id FROM notificacao INNER JOIN perfil_pessoa WHERE perfil_pessoa.id = ' . $_SESSION['loginID'] . ' AND perfil_pessoa.id = pessoa_id')->fetchAll();
            $notificacao = $conexao->query('SELECT COUNT(id) FROM notificacao WHERE pessoa_id = '.$_SESSION['loginID'])->fetchAll();
                $args['notificacao'] = $notificacao[0]['COUNT(id)'];
        }
        $args['perfil'] = $resultSet;
        $args['notificacoes'] = $notificacoes;

        // Render index view
        return $container->get('renderer')->render($response, 'notificacoes.phtml', $args);
    });

    $app->post('/notificacoes/', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/notificacoes/' route");
        $conexao = $container->get('pdo');
        $params = $request->getParsedBody();

        if ($params['botao'] == 'aceitar') {
            $resultSet = $conexao->query('SELECT vaga_id, usuario, banda_destino FROM pedido WHERE id = ' . $params['pedido_id'])->fetchAll();
            //Coloca o usuário na Banda
            $conexao->query('UPDATE perfil_pessoa SET banda_id = ' . $_SESSION['loginID'] . ' WHERE id = ' . $resultSet[0]['usuario'])->fetchAll();

            //Envia a notificação para o usuário que foi aceito
            $resultSet = $conexao->query('SELECT vaga_id, usuario, banda_destino FROM pedido WHERE id = ' . $params['pedido_id'])->fetchAll();
            $vaga = $conexao->query('SELECT vaga FROM vaga WHERE id = '.$resultSet[0]['vaga_id'])->fetchAll();
            $nome_banda = $conexao->query('SELECT nome_usuario FROM perfil_banda WHERE id = '.$_SESSION['loginID'])->fetchAll();
            $conexao->query('INSERT INTO notificacao (titulo, descricao, pessoa_id) VALUES("Aviso","A banda '.$nome_banda[0]['nome_usuario'].' aceitou sua solicitação para preencher a vaga de '.$vaga[0]['vaga'].'",'.$resultSet[0]['usuario'].')')->fetchAll();

            //Deleta todas as notificações e pedidos envolvidos com a vaga
            $conexao->query('DELETE FROM notificacao WHERE pedido_id IN(SELECT id FROM pedido WHERE usuario = '.$resultSet[0]['usuario'].')')->fetchAll();
            $conexao->query('DELETE FROM notificacao WHERE pedido_id IN(SELECT id FROM pedido WHERE vaga_id = '.$resultSet[0]['vaga_id'].')')->fetchAll();
            $conexao->query('DELETE FROM pedido WHERE usuario = ' . $resultSet[0]['usuario'])->fetchAll();
            $conexao->query('DELETE FROM pedido WHERE vaga_id = ' . $resultSet[0]['vaga_id'])->fetchAll();
            //Deleta a vaga
            $conexao->query('DELETE FROM vaga WHERE id = ' . $resultSet[0]['vaga_id'])->fetchAll();

        } else if($params['botao'] == 'recusar'){
            //Envia a notificação para o usuário que foi recusado
            $resultSet = $conexao->query('SELECT vaga_id, usuario, banda_destino FROM pedido WHERE id = ' . $params['pedido_id'])->fetchAll();
            $vaga = $conexao->query('SELECT vaga FROM vaga WHERE id = '.$resultSet[0]['vaga_id'])->fetchAll();
            $nome_banda = $conexao->query('SELECT nome_usuario FROM perfil_banda WHERE id = '.$_SESSION['loginID'])->fetchAll();
            $conexao->query('INSERT INTO notificacao (titulo, descricao, pessoa_id) VALUES("Aviso","A banda '.$nome_banda[0]['nome_usuario'].' recusou sua solicitação para preencher a vaga de '.$vaga[0]['vaga'].'",'.$resultSet[0]['usuario'].')')->fetchAll();
           
            //Deleta a notificação e o pedido
            $conexao->query('DELETE FROM notificacao WHERE pedido_id = ' . $params['pedido_id'])->fetchAll();
            $conexao->query('DELETE FROM pedido WHERE id = ' . $params['pedido_id'])->fetchAll();

        } else if($params['botao'] == 'mensagem') {
            $conexao->query('DELETE FROM notificacao WHERE id = ' . $params['notificacao_id'])->fetchAll();
            
        }

        return $response->withRedirect('/notificacoes/');

    });
};
