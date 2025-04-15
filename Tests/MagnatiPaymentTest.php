<?php

namespace Modules\Magnati\Tests;

use Tests\TestCase;
use Modules\Order\Entities\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Magnati\Gateways\Magnati;
use Modules\Magnati\Services\MagnatiApiService;
use Illuminate\Http\Request;
use Mockery;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Middleware;

class MagnatiPaymentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the Magnati payment gateway purchase method.
     *
     * @return void
     */
    public function testPurchaseMethod()
    {
        // Create a mock response for the InitiatePayment API call
        $mockResponse = [
            'Transaction' => [
                'ResponseCode' => '0',
                'ResponseDescription' => 'Success',
                'TransactionID' => 'test-transaction-id',
                'PaymentPage' => 'https://demo-ipg.ctdev.comtrust.ae:2443/payment-page'
            ]
        ];

        // Create a mock HTTP client
        $mock = new MockHandler([
            new Response(200, [], json_encode($mockResponse))
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        // Mock the MagnatiApiService
        $apiServiceMock = Mockery::mock(MagnatiApiService::class);
        $apiServiceMock->shouldReceive('createPaymentSession')
            ->once()
            ->andReturn([
                'success' => true,
                'payment_url' => 'https://demo-ipg.ctdev.comtrust.ae:2443/payment-page',
                'session_id' => 'test-transaction-id',
            ]);
        
        // Bind the mock to the container
        $this->app->instance(MagnatiApiService::class, $apiServiceMock);
        
        // Create a test order
        $order = factory(Order::class)->create([
            'customer_email' => 'test@example.com',
            'customer_first_name' => 'Test',
            'customer_last_name' => 'User',
            'billing_address_1' => '123 Test St',
            'billing_city' => 'Test City',
            'billing_state' => 'Test State',
            'billing_zip' => '12345',
            'billing_country' => 'US',
        ]);
        
        // Create a request
        $request = new Request();
        
        // Create the Magnati gateway
        $gateway = new Magnati();
        
        // Call the purchase method
        $response = $gateway->purchase($order, $request);
        
        // Assert the response is a redirect
        $this->assertTrue($response->isRedirect());
        
        // Assert the redirect URL is correct
        $this->assertEquals('https://demo-ipg.ctdev.comtrust.ae:2443/payment-page', $response->getRedirectUrl());
    }

    /**
     * Test the callback handling with a successful payment.
     *
     * @return void
     */
    public function testSuccessfulCallbackHandling()
    {
        // Create a mock response for the GetPaymentStatus API call
        $mockResponse = [
            'Transaction' => [
                'ResponseCode' => '0',
                'ResponseDescription' => 'Success',
                'TransactionID' => 'test-transaction-id',
                'Amount' => '100.00',
                'Currency' => 'AED'
            ]
        ];

        // Mock the MagnatiApiService
        $apiServiceMock = Mockery::mock(MagnatiApiService::class);
        $apiServiceMock->shouldReceive('verifyCallback')
            ->once()
            ->andReturn(true);
        
        // Bind the mock to the container
        $this->app->instance(MagnatiApiService::class, $apiServiceMock);
        
        // Create a test order
        $order = factory(Order::class)->create();
        
        // Create a Magnati order
        $magnatiOrder = factory(Modules\Magnati\Entities\MagnatiOrder::class)->create([
            'order_id' => $order->id,
            'status' => 'pending',
        ]);
        
        // Create a request with callback data
        $request = new Request([
            'TransactionID' => 'test-transaction-id',
            'ResponseCode' => '0',
            'ResponseDescription' => 'Success',
            'OrderID' => $order->id,
        ]);
        
        // Call the callback route
        $response = $this->get(route('magnati.callback', $request->all()));
        
        // Assert the response is a redirect to the checkout complete page
        $response->assertRedirect(route('checkout.complete.show'));
        
        // Refresh the order from the database
        $order->refresh();
        
        // Assert the order status is updated
        $this->assertEquals(Order::PROCESSING, $order->status);
    }

    /**
     * Test the callback handling with a failed payment.
     *
     * @return void
     */
    public function testFailedCallbackHandling()
    {
        // Create a mock response for the GetPaymentStatus API call
        $mockResponse = [
            'Transaction' => [
                'ResponseCode' => '51',
                'ResponseDescription' => 'Not Sufficient Funds',
                'TransactionID' => 'test-transaction-id',
                'Amount' => '100.00',
                'Currency' => 'AED'
            ]
        ];

        // Mock the MagnatiApiService
        $apiServiceMock = Mockery::mock(MagnatiApiService::class);
        $apiServiceMock->shouldReceive('verifyCallback')
            ->once()
            ->andReturn(true);
        
        // Bind the mock to the container
        $this->app->instance(MagnatiApiService::class, $apiServiceMock);
        
        // Create a test order
        $order = factory(Order::class)->create();
        
        // Create a Magnati order
        $magnatiOrder = factory(Modules\Magnati\Entities\MagnatiOrder::class)->create([
            'order_id' => $order->id,
            'status' => 'pending',
        ]);
        
        // Create a request with callback data
        $request = new Request([
            'TransactionID' => 'test-transaction-id',
            'ResponseCode' => '51',
            'ResponseDescription' => 'Not Sufficient Funds',
            'OrderID' => $order->id,
        ]);
        
        // Call the callback route
        $response = $this->get(route('magnati.callback', $request->all()));
        
        // Assert the response is a redirect to the payment canceled page
        $response->assertRedirect(route('checkout.payment_canceled.store', ['orderId' => $order->id]));
    }

    /**
     * Test the payment status check.
     *
     * @return void
     */
    public function testPaymentStatusCheck()
    {
        // Create a mock response for the GetPaymentStatus API call
        $mockResponse = [
            'Transaction' => [
                'ResponseCode' => '0',
                'ResponseDescription' => 'Success',
                'TransactionID' => 'test-transaction-id',
                'Amount' => '100.00',
                'Currency' => 'AED'
            ]
        ];

        // Mock the MagnatiApiService
        $apiServiceMock = Mockery::mock(MagnatiApiService::class);
        $apiServiceMock->shouldReceive('checkPaymentStatus')
            ->once()
            ->with('test-transaction-id')
            ->andReturn([
                'success' => true,
                'status' => 'success',
                'transaction_id' => 'test-transaction-id',
                'amount' => '100.00',
                'currency' => 'AED',
                'response_code' => '0',
                'response_description' => 'Success',
            ]);
        
        // Bind the mock to the container
        $this->app->instance(MagnatiApiService::class, $apiServiceMock);
        
        // Create a test order
        $order = factory(Order::class)->create();
        
        // Create a Magnati order
        $magnatiOrder = factory(Modules\Magnati\Entities\MagnatiOrder::class)->create([
            'order_id' => $order->id,
            'status' => 'pending',
            'transaction_id' => 'test-transaction-id',
        ]);
        
        // Call the service directly to check the status
        $apiService = app(MagnatiApiService::class);
        $result = $apiService->checkPaymentStatus('test-transaction-id');
        
        // Assert the result is successful
        $this->assertTrue($result['success']);
        $this->assertEquals('success', $result['status']);
        $this->assertEquals('test-transaction-id', $result['transaction_id']);
    }
}
