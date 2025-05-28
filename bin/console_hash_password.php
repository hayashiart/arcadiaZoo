<?php

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

$container = new ContainerBuilder();
$kernel = new \App\Kernel('dev', true);
$kernel->boot();
$container = $kernel->getContainer();

$passwordHasher = $container->get('security.user_password_hasher');
$plainPassword = 'vetpassword123';
$hashedPassword = $passwordHasher->hashPassword(new \App\Entity\User(), $plainPassword);

echo "Hashed password: $hashedPassword\n";
