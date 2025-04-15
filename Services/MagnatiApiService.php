<?php

namespace Modules\Magnati\Services;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Modules\Magnati\Entities\MagnatiOrder;

class MagnatiApiService
{
    protected $client;
    protected $baseUrl;
    protected $username;
    protected $password;
    protected $customer;
    protected $store;
    protected $terminal;
    protected $transactionHint;
    protected $testMode;

    /**
     * Create a new MagnatiApiService instance.
     */
    public function __construct()
    {
        $this->testMode = config('magnati.test_mode', true);
        
        $credentials = $this->testMode ? config('magnati.test') : config('magnati.production');
        
        $this->username = $credentials['username'];
        $this->password = $credentials['password'];
        $this->customer = $credentials['customer'];
        $this->store = $credentials['store'];
        $this->terminal = $credentials['terminal'];
        $this->transactionHint = $credentials['transaction_hint'];
        
        $this->baseUrl = $this->testMode 
            ? config('magnati.test_api_url', 'https://demo-ipg.ctdev.comtrust.ae:2443')
            : config('magnati.api_url', 'https://ipg.comtrust.ae:2443');
        
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => 30,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'verify' => false, // For testing only, should be true in production
        ]);
    }

    /**
     * Create a payment session with Magnati.
     *
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function createPaymentSession(array $data)
    {
        try {
            // Prepare the request payload
            $payload = [
                'Authentication' => [
                    'UserName' => $this->username,
                    'Password' => $this->password
                ],
                'Customer' => $this->customer,
                'Store' => $this->store,
                'Terminal' => $this->terminal,
                'Channel' => 'Web',
                'Amount' => $data['amount'],
                'Currency' => $data['currency'],
                'OrderID' => $data['order_id'],
                'OrderName' => 'Order #' . $data['order_id'],
                'OrderInfo' => 'Purchase from ' . config('app.name'),
                'TransactionHint' => $this->transactionHint,
                'ReturnPath' => $data['return_url'],
                'UserDefinedField1' => $data['customer_email'],
                'UserDefinedField2' => $data['customer_name'],
                'UserDefinedField3' => json_encode([
                    'billing_address' => $data['billing_address'],
                    'billing_city' => $data['billing_city'],
                    'billing_state' => $data['billing_state'],
                    'billing_zip' => $data['billing_zip'],
                    'billing_country' => $data['billing_country'],
                ]),
            ];
            
            // Make the API request
            $response = $this->client->post('/PaymentEx/MerchantPayment/Payment/InitiatePayment', [
                'json' => $payload,
            ]);
            
            // Parse the response
            $result = json_decode($response->getBody()->getContents(), true);
            
            // Check if the request was successful
            if (isset($result['Transaction']['ResponseCode']) && $result['Transaction']['ResponseCode'] === '0') {
                return [
                    'success' => true,
                    'payment_url' => $result['Transaction']['PaymentPage'],
                    'session_id' => $result['Transaction']['TransactionID'],
                ];
            }
            
            throw new Exception($result['Transaction']['ResponseDescription'] ?? 'Failed to create payment session');
        } catch (Exception $e) {
            Log::error('Magnati API Error: ' . $e->getMessage());
            throw new Exception('Failed to create payment session: ' . $e->getMessage());
        }
    }

    /**
     * Check the status of a payment.
     *
     * @param string $transactionId
     * @return array
     * @throws \Exception
     */
    public function checkPaymentStatus($transactionId)
    {
        try {
            // Prepare the request payload
            $payload = [
                'Authentication' => [
                    'UserName' => $this->username,
                    'Password' => $this->password
                ],
                'TransactionID' => $transactionId
            ];
            
            // Make the API request
            $response = $this->client->post('/PaymentEx/MerchantPayment/Payment/GetPaymentStatus', [
                'json' => $payload,
            ]);
            
            // Parse the response
            $result = json_decode($response->getBody()->getContents(), true);
            
            // Check if the request was successful
            if (isset($result['Transaction']['ResponseCode'])) {
                $isSuccess = $result['Transaction']['ResponseCode'] === '0';
                
                return [
                    'success' => $isSuccess,
                    'status' => $isSuccess ? 'success' : 'failed',
                    'transaction_id' => $result['Transaction']['TransactionID'] ?? null,
                    'amount' => $result['Transaction']['Amount'] ?? null,
                    'currency' => $result['Transaction']['Currency'] ?? null,
                    'response_code' => $result['Transaction']['ResponseCode'],
                    'response_description' => $result['Transaction']['ResponseDescription'] ?? null,
                ];
            }
            
            throw new Exception('Failed to check payment status');
        } catch (Exception $e) {
            Log::error('Magnati API Error: ' . $e->getMessage());
            throw new Exception('Failed to check payment status: ' . $e->getMessage());
        }
    }

    /**
     * Verify a payment callback.
     *
     * @param array $data
     * @return bool
     */
    public function verifyCallback(array $data)
    {
        // In a real implementation, we would verify the callback data
        // For now, we'll check if the transaction ID exists
        if (!isset($data['TransactionID'])) {
            return false;
        }
        
        try {
            // Check the payment status
            $status = $this->checkPaymentStatus($data['TransactionID']);
            
            // Verify that the response code matches
            return $status['response_code'] === $data['ResponseCode'];
        } catch (Exception $e) {
            Log::error('Magnati Callback Verification Error: ' . $e->getMessage());
            return false;
        }
    }
}
