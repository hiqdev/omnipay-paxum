<?php

/*
 * Paxum plugin for PHP merchant library
 *
 * @link      https://github.com/hiqdev/php-merchant-paxum
 * @package   php-merchant-paxum
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\paxum;

class Merchant extends \hiqdev\php\merchant\Merchant
{
    protected static $_defaults = [
        'name'        => 'paxum',
        'label'       => 'Paxum',
        'actionUrl'   => 'https://paxum.com/payment/phrame.php?action=displayProcessPaymentLogin',
        'confirmText' => 'OK',
    ];

    public function getVariables()
    {
        return 'notify_url=' . $this->confirmUrl;
    }

    public function getInputs()
    {
        return [
            'item_name'      => $this->description,
            'business_email' => $this->purse,
            'amount'         => $this->total,
            'currency'       => $this->currency,
            'finish_url'     => $this->successUrl,
            'cancel_url'     => $this->failureUrl,
            'variables'      => $this->variables,
            'button_type_id' => 1, /// Pay Now button
            //'sandbox'         => 'ON',
        ];
    }

    public function validateConfirmation($data)
    {
        $sum = $this->checkMoney($data['transaction_amount']);
        if (!$sum) {
            return 'No sum';
        }
        if ($data['transaction_status'] !== 'done') {
            return 'Not done';
        }
        $skips = ['key' => 1, 'ip' => 1, 'auth_login' => 1, 'auth_password' => 1];
        foreach ($data as $k => $v) {
            if (!$skips[$k]) {
                $str .= "&$k=$v";
            }
        }
        if (md5($str) !== strtolower($data['key'])) {
            return 'Wrong hash';
        }
        $this->mset([
            'from' => $data['buyer_name'] . '/' . $data['buyer_username'] . '/' . $data['buyer_id'],
            'txn'  => $data['transaction_id'],
            'sum'  => $sum,
            'time' => date('c', strtotime($data['transaction_date'] . ' EST')),
        ]);

        return;
    }
}
