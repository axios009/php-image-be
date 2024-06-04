<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require 'vendor/autoload.php';

$app = new \Slim\App;

// Add Routing Middleware
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);


$app->post('/upload', function (Request $request, Response $response, $args) {
  $directory = '../uploads';
  $uploadedFiles = $request->getUploadedFiles();
  $response = $response->withHeader('Content-Type', 'application/json');

  if (empty($uploadedFiles['picture'])) {
    $response->getBody()->write(json_encode(['success' => false, 'message' => 'No file uploaded.']));
    return $response;
  }

  $uploadedFile = $uploadedFiles['picture'];
  if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
    $filename = moveUploadedFile($directory, $uploadedFile);

    $response->getBody()->write(json_encode(['success' => true, 'filename' => $filename]));
  } else {
    $response->getBody()->write(json_encode(['success' => false, 'message' => 'Error during file upload.']));
  }

  return $response;
});

function moveUploadedFile($directory, \Slim\Http\UploadedFile $uploadedFile)
{
  $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
  $timestamp = date('d-m-Y-Hisv');
  $filename = sprintf('%s.%s', $timestamp, $extension);

  $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

  return $filename;
}

$app->run();