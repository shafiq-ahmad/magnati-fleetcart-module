<?php

namespace Modules\Magnati\Gateways;

use Exception;
use Modules\Order\Entities\Order;
use Modules\Payment\GatewayInterface;
use Modules\Magnati\Entities\MagnatiOrder;
use Modules\Magnati\Responses\MagnatiResponse;

class Magnati implements GatewayInterface
{
    public const gatewayCode = 'magnati';
    public $label;
    public $description;

    public function __construct()
    {
        $this->label = setting('magnati_label');
        $this->description = setting('magnati_description');
    }

    /**
     * Process the payment request.
     *
     * @param \Modules\Order\Entities\Order $order
     * @param \Illuminate\Http\Request $request
     * @return \Modules\Magnati\Responses\MagnatiResponse
     */
    public function purchase(Order $order, $request)
    {
        try {
            // Create invoice data
            $invoiceData = [
                'amount' => $order->total->convertToCurrentCurrency()->round()->amount(),
                'currency' => currency(),
                'order_id' => $order->id,
                'customer_name' => $order->customer_full_name,
                'customer_email' => $order->customer_email,
                'billing_address' => $order->billing_address_1,
                'billing_city' => $order->billing_city,
                'billing_state' => $order->billing_state,
                'billing_zip' => $order->billing_zip,
                'billing_country' => $order->billing_country,
                'return_url' => route('checkout.complete.store', ['orderId' => $order->id]),
                'cancel_url' => route('checkout.payment_canceled.store', ['orderId' => $order->id]),
            ];

            // Create a new Magnati order record
            $magnatiOrder = new MagnatiOrder();
            $magnatiOrder->fill([
                'order_id' => $order->id,
                'payment_method' => self::gatewayCode,
                'amount' => $invoiceData['amount'],
                'currency' => $invoiceData['currency'],
                'status' => 'pending',
            ]);
            $magnatiOrder->save();

            // In a real implementation, we would make an API call to Magnati here
            // For now, we'll simulate a redirect to the payment page
            $paymentUrl = $this->getMagnatiPaymentUrl($invoiceData);

            return new MagnatiResponse($paymentUrl, $magnatiOrder);
        } catch (Exception $e) {
            return new MagnatiResponse(null, null, $e->getMessage());
        }
    }

    /**
     * Get the Magnati payment URL by creating a payment session.
     *
     * @param array $data
     * @return string
     * @throws \Exception
     */
    private function getMagnatiPaymentUrl($data)
    {
        $apiService = app(Modules\Magnati\Services\MagnatiApiService::class);
        
        try {
            $result = $apiService->createPaymentSession($data);
            
            if ($result['success']) {
                return $result['payment_url'];
            }
            
            throw new Exception('Failed to create payment session');
        } catch (Exception $e) {
            throw new Exception('Magnati payment error: ' . $e->getMessage());
        }
    }
}
