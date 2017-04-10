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

class PurchaseRequestTest extends TestCase
{
    private $request;

    private $purse          = 'vip.vip@corporation.inc';
    private $secret         = '22SAD#-78G888';
    private $returnUrl      = 'https://www.foodstore.com/success';
    private $cancelUrl      = 'https://www.foodstore.com/failure';
    private $notifyUrl      = 'https://www.foodstore.com/notify';
    private $description    = 'Test Transaction long description';
    private $transactionId  = '12345ASD67890sd';
    private $amount         = '14.65';
    private $currency       = 'USD';
    private $testMode       = true;

    public function setUp()
    {
        parent::setUp();

        $this->request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize([
            'purse'         => $this->purse,
            'secret'        => $this->secret,
            'returnUrl'     => $this->returnUrl,
            'cancelUrl'     => $this->cancelUrl,
            'notifyUrl'     => $this->notifyUrl,
            'description'   => $this->description,
            'transactionId' => $this->transactionId,
            'amount'        => $this->amount,
            'currency'      => $this->currency,
            'testMode'      => $this->testMode,
        ]);
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame($this->purse,         $data['business_email']);
        $this->assertSame($this->returnUrl,     $data['finish_url']);
        $this->assertSame($this->cancelUrl,     $data['cancel_url']);
        $this->assertSame('notify_url=' . $this->notifyUrl, $data['variables']);
        $this->assertSame($this->description,   $data['item_name']);
        $this->assertSame($this->transactionId, $data['item_id']);
        $this->assertSame($this->amount,        $data['amount']);
        $this->assertSame($this->currency,      $data['currency']);
        $this->assertSame('ON',                 $data['sandbox']);
        $this->assertSame('00',                 $data['return']);
    }

    public function testSendData()
    {
        $data = $this->request->getData();
        $response = $this->request->sendData($data);
        $this->assertSame('Omnipay\Paxum\Message\PurchaseResponse', get_class($response));
    }
}
