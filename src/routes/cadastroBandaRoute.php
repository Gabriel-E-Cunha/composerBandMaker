<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->get('/criarBanda/[{erro}]', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/criarBanda/' route");

        if(isset($args['erro']) && $args['erro'] == "error") {
            $args['cadastroError'] = true;
        } else {
            $args['cadastroError'] = false;
        }

        // Render index view
        return $container->get('renderer')->render($response, 'cadastroBanda.phtml', $args);
    });

    $app->post('/criarBanda/', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/criarBanda/' route");

        $conexao = $container->get('pdo');
        $params = $request->getParsedBody();

        
        $conexao->query('INSERT INTO perfil_banda (nome_usuario,cidade,
        cep,estado,email,influencias,descricao) 
        VALUES("' . $params['nome_usuario'] . '", "' . $params['cidade'] . '",
         "' . $params['cep'] . '", "' . $params['estado'] . '",
         "' . $params['email'] . '","' . $params['influencias'] . '",
         "' . $params['descricao'] . '")');

        $resultSet = $conexao->query('SELECT * FROM perfil_banda
        WHERE nome_usuario = "' . $params['nome_usuario'] . '"')->fetchAll();

        $conexao->query('INSERT INTO dado_login (nome_usuario, senha, banda_id)
        VALUES("'. $params['nome_usuario'] .'", "'. md5($params['senha']).'", "'. $resultSet[0]['id'] .'")');
        
            $_SESSION['banda'] = true;
            $_SESSION['loginID'] = $resultSet[0]['id'];
            return $response->withRedirect('/');


        // Render index view
        return $container->get('renderer')->render($response, 'perfilBanda.phtml', $args);
    });
};
