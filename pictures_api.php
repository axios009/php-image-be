<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require 'vendor/autoload.php';

$app = new \Slim\App;

// Add Routing Middleware
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);


$app->get('/list', function (Request $request, Response $response, $args) {
  $directory = '../uploads';
  $files = array_diff(scandir($directory), array('..', '.'));
  $pictures = [];

  foreach ($files as $file) {
    $filePath = $directory . '/' . $file;
    $fileContents = file_get_contents($filePath);
    $base64 = base64_encode($fileContents);
    $fileInfo = pathinfo($filePath);

    $pictures[] = [
      'url' => $filePath,
      'name' => $file,
      'base64' => $base64,
      'extension' => $fileInfo['extension']
    ];
  }

  $response = $response->withHeader('Content-Type', 'application/json');
  $response->getBody()->write(json_encode($pictures));
  return $response;
});

$app->post('/delete', function (Request $request, Response $response, $args) {
  $directory = '../uploads';
  $r = $request->getParsedBody();
  $r = (object) $r;
  $filename = $r->filename;
  $filepath = $directory . '/' . $filename;
  if (is_file($filepath)) {
    unlink($filepath);
  }
  $response = $response->withHeader('Content-Type', 'application/json');
  $response->getBody()->write(json_encode('Success'));
  return $response;
});


$app->run();