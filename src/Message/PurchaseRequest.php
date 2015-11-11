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

class PurchaseRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate(
            'purse',
            'amount', 'currency', 'description',
            'returnUrl', 'cancelUrl', 'notifyUrl'
        );

        return [
            'item_name'      => $this->getDescription(),
            'business_email' => $this->getPurse(),
            'amount'         => $this->getAmount(),
            'currency'       => $this->getCurrency(),
            'finish_url'     => $this->getReturnUrl(),
            'cancel_url'     => $this->getCancelUrl(),
            'variables'      => $this->getVariables(),
            'button_type_id' => 1, /// Pay Now button
            //'sandbox'         => 'ON',
        ];
    }

    public function sendData($data)
    {
        return $this->response = new PurchaseResponse($this, $data);
    }
}
