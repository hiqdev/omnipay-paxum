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

use Omnipay\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

class CompletePurchaseRequestTest extends TestCase
{
    private $request;

    private $purse                  = 'vip.vip@corporation.inc';
    private $secret                 = '*&^^&$%&(23';
    private $key                    = 'c444d2589a171dadf98c72cb412ffc23';
    private $description            = 'Test Transaction long description';
    private $transactionId          = '12345ASD67890sd';
    private $transactionReference   = '12345678';
    private $amount                 = '1,465.01';
    private $status                 = 'done';
    private $currency               = 'USD';
    private $testMode               = false;

    public function setUp()
    {
        parent::setUp();

        $httpRequest = new HttpRequest([], [
            'transaction_item_name'     => $this->description,
            'transaction_item_id'       => $this->transactionId,
            'transaction_amount'        => $this->amount,
            'transaction_status'        => $this->status,
            'transaction_currency'      => $this->currency,
            'test'                      => (int) $this->testMode,
            'key'                       => $this->key,
        ]);

        $this->request = new CompletePurchaseRequest($this->getHttpClient(), $httpRequest);
        $this->request->initialize([
            'purse'     => $this->purse,
            'secret'    => $this->secret,
            'testMode'  => $this->testMode,
        ]);
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame($this->description,   $data['transaction_item_name']);
        $this->assertSame($this->transactionId, $data['transaction_item_id']);
        $this->assertSame($this->amount,        $data['transaction_amount']);
        $this->assertSame($this->status,        $data['transaction_status']);
        $this->assertSame($this->currency,      $data['transaction_currency']);
        $this->assertSame($this->key,           $data['key']);
    }

    public function testSendData()
    {
        $data = $this->request->getData();
        $response = $this->request->sendData($data);
        $this->assertInstanceOf(\Omnipay\Paxum\Message\CompletePurchaseResponse::class, $response);
    }
}
