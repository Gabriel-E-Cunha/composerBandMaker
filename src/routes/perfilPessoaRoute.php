<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();
    $app->get('/perfil/[{action}]', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/perfil/' route");

        $conexao = $container->get('pdo');

        $resultSetPerfil = $conexao->query('SELECT * FROM perfil_pessoa WHERE nome_usuario = "' . $args['action'] . '"')->fetchAll();
        $resultSetBanda = $conexao->query('SELECT perfil_banda.nome_usuario FROM perfil_banda INNER JOIN perfil_pessoa WHERE perfil_pessoa.banda_id = perfil_banda.id AND perfil_pessoa.id = ' . $resultSetPerfil[0]['id'])->fetchAll();

        $args['perfil'] = $resultSetPerfil;
        $args['banda'] = $resultSetBanda;

        // Render index view
        return $container->get('renderer')->render($response, 'perfilNormal.phtml', $args);
    });

    $app->post('/perfil/[{action}]', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/perfil/' route");

        $conexao = $container->get('pdo');

        $resultSet = $conexao->query('SELECT * FROM perfil_pessoa WHERE id = "' . $_SESSION['loginID'] . '"')->fetchAll();


        $conexao->query('INSERT INTO notificacao(titulo,descricao,banda_id)
         VALUES("Aviso","O usuário ' . $resultSet[0]['nome_usuario'] . ' saiu da banda",
         ' . $resultSet[0]['banda_id'] . ')');

        $conexao->query('UPDATE perfil_pessoa
        SET banda_id = Null WHERE id = ' . $_SESSION['loginID']);
        // Render index view
        return $response->withRedirect('/perfil/' . $resultSet[0]['nome_usuario'] . '');
    });
};
