<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->get('/criarBanda/[{action}]', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/criarBanda/' route");

        // Render index view
        return $container->get('renderer')->render($response, 'cadastroBanda.phtml', $args);
    });

    $app->post('/criarBanda/', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/criarBanda/' route");

        $conexao = $container->get('pdo');
        $params = $request->getParsedBody();

        $resultSet = $conexao->query('SELECT nome_usuario FROM perfil_banda WHERE nome_usuario = "' . $params['nome_usuario'] . '"')->fetchAll();
        $imgFileType = explode('/', $_FILES["img"]["type"])[1];
        $imgName = "profile" . $resultSet[0]['id'] . "." . $imgFileType;
        $target_dir = "public/assets/BandP  rofileImg/";
        $target_file = $target_dir . $imgName;
        $_SESSION['inputValues'] = $params;
        $_SESSION['inputValues']['senha'] = null;
        $_SESSION['inputValues']['confirmar-senha'] = null;


        if (
            $params['nome_usuario'] == null || $params['senha'] == null || $params['confirmar-senha'] == null  || $params['cidade'] == null || $params['cep'] == null ||
            $params['estado'] == null || $params['email'] == null || $params['rua'] == null || $params['genero'] == null
        ) {
            return $response->withRedirect('/criarBanda/blank-fields');
        } else if ($resultSet != null) {

            return $response->withRedirect('/criarBanda/band-alredy-exists');
        } else if ($params['senha'] != $params['confirmar-senha']) {

            return $response->withRedirect('/criarBanda/passwords-not-equal');
        } else if (getimagesize($_FILES["img"]["tmp_name"]) == false) {
            return $response->withRedirect('/criarBanda/not-an-image');
        } else if ($imgFileType != "jpeg" && $imgFileType != "png" && $imgFileType != "jpg") {
            return $response->withRedirect('/criarBanda/incorrect-format');
        } else if ($_FILES["img"]["size"] > 500000) {
            return $response->withRedirect('/criarConta/img-too-big');
        } else {

            $conexao->query('INSERT INTO perfil_banda (nome_usuario,cidade,
            cep,estado,email,influencias,descricao,telefone,rua,genero) 
            VALUES("' . $params['nome_usuario'] . '", "' . $params['cidade'] . '",
             "' . $params['cep'] . '", "' . $params['estado'] . '",
             "' . $params['email'] . '","' . $params['influencias'] . '",
             "' . $params['descricao'] . '", "' . $params['telefone'] . '", "' . $params['rua'] . '",
             "' . $params['genero'] . '")');

            $resultSet = $conexao->query('SELECT * FROM perfil_banda
            WHERE nome_usuario = "' . $params['nome_usuario'] . '"')->fetchAll();

            $conexao->query('INSERT INTO dado_login (nome_usuario, senha, banda_id)
            VALUES("' . $params['nome_usuario'] . '", "' . md5($params['senha']) . '", "' . $resultSet[0]['id'] . '")');

            //Tratamento de imagem 
            move_uploaded_file($_FILES["img"]["tmp_name"], $target_file);

            $conexao->query('UPDATE perfil_pessoa SET imagem = "' . $imgName . '" WHERE id = ' . $resultSet[0]['id']);


            $_SESSION['banda'] = true;
            $_SESSION['loginID'] = $resultSet[0]['id'];
            return $response->withRedirect('/');
        }



        // Render index view
        return $container->get('renderer')->render($response, 'perfilBanda.phtml', $args);
    });
};
