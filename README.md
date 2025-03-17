# Mesomb CodeIgniter Plugin

Ce plugin facilite l'intégration de la gateway de paiement mobile [Mesomb](https://mesomb.hachther.com/en/api/v1.1/schema/) dans vos applications CodeIgniter. Il permet d'initier des paiements, de vérifier le statut des transactions, d'effectuer des remboursements et des annulations, tout en assurant la sécurité des échanges grâce à la génération de signatures.

## Table des matières

- [Présentation](#présentation)
- [Installation](#installation)
- [Configuration](#configuration)
- [Utilisation](#utilisation)
- [Méthodes disponibles](#méthodes-disponibles)
- [Exemple d'utilisation](#exemple-dutilisation)
- [Accès aux Endpoints](#accès-aux-endpoints)
- [Auteur](#auteur)
- [Show your support](#show-your-support)

## Présentation

Ce plugin a été développé pour offrir une intégration simple et modulable de l'API Mesomb dans les applications CodeIgniter. Grâce à une couche d'abstraction dédiée, vous pouvez facilement initier des paiements mobiles, vérifier leur statut, et gérer les opérations de remboursement ou d'annulation. La sécurité des requêtes est renforcée par l'ajout d'une méthode optionnelle pour générer une signature HMAC-SHA256.

## Installation

### Installation manuelle

1. Clonez ou téléchargez ce dépôt.
2. Copiez le fichier `Mesomb_api.php` dans le répertoire `application/Libraries` de votre projet CodeIgniter.
3. Copiez le fichier de configuration `mesomb.php` dans le répertoire `application/Config`.

### Installation via Composer (optionnel)

Si vous préférez gérer vos dépendances avec Composer, vous pouvez adapter ce projet en créant un package Composer et en configurant l'autoloading PSR-4 pour l'intégrer dans votre projet.

## Configuration

Créez un fichier `mesomb.php` dans le dossier `application/Config` et renseignez vos paramètres API :

```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['mesomb_api_url'] = 'https://mesomb.hachther.com/en/api/v1.1';
```

Ajoutez les clés API dans vos variables d'environnement :

```sh
MESOMB_API_KEY='VOTRE_CLE_API'
MESOMB_API_SECRET='VOTRE_SECRET_API'
```

## Utilisation

Chargez la librairie dans vos contrôleurs CodeIgniter :

```php
$this->load->library('mesomb_api');
```
Vous pouvez alors appeler les méthodes disponibles pour interagir avec l'API Mesomb.

# Méthodes disponibles

* initiatePayment($amount, $currency, $payer, $callbackUrl)
Initie un paiement mobile en générant automatiquement une signature sécurisée.

* checkPaymentStatus($transactionId)
Vérifie le statut d'une transaction en cours.

* refundPayment($transactionId, $amount)
Procède au remboursement partiel ou total d'une transaction.

* cancelPayment($transactionId)
Annule une transaction.

* generateSignature($params, $method, $endpoint) (méthode privée)
Génère une signature HMAC-SHA256 basée sur les paramètres de la requête, utilisée pour sécuriser les appels à l'API.


## Utilisation

### Exemple d'utilisation 

Voici un exemple de contrôleur utilisant ce plugin :

Créez un contrôleur (par exemple `Paiement.php`) dans `application/controllers/` pour tester et utiliser le plugin :

```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Paiement extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Chargement de la librairie Mesomb_api
        $this->load->library('mesomb_api');
    }
    
    /**
     * Initier un paiement avec signature sécurisée.
     */
    public function initier() {
        $amount      = 1500.00;
        $currency    = 'XOF';
        $payer       = '+22501234567';
        $callbackUrl = base_url('paiement/callback');
        
        // Appel de la méthode pour initier le paiement
        $response = $this->mesomb_api->initiatePayment($amount, $currency, $payer, $callbackUrl);
        
        echo '<pre>';
        print_r($response);
        echo '</pre>';
    }
    
    /**
     * Vérifier le statut d'une transaction.
     */
    public function statut() {
        $transactionId = $this->input->get('transaction_id');
        $response = $this->mesomb_api->checkPaymentStatus($transactionId);
        
        echo '<pre>';
        print_r($response);
        echo '</pre>';
    }
    
    /**
     * Rembourser une transaction.
     */
    public function rembourser() {
        $transactionId = $this->input->post('transaction_id');
        $amount = $this->input->post('amount');
        
        $response = $this->mesomb_api->refundPayment($transactionId, $amount);
        
        echo '<pre>';
        print_r($response);
        echo '</pre>';
    }
    
    /**
     * Annuler une transaction.
     */
    public function annuler() {
        $transactionId = $this->input->post('transaction_id');
        
        $response = $this->mesomb_api->cancelPayment($transactionId);
        
        echo '<pre>';
        print_r($response);
        echo '</pre>';
    }
    
    /**
     * Méthode de callback pour gérer la réponse de l'API.
     */
    public function callback() {
        $data = $this->input->post();
        
        // Traiter le callback (mise à jour en base, notification, etc.)
        echo "Callback reçu : ";
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
}
```

### Accès aux Endpoints

| Méthode                                                       | Endpoint                  | Description                                         |
|---------------------------------------------------------------|---------------------------|-----------------------------------------------------|
| **initiatePayment**                                           | `/paiement/initier`       | Initie un paiement mobile                           |
| **checkPaymentStatus**                                        | `/paiement/status`        | Vérifie le statut d'une transaction                |
| **refundPayment**                                             | `/paiement/remboursement` | Rembourse une transaction                            |
| **cancelPayment**                                             | `/paiement/annulation`    | Annule une transaction                               |

---


## Author

👤 **Hachther LLC <contact@hachther.com>**

* Website: https://www.hachther.com
* Twitter: [@hachther](https://twitter.com/hachther)
* Github: [@hachther](https://github.com/hachther)
* LinkedIn: [@hachther](https://linkedin.com/in/hachther)

## Show your support

Give a ⭐️ if this project helped you!