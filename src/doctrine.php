<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

$path = [
    __DIR__ . '/Entity'
];

$isDevMod = true;

$conn = [
    'driver' => 'pdo_mysql',
    'host' => '127.0.0.1',
    'port' => '3306',
    'user' => 'root',
    'password' => 'root',
    'dbname' => 'curso_doctrine_basico'
];

$config = Setup::createAnnotationMetadataConfiguration($path, $isDevMod);
$entityManager = EntityManager::create($conn, $config);

function getEntityManager() {
    global $entityManager;
    return $entityManager;
}