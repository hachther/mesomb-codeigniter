<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mesomb_api {

    protected $CI;
    protected $api_key;
    protected $api_secret;
    protected $api_url;

    public function __construct() {
        // Récupération de l'instance CodeIgniter
        $this->CI =& get_instance();
        // Chargement de la configuration
        $this->CI->load->config('mesomb');
        $this->api_key    = getenv('MESOMB_API_KEY');
        $this->api_secret = getenv('MESOMB_API_SECRET');
        $this->api_url    = $this->CI->config->item('mesomb_api_url');
    }

    /**
     * Générer une signature HMAC-SHA256 pour sécuriser les requêtes à l'API Mesomb.
     *
     * @param array  $params   Paramètres de la requête (clé => valeur).
     * @param string $method   Méthode HTTP utilisée (GET ou POST).
     * @param string $endpoint L'URL de l'endpoint sans le domaine (ex: "/payment/initiate").
     *
     * @return string Signature encodée en base64.
     */
    private function generateSignature($params, $method, $endpoint) {
        ksort($params); // Trier les paramètres par clé (ordre alphabétique)
        $paramString = http_build_query($params); // Construire la chaîne des paramètres
        $timestamp = time(); // Timestamp UNIX actuel
        $nonce = bin2hex(random_bytes(16)); // Générer un nonce unique

        // Construire la chaîne à signer
        $stringToSign = "$method\n$endpoint\n$paramString\n$timestamp\n$nonce";

        // Générer la signature HMAC-SHA256 avec la clé secrète
        $signature = hash_hmac('sha256', $stringToSign, $this->api_secret, true);

        // Retourner la signature encodée en base64
        return base64_encode($signature);
    }

    /**
     * Initier un paiement avec signature sécurisée.
     */
    public function initiatePayment($amount, $currency, $payer, $callbackUrl) {
        if (empty($amount) || empty($currency) || empty($payer)) {
            return ['error' => 'Les paramètres amount, currency et payer sont obligatoires.'];
        }

        $endpoint = "/payment/initiate";
        $params = [
            'api_key'      => $this->api_key,
            'amount'       => $amount,
            'currency'     => $currency,
            'payer'        => $payer,
            'callback_url' => $callbackUrl
        ];

        // Génération de la signature
        $params['signature'] = $this->generateSignature($params, 'POST', $endpoint);

        // Envoi de la requête
        $url = $this->api_url . $endpoint;
        return $this->sendRequest($url, $params);
    }

    /**
     * Vérifier le statut d'une transaction avec signature.
     */
    public function checkPaymentStatus($transactionId) {
        if (empty($transactionId)) {
            return ['error' => 'Le paramètre transaction_id est obligatoire.'];
        }

        $endpoint = "/payment/status";
        $params = [
            'api_key'        => $this->api_key,
            'transaction_id' => $transactionId
        ];

        // Génération de la signature
        $params['signature'] = $this->generateSignature($params, 'GET', $endpoint);

        $url = $this->api_url . $endpoint;
        return $this->sendRequest($url, $params, 'GET');
    }

    /**
     * Rembourser une transaction.
     */
    public function refundPayment($transactionId, $amount) {
        if (empty($transactionId) || empty($amount)) {
            return ['error' => 'Les paramètres transaction_id et amount sont obligatoires.'];
        }

        $endpoint = "/payment/refund";
        $params = [
            'api_key'        => $this->api_key,
            'transaction_id' => $transactionId,
            'amount'         => $amount
        ];

        // Génération de la signature
        $params['signature'] = $this->generateSignature($params, 'POST', $endpoint);

        $url = $this->api_url . $endpoint;
        return $this->sendRequest($url, $params);
    }

    /**
     * Annuler une transaction.
     */
    public function cancelPayment($transactionId) {
        if (empty($transactionId)) {
            return ['error' => 'Le paramètre transaction_id est obligatoire.'];
        }

        $endpoint = "/payment/cancel";
        $params = [
            'api_key'        => $this->api_key,
            'transaction_id' => $transactionId
        ];

        // Génération de la signature
        $params['signature'] = $this->generateSignature($params, 'POST', $endpoint);

        $url = $this->api_url . $endpoint;
        return $this->sendRequest($url, $params);
    }

    /**
     * Méthode générique pour envoyer une requête HTTP.
     */
    private function sendRequest($url, $params, $method = 'POST') {
        $ch = curl_init();

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        } else {
            curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($params));
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            return ['error' => $error_msg];
        }
        curl_close($ch);

        $decoded_response = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['error' => 'Invalid JSON response'];
        }

        if (isset($decoded_response['error'])) {
            return ['error' => $decoded_response['error']];
        }

        return $decoded_response;
    }
}