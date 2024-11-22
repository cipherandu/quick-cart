<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../src/vendor/autoload.php';

$app = new \Slim\App;

    //test end point
    $app->get('/getName/{fname}/{lname}', function (Request $request, Response $response, array $args) {
        $name = $args['fname'] . " " . $args['lname'];
        $response->getBody()->write("Hello, $name");
        return $response;

    });




$app->run();
?>