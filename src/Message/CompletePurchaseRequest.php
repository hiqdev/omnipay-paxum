<?php

/*
 * Paxum plugin for PHP merchant library
 *
 * @link      https://github.com/hiqdev/omnipay-paxum
 * @package   omnipay-paxum
 * @license   MIT
 * @copyright Copyright (c) 2015, HiQDev (http://hiqdev.com/)
 */

namespace Omnipay\Paxum\Message;

/**
 * Paxum Complete Purchase Request.
 */
class CompletePurchaseRequest extends AbstractRequest
{
    /**
     * Get the data for this request.
     *
     * @return array request data
     */
    public function getData()
    {
        $this->validate('purse');

        return $this->httpRequest->request->all();
    }

    /**
     * Send the request with specified data.
     *
     * @param mixed $data The data to send
     *
     * @return CompletePurchaseResponse
     */
    public function sendData($data)
    {
        return $this->response = new CompletePurchaseResponse($this, $data);
    }
}
