<?php

use App\Entity\Post;
use App\Entity\Category;
use Zend\Diactoros\Response;
use Psr\Http\Message\ServerRequestInterface;

$map->get('home', '/', function(ServerRequestInterface $request, $response) use ($view, $entityManager) {
    
    $queryParams = $request->getQueryParams();

    $repositoryCategory = $entityManager->getRepository(Category::class);
    $categories = $repositoryCategory->findAll();

    $repositoryPost = $entityManager->getRepository(Post::class);

    if (isset($queryParams['search']) and !is_null($queryParams['search'])) {
        $queryBuilder = $repositoryPost->createQueryBuilder('p');
        $queryBuilder->join('p.categories', 'c')
            ->where($queryBuilder->expr()->eq('c.id', $queryParams['search']));
        $posts = $queryBuilder->getQuery()->getResult();
    } else {
        $posts = $repositoryPost->findAll();
    }

    return $view->render($response, 'home.phtml', [
        "posts" => $posts,
        "categories" => $categories
    ]);
});

$map->get('posts.list', '/posts', function($request, $response) use ($view, $entityManager) {
    $repository = $entityManager->getRepository(Post::class);
    $posts = $repository->findAll();
    return $view->render($response, 'posts/list.phtml', [
        "posts" => $posts
    ]);
});

$map->get('posts.create', '/posts/create', function($request, $response) use ($view, $entityManager) {
    return $view->render($response, 'posts/create.phtml');
});

$map->post('posts.store', '/posts/store', function(ServerRequestInterface $request, $response) use ($view, $entityManager, $generator) {
    $data = $request->getParsedBody();
    
    $posts = new Post();
    $posts->setTitle($data['title']);
    $posts->setContent($data['content']);

    $entityManager->persist($posts);
    $entityManager->flush();

    $uri = $generator->generate('posts.list');
    return new Response\RedirectResponse($uri);
});

$map->get('posts.edit', '/posts/edit/{id}', function($request, $response) use ($view, $entityManager) {
    $id = $request->getAttribute("id");
    $repository = $entityManager->getRepository(Post::class);
    $post = $repository->find($id);

    return $view->render($response, 'posts/edit.phtml', [
        "post" => $post
    ]);
});

$map->post('posts.update', '/posts/update', function(ServerRequestInterface $request, $response) use ($view, $entityManager, $generator) {
    $data = $request->getParsedBody();

    $repository = $entityManager->getRepository(Post::class);
    
    $post = $repository->find($data['id']);
    $post->setTitle($data['title']);
    $post->setContent($data['content']);

    $entityManager->flush();

    $uri = $generator->generate('posts.list');
    return new Response\RedirectResponse($uri);
});

$map->get('posts.remove', '/posts/remove/{id}', function(ServerRequestInterface $request, $response) use ($view, $entityManager, $generator) {
    $id = $request->getAttribute("id");

    $repository = $entityManager->getRepository(Post::class);
    
    $post = $repository->find($id);

    $entityManager->remove($post);
    $entityManager->flush();

    $uri = $generator->generate('posts.list');
    return new Response\RedirectResponse($uri);
});

$map->get('posts.categories', '/posts/categories/{id}', function($request, $response) use ($view, $entityManager) {
    $idPost = $request->getAttribute("id");

    $repository = $entityManager->getRepository(Category::class);
    $categories = $repository->findAll();

    $repository = $entityManager->getRepository(Post::class);
    $posts = $repository->find($idPost);

    return $view->render($response, 'posts/posts-categories.phtml', [
        "posts" => $posts,
        "categories" => $categories
    ]);
});

$map->post('posts.categories.store', '/posts/categories/store', function($request, $response) use ($view, $entityManager, $generator) {
    $data = $request->getParsedBody();
    
    $repository = $entityManager->getRepository(Post::class);
    $post = $repository->find($data['idPost']);
    $post->getCategories()->clear();
    $entityManager->flush();

    $repositoryCategory = $entityManager->getRepository(Category::class);
    foreach ($data['categories'] as $idCategories) {
        $category = $repositoryCategory->find($idCategories);
        $post->addCategory($category);
    }
    $entityManager->flush();

    $uri = $generator->generate('posts.list');
    return new Response\RedirectResponse($uri);
});