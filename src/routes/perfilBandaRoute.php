<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->get('/perfil-banda/[{action}]', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/perfil-banda/' route");

        $conexao = $container->get('pdo');

        //Caso logado, envia info para Navbar de logado
        if (isset($_SESSION['loginID'])) {
            if ($_SESSION['banda']) {
                $args['banda'] = true;
                $resultSet = $conexao->query('SELECT * FROM perfil_banda WHERE id = ' . $_SESSION['loginID'])->fetchAll();
            } else {
                $args['banda'] = false;
                $resultSet = $conexao->query('SELECT * FROM perfil_pessoa WHERE id = ' . $_SESSION['loginID'])->fetchAll();
            }
            $args['perfil'] = $resultSet;
        }

        //Envia informações do perfil do action para o front construir o perfil
        $resultSet = $conexao->query('SELECT * FROM perfil_banda WHERE nome_usuario = "' . $args['action'] . '"')->fetchAll();
        $args['perfilExibido'] = $resultSet;
        $resultSet = $conexao->query('SELECT * FROM perfil_pessoa WHERE banda_id = ' . $resultSet[0]['id'])->fetchAll();
        $args['integrante']  = $resultSet;


        // Render index view
        return $container->get('renderer')->render($response, 'perfilBanda.phtml', $args);
    });

    $app->post('/perfil-banda/[{action}]', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/perfil-banda/' route");

        $conexao = $container->get('pdo');

        $audioFileType = explode('.', $_FILES["audio"]["name"])[1];


        $resultSet = $conexao->query('SELECT * 
        FROM perfil_banda 
        WHERE id = ' . $_SESSION['loginID'] . '')->fetchAll();
        

        if ($_FILES['audio'] != null) {
            $audioName = "audio" . $resultSet[0]['id'] . "." . $audioFileType;
            $target_dir = "public/assets/audio/";
            $target_file = $target_dir . $audioName;
            move_uploaded_file($_FILES["audio"]["tmp_name"], $target_file);
            $conexao->query('UPDATE perfil_banda SET track = "' . $audioName . '"
            WHERE id = ' . $resultSet[0]['id'])->fetchAll();
        }
        return $response->withRedirect('/perfil-banda/' . $resultSet[0]['nome_usuario']);
    });
};
