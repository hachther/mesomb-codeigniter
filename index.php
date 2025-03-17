<?php

// Vérifiez si le fichier .env existe et chargez les variables d'environnement
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

// Inclure l'autoloader de Composer
require __DIR__ . '/vendor/autoload.php';

// Définir le chemin vers le répertoire système de CodeIgniter
$system_path = 'system';

// Définir le chemin vers le répertoire d'application
$application_folder = 'application';

// Définir le chemin vers le répertoire de vues
$view_folder = '';

// Le reste du fichier index.php de CodeIgniter
// ...

// Charger le fichier bootstrap de CodeIgniter
require_once BASEPATH . 'core/CodeIgniter.php';