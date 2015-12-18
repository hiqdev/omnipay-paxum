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

use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

/**
 * Paxum Complete Purchase Response.
 */
class CompletePurchaseResponse extends AbstractResponse
{
    /**
     * @param RequestInterface $request
     * @param array $data
     */
    public function __construct(RequestInterface $request, $data)
    {
        $this->request = $request;
        $this->data    = $data;

        if ($this->getHash() !== $this->calculateHash()) {
            throw new InvalidResponseException('Invalid hash');
        }

        if ($this->request->getTestMode() !== $this->getTestMode()) {
            throw new InvalidResponseException('Invalid test mode');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isSuccessful()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    public function getTransactionId()
    {
        return $this->data['item_id'];
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    public function getTransactionReference()
    {
        return $this->data['transaction_id'];
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    public function getAmount()
    {
        return $this->data['transaction_amount'];
    }

    /**
     * Get payment time.
     *
     * @return string
     */
    public function getTime()
    {
        return Helper::isotime($this->getVar('transaction_date') . ' EST');
    }

    /**
     * Get test mode.
     *
     * @return string
     */
    public function getTestMode()
    {
        return $this->data['sandbox'] === 'ON';
    }

    /**
     * Get payer info - name, username and id.
     *
     * @return string
     */
    public function getPayer()
    {
        return $this->getVar('buyer_name') . '/' . $this->getVar('buyer_username') . '/' . $this->getVar('buyer_id');
    }

    /**
     * Get hash from request.
     *
     * @return string
     */
    public function getHash()
    {
        return $this->data['key'];
    }

    /**
     * Calculate hash to validate incoming IPN notifications.
     *
     * @return string
     */
    public function calculateHash()
    {
        // raw POST request
        $raw = file_get_contents('php://input');
        // removing trailing '&key=...'
        $fields = substr($raw, 0, strpos($raw, "&key="));

        return md5($fields . $this->request->getSecret());
    }
}
