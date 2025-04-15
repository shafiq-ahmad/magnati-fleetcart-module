<?php

namespace Modules\Magnati\Responses;

use Modules\Magnati\Entities\MagnatiOrder;

class MagnatiResponse
{
    private $redirectUrl;
    private $magnatiOrder;
    private $error;

    /**
     * Create a new MagnatiResponse instance.
     *
     * @param string|null $redirectUrl
     * @param \Modules\Magnati\Entities\MagnatiOrder|null $magnatiOrder
     * @param string|null $error
     */
    public function __construct($redirectUrl = null, $magnatiOrder = null, $error = null)
    {
        $this->redirectUrl = $redirectUrl;
        $this->magnatiOrder = $magnatiOrder;
        $this->error = $error;
    }

    /**
     * Check if the response is successful.
     *
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->error === null;
    }

    /**
     * Check if the response requires a redirect.
     *
     * @return bool
     */
    public function isRedirect()
    {
        return $this->redirectUrl !== null;
    }

    /**
     * Get the redirect URL.
     *
     * @return string|null
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * Get the Magnati order.
     *
     * @return \Modules\Magnati\Entities\MagnatiOrder|null
     */
    public function getMagnatiOrder()
    {
        return $this->magnatiOrder;
    }

    /**
     * Get the error message.
     *
     * @return string|null
     */
    public function getError()
    {
        return $this->error;
    }
}
