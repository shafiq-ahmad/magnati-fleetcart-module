<?php

namespace Modules\Magnati\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Magnati\Entities\MagnatiOrder;

class MagnatiPaymentCompleted
{
    use SerializesModels;

    /**
     * The Magnati order instance.
     *
     * @var \Modules\Magnati\Entities\MagnatiOrder
     */
    public $magnatiOrder;

    /**
     * Create a new event instance.
     *
     * @param \Modules\Magnati\Entities\MagnatiOrder $magnatiOrder
     * @return void
     */
    public function __construct(MagnatiOrder $magnatiOrder)
    {
        $this->magnatiOrder = $magnatiOrder;
    }
}
