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

        if($_FILES['img']['tmp_name'] != null) {
            $imgFileType = explode('/',$_FILES["img"]["type"])[1];
        }
        
        $_SESSION['inputValues'] = $params;
        $_SESSION['inputValues']['senha'] = null;
        $_SESSION['inputValues']['confirmar-senha'] = null;
       
        if (
            $params['nome_usuario'] == null || $params['senha'] == null || $params['confirmar-senha'] == null || $params['nome'] == null || $params['cidade'] == null || $params['cep'] == null ||
            $params['sobrenome'] == null || $params['estado'] == null || $params['email'] == null || $params['idade'] == null ||
            $params['tempo'] == null || $params['email'] == null || $params['instrumento'] == "---"
        ) {
            return $response->withRedirect('/criarConta/blank-fields');

        } else if ($resultSet != null) {
        //    verifica conta/senha
            return $response->withRedirect('/criarConta/user-alredy-exists');
        } else if ($params['senha'] != $params['confirmar-senha']) {
            return $response->withRedirect('/criarConta/passwords-not-equal');
        } 
        // verifica email
        else if(filter_var($params['email'], FILTER_VALIDATE_EMAIL) == false){
            return $response->withRedirect('/criarConta/email-not-valid');
        } 
        // verifica imagem
        else if ($_FILES['img']['name'] != null && $imgFileType != "jpeg" && $imgFileType != "png" && $imgFileType != "jpg") {
            return $response->withRedirect('/criarConta/incorrect-format');
        } else if ($_FILES['img']['name'] != null && $_FILES["img"]["size"] > 500000) {
                return $response->withRedirect('/criarConta/img-too-big');
        } else {

            $conexao->query('INSERT INTO perfil_pessoa (nome_usuario,nome,sobrenome,email,idade,cidade,estado,instrumento,
            tempo,telefone,influencia,cep,rua) 
            VALUES("' . $params['nome_usuario'] . '", "' . $params['nome'] . '", "' . $params['sobrenome'] . '",
            "' . $params['email'] . '", "' . $params['idade'] . '", "' . $params['cidade'] . '", "' . $params['estado'] . '",
            "' . $params['instrumento'] . '", "' . $params['tempo'] . '", "' . $params['telefone'] . '", "' . $params['influencia'] . '","' . $params['cep'] . '", "' . $params['rua'] . '")');

            $resultSet = $conexao->query('SELECT id FROM perfil_pessoa
            WHERE nome_usuario = "' . $params['nome_usuario'] . '"')->fetchAll();

            $conexao->query('INSERT INTO dado_login (nome_usuario, senha, pessoa_id)
            VALUES("' . $params['nome_usuario'] . '", "' . md5($params['senha']) . '", "' . $resultSet[0]['id'] . '")');

            //Tratamento da imagem
            if($_FILES['img']['tmp_name'] != null) {
                $imgName = "profile".$resultSet[0]['id']."." . $imgFileType;
                $target_dir = "public/assets/profileImg/";
                $target_file = $target_dir . $imgName;
                move_uploaded_file($_FILES["img"]["tmp_name"], $target_file);   
                $conexao->query('UPDATE perfil_pessoa SET imagem = "'.$imgName.'" WHERE id = ' . $resultSet[0]['id']);
            }
            
            $_SESSION['banda'] = false;
            $_SESSION['loginID'] = $resultSet[0]['id'];
            
            return $response->withRedirect('/');
        }
        // Render index view
        return $container->get('renderer')->render($response, 'cadastroNormal.phtml', $args);
    });
};
