<?php

namespace Modules\Magnati\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Order\Entities\Order;
use Modules\Magnati\Entities\MagnatiOrder;

class MagnatiController extends Controller
{
    /**
     * Handle the payment callback from Magnati.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function callback(Request $request)
    {
        // Get the transaction details from the request
        $transactionId = $request->input('TransactionID');
        $responseCode = $request->input('ResponseCode');
        $responseDescription = $request->input('ResponseDescription');
        $orderId = $request->input('OrderID');
        
        // Find the order
        $order = Order::findOrFail($orderId);
        
        // Find the Magnati order
        $magnatiOrder = MagnatiOrder::where('order_id', $orderId)->firstOrFail();
        
        // Update the Magnati order with the transaction details
        $magnatiOrder->transaction_id = $transactionId;
        $magnatiOrder->status = $responseCode === '0' ? 'success' : 'failed';
        $magnatiOrder->transaction_data = $request->all();
        $magnatiOrder->save();
        
        // Update the order status based on the payment status
        if ($responseCode === '0') {
            $order->update(['status' => Order::PROCESSING]);
            
            return redirect()->route('checkout.complete.show')
                ->with('success', trans('checkout::messages.payment_success'));
        }
        
        return redirect()->route('checkout.payment_canceled.store', ['orderId' => $orderId])
            ->with('error', $responseDescription ?? trans('magnati::magnati.messages.payment_failed'));
    }
    
    /**
     * Handle the webhook notifications from Magnati.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function webhook(Request $request)
    {
        // Verify the webhook signature
        if (!$this->verifyWebhookSignature($request)) {
            return response('Invalid signature', 400);
        }
        
        // Get the data from the webhook
        $orderId = $request->input('order_id');
        $transactionId = $request->input('transaction_id');
        $status = $request->input('status');
        
        // Find the Magnati order
        $magnatiOrder = MagnatiOrder::where('order_id', $orderId)->first();
        
        if (!$magnatiOrder) {
            return response('Order not found', 404);
        }
        
        // Update the Magnati order with the transaction details
        $magnatiOrder->transaction_id = $transactionId;
        $magnatiOrder->status = $status;
        $magnatiOrder->transaction_data = $request->all();
        $magnatiOrder->save();
        
        // Update the order status based on the payment status
        if ($status === 'success') {
            $order = Order::find($orderId);
            if ($order) {
                $order->update(['status' => Order::PROCESSING]);
            }
        }
        
        return response('Webhook processed', 200);
    }
    
    /**
     * Verify the webhook signature.
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    private function verifyWebhookSignature(Request $request)
    {
        $apiService = app(Modules\Magnati\Services\MagnatiApiService::class);
        return $apiService->verifyCallback($request->all());
    }
}
