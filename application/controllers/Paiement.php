<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use App\Libraries\Mesomb_api;

class Paiement extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Charger la bibliothèque Mesomb_api
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

        // Validation des entrées
        if (empty($amount) || empty($currency) || empty($payer)) {
            echo 'Les paramètres amount, currency et payer sont obligatoires.';
            return;
        }

        // Appel de la méthode qui génère la signature et initie le paiement
        $response = $this->mesomb_api->initiatePayment($amount, $currency, $payer, $callbackUrl);

        // Gestion des erreurs
        if (isset($response['error'])) {
            echo 'Erreur: ' . $response['error'];
        } else {
            echo '<pre>';
            print_r($response);
            echo '</pre>';
        }
    }

    /**
     * Vérifier le statut d'une transaction.
     */
    public function statut() {
        // Supposons que l'identifiant de transaction soit passé en GET (ex: /paiement/statut?transaction_id=12345)
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
        // Récupérer les données via POST (id de transaction et montant à rembourser)
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
        // Récupérer l'identifiant de la transaction à annuler via POST
        $transactionId = $this->input->post('transaction_id');

        $response = $this->mesomb_api->cancelPayment($transactionId);

        echo '<pre>';
        print_r($response);
        echo '</pre>';
    }

    /**
     * Méthode de callback pour gérer la réponse de l'API après traitement du paiement.
     */
    public function callback() {
        // Récupération des données envoyées par l'API (POST ou GET selon la configuration)
        $data = $this->input->post();

        // Vous pouvez ici ajouter la logique de vérification du callback, mise à jour en base, etc.
        echo "Callback reçu : ";
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
}
