<?php

use PHPUnit\Framework\TestCase;

// Define mock constants if not defined
if (!defined('BASEPATH')) define('BASEPATH', true);

// Define get_instance function if not defined
if (!function_exists('get_instance')) {
    function &get_instance() {
        return $GLOBALS['CI'];
    }
}

// Mock CI_Controller class
class CI_Controller {
    public $config;
    public $load;
}

// Mock load class
class CI_Loader {
    public function config() {}
}

// Mock CI_Config class
class CI_Config {
    public function item($key) {
        return 'https://mesomb.hachther.com/en/api/v1.1';
    }
}

class MesombApiTest extends TestCase {
    private $mesomb_api;
    private $ci;

    protected function setUp(): void {
        parent::setUp();
        
        // Create CI instance with required properties
        $this->ci = new CI_Controller();
        $this->ci->config = new CI_Config();
        $this->ci->load = new CI_Loader();
        
        // Set global CI instance BEFORE loading the Mesomb_api class
        $GLOBALS['CI'] = $this->ci;
        
        // Load Mesomb_api class
        require_once __DIR__ . '/../application/libraries/Mesomb_api.php';
        $this->mesomb_api = new Mesomb_api();
        
        // Set test environment variables
        putenv('MESOMB_API_KEY=test_key');
        putenv('MESOMB_API_SECRET=test_secret');
    }

    protected function tearDown(): void {
        parent::tearDown();
        putenv('MESOMB_API_KEY');
        putenv('MESOMB_API_SECRET');
        unset($GLOBALS['CI']);
    }

    public function testInitiatePayment(): void {
        $response = $this->mesomb_api->initiatePayment(1500.00, 'XOF', '+22501234567', 'http://votre-domaine/callback');
        $this->assertIsArray($response);
    }

    public function testCheckPaymentStatus(): void {
        $response = $this->mesomb_api->checkPaymentStatus('12345');
        $this->assertIsArray($response);
    }

    public function testRefundPayment(): void {
        $response = $this->mesomb_api->refundPayment('12345', 1500.00);
        $this->assertIsArray($response);
    }

    public function testCancelPayment(): void {
        $response = $this->mesomb_api->cancelPayment('12345');
        $this->assertIsArray($response);
    }
}