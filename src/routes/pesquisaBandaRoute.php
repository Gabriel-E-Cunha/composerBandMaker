<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->get('/pesquisaBanda/', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/pesquisaBanda/' route");
        $conexao = $container->get('pdo');   

        //Caso logado, envia info para Navbar de logado
        if (isset($_SESSION['loginID'])) {
            if($_SESSION['banda']) {
                $args['banda'] = true;
                $resultSet = $conexao->query('SELECT * FROM perfil_banda WHERE id = ' . $_SESSION['loginID'])->fetchAll();
                $notificacoes = $conexao->query('SELECT COUNT(id) FROM notificacao WHERE banda_id = '.$_SESSION['loginID'])->fetchAll();
                $args['notificacao'] = $notificacoes[0]['COUNT(id)'];
            } else {
                $args['banda'] = false;
                $resultSet = $conexao->query('SELECT * FROM perfil_pessoa WHERE id = ' . $_SESSION['loginID'])->fetchAll(); 
                $notificacoes = $conexao->query('SELECT COUNT(id) FROM notificacao WHERE pessoa_id = '.$_SESSION['loginID'])->fetchAll();
                $args['notificacao'] = $notificacoes[0]['COUNT(id)'];
            }
            $args['perfil'] = $resultSet;
        }

        $resultSetGenero = $conexao->query('SELECT DISTINCT genero FROM perfil_banda')->fetchAll();       
        $args['genero'] = $resultSetGenero;

        if(isset($_GET['busca']) && isset($_GET['genero']) && $_GET['genero'] != "Gênero") {
            $resultSet = $conexao->query('SELECT imagem,nome_usuario,genero FROM perfil_banda WHERE nome_usuario LIKE "%' . $_GET['busca'] . '%" AND genero LIKE "%' . $_GET['genero'] . '%"')->fetchAll();
            $args['resultado'] = $resultSet; 
        } else if (isset($_GET['busca'])) {                     
            $resultSet = $conexao->query('SELECT imagem,nome_usuario,genero FROM perfil_banda WHERE nome_usuario LIKE "%' . $_GET['busca'] . '%"')->fetchAll();
            $args['resultado'] = $resultSet;            
        }
        else if (isset($_GET['genero']) && $_GET['genero'] != "Gênero") {            
            $resultSet = $conexao->query('SELECT imagem,nome_usuario,genero FROM perfil_banda WHERE genero LIKE "%' . $_GET['genero'] . '%"')->fetchAll();
            $args['resultado'] = $resultSet;  
        }

        // Render index view
        return $container->get('renderer')->render($response, 'buscarBanda.phtml', $args);
    });
};
