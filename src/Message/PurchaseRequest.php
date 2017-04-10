<?php
/**
 * Paxum driver for PHP merchant library
 *
 * @link      https://github.com/hiqdev/omnipay-paxum
 * @package   omnipay-paxum
 * @license   MIT
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
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

        $res = [
            'business_email' => $this->getPurse(),
            'amount'         => $this->getAmount(),
            'currency'       => $this->getCurrency(),
            'item_name'      => $this->getDescription(),
            'finish_url'     => $this->getReturnUrl(),
            'cancel_url'     => $this->getCancelUrl(),
            'variables'      => 'notify_url=' . $this->getNotifyUrl(),
            'button_type_id' => 1, /// Pay Now button
        ];

        if ($this->getTransactionId()) {
            $res['item_id'] = $this->getTransactionId();
        }
        if ($this->getTestMode()) {
            $res['sandbox'] = 'ON';
            $res['return']  = '00';
        }

        return $res;
    }

    public function sendData($data)
    {
        return $this->response = new PurchaseResponse($this, $data);
    }
}
