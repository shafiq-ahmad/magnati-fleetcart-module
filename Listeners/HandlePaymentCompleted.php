<?php

namespace Modules\Magnati\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\Magnati\Events\MagnatiPaymentCompleted;
use Modules\Order\Entities\Order;

class HandlePaymentCompleted
{
    /**
     * Handle the event.
     *
     * @param \Modules\Magnati\Events\MagnatiPaymentCompleted $event
     * @return void
     */
    public function handle(MagnatiPaymentCompleted $event)
    {
        $magnatiOrder = $event->magnatiOrder;
        
        // Find the order
        $order = Order::find($magnatiOrder->order_id);
        
        if (! $order) {
            Log::error("Order not found for Magnati payment: {$magnatiOrder->id}");
            return;
        }
        
        // Update the order status based on the payment status
        if ($magnatiOrder->status === 'success') {
            $order->update(['status' => Order::PROCESSING]);
            
            // Additional order processing logic can be added here
            Log::info("Order {$order->id} has been processed successfully with Magnati payment");
        } else {
            Log::warning("Magnati payment for order {$order->id} has status: {$magnatiOrder->status}");
        }
    }
}
