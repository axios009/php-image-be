<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require 'vendor/autoload.php';

$app = new \Slim\App;

// Add Routing Middleware
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

$app->get('/health', function (Request $request, Response $response, $args) {
  $response = $response->withHeader('Content-Type', 'application/json');
  $response->getBody()->write(json_encode(['status' => "Online"]));
  return $response;
});

$app->post('/login', function (Request $request, Response $response, $args) {
  $r = $request->getParsedBody();
  $r = (object) $r;
  $username = $r->username;
  $password = $r->password;
  $response = $response->withHeader('Content-Type', 'application/json');
  if ($username == 'admin' && $password == '77x12345678') {
    $response->getBody()->write(json_encode(['status' => "Success"]));
  } else {
    $response->getBody()->write(json_encode(['status' => "Fail"]));
  }
  return $response;
});

$app->run();