<?php
if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

session_start();

// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);

// Set up dependencies
$dependencies = require __DIR__ . '/../src/dependencies.php';
$dependencies($app);

// Register middleware
$middleware = require __DIR__ . '/../src/middleware.php';
$middleware($app);

// Register routes
$routes = require __DIR__ . '/../src/routes/indexRoute.php';
$routes($app);

//login
$routes = require __DIR__ . '/../src/routes/loginRoute.php';
$routes($app);

//Perfil Banda
$routes = require __DIR__ . '/../src/routes/perfilBandaRoute.php';
$routes($app);

// Cadastro Normal
$routes = require __DIR__ . '/../src/routes/perfilNormalRoute.php';
$routes($app);

// Cadastro Banda
$routes = require __DIR__ . '/../src/routes/cadastroBandaRoute.php';
$routes($app);

// Eventos
$routes = require __DIR__ . '/../src/routes/eventosRoute.php';
$routes($app);

//Criar Conta normal
$routes = require __DIR__ . '/../src/routes/cadastroNormalRoute.php';
$routes($app);

// Run app
$app->run();
