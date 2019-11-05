<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->get('/criar-evento/', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/criar-evento/' route");

        $conexao = $container->get('pdo');


        $args['banda'] = true;
        $resultSet = $conexao->query('SELECT id,nome_usuario FROM perfil_banda WHERE id = ' . $_SESSION['loginID'])->fetchAll();
        $args['eventos'] = $conexao->query('SELECT nome_evento,descricao,data FROM evento WHERE banda_id = ' . $resultSet[0]['id'])->fetchAll();
        $args['nome_banda'] = $resultSet[0]['nome_usuario'];
        $args['perfil'] = $resultSet;

        //Converte as datas para expor de forma coesa MM/DD/YYYY
        foreach ($args['eventos'] as $key => $value) {
            $args['eventos'][$key]['data'] = explode('-',$args['eventos'][$key]['data'])[1].'/'.explode('-',$args['eventos'][$key]['data'])[2].'/'.explode('-',$args['eventos'][$key]['data'])[0];
        }


        // Render index view
        return $container->get('renderer')->render($response, 'criarEvento.phtml', $args);
    });

    $app->post('/criar-evento/', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/criar-evento/' route");

        $conexao = $container->get('pdo');
        $params = $request->getParsedBody();
        
        //Converte a data para postar no banco
        $params['data'] = explode('/',$params['data'])[2] . '-' . explode('/',$params['data'])[0].'-'.explode('/',$params['data'])[1];

        $conexao->query('INSERT INTO evento (nome_evento, data, descricao, banda_id)
        VALUES ("' . $params['nome_evento'] . '", "' . $params['data'] . '" ,"' . $params['descricao'] . '", ' . $_SESSION['loginID'] . ')')->fetchAll();

        return $response->withRedirect('/criar-evento/');


        // Render index view
        return $container->get('renderer')->render($response, 'criarEvento.phtml', $args);
    });
};
