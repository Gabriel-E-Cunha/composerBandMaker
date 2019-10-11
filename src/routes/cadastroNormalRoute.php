<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->get('/criarConta/', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/criarConta/' route");





        // Render index view
        return $container->get('renderer')->render($response, 'cadastroNormal.phtml', $args);
    });

    $app->post('/criarConta/', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/criarConta/' route");

        $conexao = $container->get('pdo');
        $params = $request->getParsedBody();

        $conexao->query('INSERT INTO perfil_pessoa (nome_usuario,nome,sobrenome,email,idade,cidade,estado,instrumento,
        tempo,telefone,influencia,cep,rua) 
        VALUES("' . $params['nome_usuario'] . '", "' . $params['nome'] . '", "' . $params['sobrenome'] . '",
        "' . $params['email'] . '", "' . $params['idade'] . '", "' . $params['cidade'] . '", "' . $params['estado'] . '",
        "' . $params['instrumento'] . '", "' . $params['tempo'] . '", "' . $params['telefone'] . '", "' . $params['influencia'] . '","'. $params['cep'] .'", "'. $params['rua'] .'")');

        $resultSet = $conexao->query('SELECT * FROM perfil_pessoa
        WHERE nome_usuario = "' . $params['nome_usuario'] . '"')->fetchAll();

        $conexao->query('INSERT INTO dado_login (nome_usuario, senha, pessoa_id)
        VALUES("' . $params['nome_usuario'] . '", "' . md5($params['senha']) . '", "' . $resultSet[0]['id'] . '")');

        $_SESSION['banda'] = false;
        $_SESSION['loginID'] = $resultSet[0]['id'];
        return $response->withRedirect('/');

        // Render index view
        return $container->get('renderer')->render($response, 'cadastroNormal.phtml', $args);
    });
};
