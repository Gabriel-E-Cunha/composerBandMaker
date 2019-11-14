<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->get('/eventos/', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message

        $container->get('logger')->info("Slim-Skeleton '/eventos/' route");
        $conexao = $container->get('pdo');

        //Caso o usuário tenha saído da tela de cadastro, apaga todos os inputs salvos.
        unset($_SESSION['bandValues']);
        unset($_SESSION['personValues']);

        //verifica se precisa deletar algum evento
        if(isset($_GET['deletar-evento'])) {
            $conexao->query('DELETE FROM evento WHERE id = ' . $_GET['deletar-evento'])->fetchAll();
            return $response->withRedirect('/eventos/');
        }

        //Envia as info para o header e pega o id da banda para fazer consulta de eventos
        if ($_SESSION['banda']) {
            $args['banda'] = true;
            $resultSet = $conexao->query('SELECT id,nome_usuario FROM perfil_banda WHERE id = ' . $_SESSION['loginID'])->fetchAll();
            $banda_id = $resultSet[0]['id'];
            $args['nome_banda'] = $resultSet[0]['nome_usuario'];
            $notificacoes = $conexao->query('SELECT COUNT(id) FROM notificacao WHERE banda_id = '.$_SESSION['loginID'])->fetchAll();
                $args['notificacao'] = $notificacoes[0]['COUNT(id)'];
        } else {
            $args['banda'] = false;
            $resultSet = $conexao->query('SELECT nome_usuario,banda_id FROM perfil_pessoa WHERE id = ' . $_SESSION['loginID'])->fetchAll();
            $banda_id = $resultSet[0]['banda_id'];
            $resultNomeBanda = $conexao->query('SELECT nome_usuario FROM perfil_banda WHERE id = ' . $resultSet[0]['banda_id'])->fetchAll();
            $args['nome_banda'] = $resultNomeBanda[0]['nome_usuario'];
            $notificacoes = $conexao->query('SELECT COUNT(id) FROM notificacao WHERE pessoa_id = '.$_SESSION['loginID'])->fetchAll();
                $args['notificacao'] = $notificacoes[0]['COUNT(id)'];
        }

        //Busca eventos
        if(isset($_GET)) {
            if(isset($_GET['nome_evento']) && $_GET['nome_evento'] != null) {
                $args['eventos'] = $conexao->query('SELECT id,nome_evento,descricao,data FROM evento WHERE banda_id = ' . $banda_id . ' AND nome_evento = "' . $_GET['nome_evento'] . '"')->fetchAll();
            } else if (isset($_GET['data']) && $_GET['data'] != null) {
                $data = explode('/', $_GET['data'])[2] . '-' . explode('/', $_GET['data'])[0] . '-' . explode('/', $_GET['data'])[1];
                $args['eventos'] = $conexao->query('SELECT id,nome_evento,descricao,data FROM evento WHERE banda_id = ' . $banda_id . ' AND data = "' . $data . '"')->fetchAll();
            } else {
                $args['eventos'] = $conexao->query('SELECT id,nome_evento,descricao,data FROM evento WHERE banda_id = ' . $banda_id)->fetchAll();
            }
        } else {
            $args['eventos'] = $conexao->query('SELECT id,nome_evento,descricao,data FROM evento WHERE banda_id = ' . $banda_id)->fetchAll();
        }

        //Converte as datas para expor de forma coesa MM/DD/YYYY
        foreach ($args['eventos'] as $key => $value) {
            $args['eventos'][$key]['data'] = explode('-', $args['eventos'][$key]['data'])[1] . '/' . explode('-', $args['eventos'][$key]['data'])[2] . '/' . explode('-', $args['eventos'][$key]['data'])[0];
        }
        $args['perfil'] = $resultSet;


        // Render index view
        return $container->get('renderer')->render($response, 'eventos.phtml', $args);
    });
};
