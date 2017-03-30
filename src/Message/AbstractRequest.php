<?php
/**
 * Paxum plugin for PHP merchant library.
 *
 * @link      https://github.com/hiqdev/omnipay-paxum
 * @package   omnipay-paxum
 * @license   MIT
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace Omnipay\Paxum\Message;

/**
 * Paxum Abstract Request.
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    protected $zeroAmountAllowed = false;

    /**
     * Get the merchant purse.
     *
     * @return string merchant purse - email associated with the merchant account
     */
    public function getPurse()
    {
        return $this->getParameter('purse');
    }

    /**
     * Set the purse.
     *
     * @param string $value merchant purse - email associated with the merchant account
     * @return self
     */
    public function setPurse($value)
    {
        return $this->setParameter('purse', $value);
    }

    /**
     * Get the merchant secret.
     *
     * @return string merchant secret - IPN shared secret which merchant gets by email from Paxum Merchant Services
     */
    public function getSecret()
    {
        return $this->getParameter('secret');
    }

    /**
     * Set the merchant secret.
     *
     * @param string $value merchant secret - IPN shared secret which merchant gets by email from Paxum Merchant Services
     * @return self
     */
    public function setSecret($value)
    {
        return $this->setParameter('secret', $value);
    }
}
