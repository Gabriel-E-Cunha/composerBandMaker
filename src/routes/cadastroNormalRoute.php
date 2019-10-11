<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->get('/criarConta/[{action}]', function (Request $request, Response $response, array $args) use ($container) {
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

        $resultSet = $conexao->query('SELECT nome_usuario FROM perfil_pessoa WHERE nome_usuario = "'. $params['nome_usuario'] .'"')->fetchAll();

        if (
            $params['nome_usuario'] == null || $params['nome'] == null || $params['cidade'] == null || $params['cep'] == null ||
            $params['sobrenome'] == null || $params['estado'] == null || $params['email'] == null || $params['idade'] == null ||
            $params['tempo'] == null || $params['email'] == null || $params['instrumento'] == "---"
        ) {
            return $response->withRedirect('/criarConta/blank-fields');

        } else if ($resultSet != null) {

            return $response->withRedirect('/criarConta/user-alredy-exists');

        } else if ($params['senha'] != $params['confirmar-senha']) {
            return $response->withRedirect('/criarBanda/passwords-not-equal');
        } else {

            $conexao->query('INSERT INTO perfil_pessoa (nome_usuario,nome,sobrenome,email,idade,cidade,estado,instrumento,
            tempo,telefone,influencia,cep,rua) 
            VALUES("' . $params['nome_usuario'] . '", "' . $params['nome'] . '", "' . $params['sobrenome'] . '",
            "' . $params['email'] . '", "' . $params['idade'] . '", "' . $params['cidade'] . '", "' . $params['estado'] . '",
            "' . $params['instrumento'] . '", "' . $params['tempo'] . '", "' . $params['telefone'] . '", "' . $params['influencia'] . '","' . $params['cep'] . '", "' . $params['rua'] . '")');

            $resultSet = $conexao->query('SELECT * FROM perfil_pessoa
            WHERE nome_usuario = "' . $params['nome_usuario'] . '"')->fetchAll();

            $conexao->query('INSERT INTO dado_login (nome_usuario, senha, pessoa_id)
            VALUES("' . $params['nome_usuario'] . '", "' . md5($params['senha']) . '", "' . $resultSet[0]['id'] . '")');

            $_SESSION['banda'] = false;
            $_SESSION['loginID'] = $resultSet[0]['id'];
            return $response->withRedirect('/');
        }
        // Render index view
        return $container->get('renderer')->render($response, 'cadastroNormal.phtml', $args);
    });
};