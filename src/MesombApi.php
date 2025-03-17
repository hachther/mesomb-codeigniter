<?php

require 'vendor/autoload.php';

// Exemple d'utilisation
use Mesomb\MesombApi;

$mesomb = new MesombApi('VOTRE_CLE_API', 'VOTRE_SECRET_API');
$response = $mesomb->initiatePayment(1500.00, 'XOF', '+22501234567', 'http://votre-domaine/callback');
print_r($response);
