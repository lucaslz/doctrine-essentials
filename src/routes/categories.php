<?php

use App\Entity\Category;
use Zend\Diactoros\Response;
use Psr\Http\Message\ServerRequestInterface;

$map->get('categories.list', '/categories', function($request, $response) use ($view, $entityManager) {
    $repository = $entityManager->getRepository(Category::class);
    $categories = $repository->findAll();
    return $view->render($response, 'categories/categories.phtml', [
        "categories" => $categories
    ]);
});

$map->get('categories.create', '/categories/create', function($request, $response) use ($view, $entityManager) {
    return $view->render($response, 'categories/create.phtml');
});

$map->post('categories.store', '/categories/store', function(ServerRequestInterface $request, $response) use ($view, $entityManager, $generator) {
    $data = $request->getParsedBody();
    
    $category = new Category();
    $category->setName($data['name']);
    $entityManager->persist($category);
    $entityManager->flush();

    $uri = $generator->generate('categories.list');
    return new Response\RedirectResponse($uri);
});

$map->get('categories.edit', '/categories/edit/{id}', function($request, $response) use ($view, $entityManager) {
    $id = $request->getAttribute("id");
    $repository = $entityManager->getRepository(Category::class);
    $category = $repository->find($id);

    return $view->render($response, 'categories/edit.phtml', [
        "category" => $category
    ]);
});

$map->post('categories.update', '/categories/update', function(ServerRequestInterface $request, $response) use ($view, $entityManager, $generator) {
    $data = $request->getParsedBody();

    $repository = $entityManager->getRepository(Category::class);
    
    $category = $repository->find($data['id']);
    $category->setName($data['name']);

    $entityManager->flush();

    $uri = $generator->generate('categories.list');
    return new Response\RedirectResponse($uri);
});

$map->get('categories.remove', '/categories/remove/{id}', function(ServerRequestInterface $request, $response) use ($view, $entityManager, $generator) {
    $id = $request->getAttribute("id");

    $repository = $entityManager->getRepository(Category::class);
    
    $category = $repository->find($id);

    $entityManager->remove($category);
    $entityManager->flush();

    $uri = $generator->generate('categories.list');
    return new Response\RedirectResponse($uri);
});
