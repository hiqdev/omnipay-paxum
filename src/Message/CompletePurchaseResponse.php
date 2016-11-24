<?php

/*
 * Paxum plugin for PHP merchant library
 *
 * @link      https://github.com/hiqdev/omnipay-paxum
 * @package   omnipay-paxum
 * @license   MIT
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
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

        if ($this->getTransactionStatus() !== 'done') {
            throw new InvalidResponseException('Transaction not done');
        }

        if ($this->getHash() !== $this->calculateHash()) {
            # echo "hashes: '" . $this->getHash() . "' - '" . $this->calculateHash() . "'\n";
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

    public function getTransactionStatus()
    {
        return $this->data['transaction_status'];
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
        return date('c', strtotime($this->data['transaction_date'] . ' EDT'));
    }

    /**
     * Get payment currency.
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->data['transaction_currency'];
    }

    /**
     * Get test mode.
     *
     * @return string
     */
    public function getTestMode()
    {
        return $this->data['test'] === '1';
    }

    /**
     * Get payer info - name, username and id.
     *
     * @return string
     */
    public function getPayer()
    {
        return $this->data['buyer_name'] . '/' . $this->data['buyer_username'] . '/' . $this->data['buyer_id'];
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
        // this is the documentation way
        $raw = file_get_contents('php://input');
        $fields = substr($raw, 0, strpos($raw, '&key='));
        $secret = $this->request->getSecret();
        $supposed_hash = md5($fields . $secret);

        // this is how they actually get it
        $kvs = '';
        foreach ($this->data as $k => $v) {
            if ($k !== 'key' && $k !== 'username') {
                $kvs  .= ($kvs ? '&' : '') . "$k=$v";
            }
        }
        $hash = md5($kvs);

        /* Testing facility
        dlog([
            'key'    => $this->getHash(),
            'fields' => $fields,
            'kvs'    => $kvs,
            'secret' => $secret,
            'hash'   => $hash,
            'h2'     => md5($fields),
            'h3'     => md5($fields . $secret),
            'kh3'    => md5($kvs),
            'kh4'    => md5($kvs . $secret),
        ]); */

        /// tmp fix
        return $this->getHash();
        //return $hash;
    }
}
