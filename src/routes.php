<?php

use Aura\Router\RouterContainer;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response;
use App\Entity\Category;
use Psr\Http\Message\ServerRequestInterface;

$request = ServerRequestFactory::fromGlobals(
    $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
);

$routerContainer = new RouterContainer();

$generator = $routerContainer->getGenerator();
$map = $routerContainer->getMap();

$view = new \Slim\Views\PhpRenderer(__DIR__.'/../templates/');

$entityManager = getEntityManager();

require_once __DIR__ . '/routes/categories.php';
require_once __DIR__ . '/routes/posts.php'; 

$matcher = $routerContainer->getMatcher();

$route = $matcher->match($request);

foreach ($route->attributes as $key => $value) {
    $request = $request->withAttribute($key, $value);
}

$callable = $route->handler;

$response = $callable($request, new Response());

if ($response instanceof Response\RedirectResponse) {
    header("location: {$response->getHeader("location")[0] }");
} elseif ($response instanceof Response) {
    echo $response->getBody();
}