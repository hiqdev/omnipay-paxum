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

use Omnipay\Tests\TestCase;

class CompletePurchaseResponseTest extends TestCase
{
    private $request;

    private $purse                  = 'vip.vip@corporation.incorporated';
    private $secret                 = '22SAD#-78G8sdf$88';
    private $hash                   = 'cdfeb8cd1ecbf546a30bb7d658f4c1d2';
    private $description            = 'Test Transaction long description';
    private $transactionId          = '1SD672345A890sd';
    private $transactionReference   = 'sdfa1SD672345A8';
    private $status                 = 'done';
    private $amount                 = '0.01';
    private $payer                  = '//';
    private $time                   = '2015-12-12 12:12:12';
    private $currency               = 'USD';
    private $testMode               = true;

    public function setUp()
    {
        parent::setUp();

        $this->request = new CompletePurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize([
            'purse'     => $this->purse,
            'secret'    => $this->secret,
            'testMode'  => $this->testMode,
        ]);
    }

    public function testNotDoneException()
    {
        $this->setExpectedException('Omnipay\Common\Exception\InvalidResponseException', 'Transaction not done');
        new CompletePurchaseResponse($this->request, [
            'description'           => $this->description,
        ]);
    }

    public function testInvalidHashException()
    {
        $this->markTestSkipped('This test is disabled because of broken hash checking.');

        $this->setExpectedException('Omnipay\Common\Exception\InvalidResponseException', 'Invalid hash');
        new CompletePurchaseResponse($this->request, [
            'test'                  => '1',
            'description'           => $this->description,
            'transaction_status'    => $this->status,
            'key'                   => 'wrong',
        ]);
    }

    public function testInvalidTestModeException()
    {
        $this->setExpectedException('Omnipay\Common\Exception\InvalidResponseException', 'Invalid test mode');
        new CompletePurchaseResponse($this->request, [
            'description'           => $this->description,
            'transaction_status'    => $this->status,
            'key'                   => '9f72e6500b839e1198f6dadca7a100fa',
        ]);
    }

    public function testSuccess()
    {
        $response = new CompletePurchaseResponse($this->request, [
            'key'                   => $this->hash,
            'test'                  => '1',
            'description'           => $this->description,
            'item_id'               => $this->transactionId,
            'transaction_id'        => $this->transactionReference,
            'transaction_status'    => $this->status,
            'transaction_amount'    => $this->amount,
            'transaction_currency'  => $this->currency,
            'transaction_date'      => $this->time,
        ]);

        $this->assertTrue($response->isSuccessful());
        $this->assertTrue($response->getTestMode());
        $this->assertNull($response->getMessage());
        $this->assertNull($response->getCode());
        $this->assertSame($this->transactionId,         $response->getTransactionId());
        $this->assertSame($this->transactionReference,  $response->getTransactionReference());
        $this->assertSame($this->amount,                $response->getAmount());
        $this->assertSame($this->payer,                 $response->getPayer());
        $this->assertSame($this->hash,                  $response->getHash());
        $this->assertSame($this->currency,              $response->getCurrency());
        $this->assertSame(strtotime($this->time),       strtotime($response->getTime()) - 4 * 3600);
    }
}
