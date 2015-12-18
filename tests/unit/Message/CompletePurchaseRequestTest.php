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

use Omnipay\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

class CompletePurchaseRequestTest extends TestCase
{
    private $request;

    private $purse          = 'vip.vip@corporation.inc';
    private $secret         = '*&^^&$%&(23';
    private $key            = '5e773067003449d29d5444c31f402100';
    private $description    = 'Test Transaction long description';
    private $transactionId  = '12345ASD67890sd';
    private $amount         = '1465.01';
    private $currency       = 'USD';
    private $testMode       = true;

    public function setUp()
    {
        parent::setUp();

        $httpRequest = new HttpRequest([], [
            'key'            => $this->key,
            'business_email' => $this->purse,
            'description'    => $this->description,
            'item_id'        => $this->transcationId,
            'amount'         => $this->amount,
            'currency'       => $this->currency,
            'sandbox'        => 'ON',
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

        $this->assertSame($this->key,           $data['key']);
        $this->assertSame($this->purse,         $data['business_email']);
        $this->assertSame($this->description,   $data['description']);
        $this->assertSame($this->transcationId, $data['item_id']);
        $this->assertSame($this->amount,        $data['amount']);
        $this->assertSame($this->currency,      $data['currency']);
    }

    public function testSendData()
    {
        $data = $this->request->getData();
        $response = $this->request->sendData($data);
        $this->assertSame('Omnipay\Paxum\Message\CompletePurchaseResponse', get_class($response));
    }
}
