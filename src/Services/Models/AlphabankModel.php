<?php
namespace Botble\Alphabank\Services\Models;
use Illuminate\Support\Arr;
use OrderHelper;
use Botble\Ecommerce\Models\Order;
/**
 *  ****************************************************************
 *  *** DO NOT ALTER OR REMOVE COPYRIGHT NOTICES OR THIS HEADER. ***
 *  ****************************************************************
 *  Copyright © 2022 TEC-Dynamics LTD <support@tecdynamics.org>.
 *  All rights reserved.
 *  This software contains confidential proprietary information belonging
 *  to Tec-Dynamics Software Limited. No part of this information may be used, reproduced,
 *  or stored without prior written consent of Tec-Dynamics Software Limited.
 * @Author    : Michail Fragkiskos
 * @Created at: 06/02/2022 at 20:17
 * @Interface     : AlphabankModel
 * @Package   : tec
 */
class AlphabankModel {
    static $version = 2;
    const METHOD = 'aes-128-cbc';
    const _CAPTURED = 'CAPTURED';
    const _AUTHORIZED = 'AUTHORIZED';
    const _CANCELED = 'CANCELED';
    const _ERROR = 'ERROR';
    static $responses = [
        self::_CAPTURED => 'Payment was successful (accept order)',
        self::_AUTHORIZED => 'Payment was successful (accept order)',
        self::_CANCELED => 'Payment failed, user canceled the process (deny order) REFUSED Payment failed, payment was denied for card or by bank (deny order)',
        self::_ERROR => 'Non recoverable system or other error occurred during payment process (deny order)'
    ];

    /**
     * @Function  * @static    getLogo
     * @Author    : Michail Fragkiskos
     * @Created at: 08/01/2022 at 12:38
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\UrlGenerator|string|string[]
     */
    public static function getLogo() {
        return url('vendor/core/plugins/alphabank/images/Alphabank.png');
    }

    /**
     * @Function  * @static    transactionType
     * @Author    : Michail Fragkiskos
     * @Created at: 08/01/2022 at 12:38
     * @return bool
     */
    public static function transactionType() {
        // $enable = get_option(self::$paymentId . '_active_instalments_key');
        // get_option('enable_' . self::$paymentId . '_transactionType', 'off');
        return true;//!!($enable == 'on');
    }

    public static function getEnviroment() {
        //$enable = get_payment_setting('installments', Alphabank_PAYMENT_METHOD_NAME,false);// get_option('enable_' . self::$testpaymentId, 'off');
        return true;//($enable == false);
    }

    /**
     * @Function   AlphabankUrl
     * @Author    : Michail Fragkiskos
     * @Created at: 08/01/2022 at 12:38
     * @return string
     */
    public function AlphabankUrl() {
        if (!$this->getEnviroment()) {
            return "https://vpos.eurocommerce.gr/vpos/shophandlermpi";
        } else {
            return "https://alphaecommerce-test.cardlink.gr/vpos/shophandlermpi";
        }
    }

    /**
     * @Function  * @static    getName
     * @Author    : Michail Fragkiskos
     * @Created at: 08/01/2022 at 12:37
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public static function getName() {
        return __('Alphabank');
    }

    /**
     * @Function   getTransactionType
     * @Author    : Michail Fragkiskos
     * @Created at: 08/01/2022 at 12:38
     * @return int
     */
    protected function getTransactionType() {
        if ($this->transactionType()) {
            $trType = 2;
        } else {
            $trType = 1;
        }
        return $trType;
    }

    /**
     * @Function   setParams
     * @param $params
     * @Author    : Michail Fragkiskos
     * @Created at: 08/01/2022 at 12:38
     * @param $params
     */
    public function setParams($params) {

//        if (self::instalmentsisActive()) {
//            $totalInstalments = get_option(self::$paymentId . '_instalments_key');
//            $default['extInstallmentoffset'] = 0;
//            if ((int)request()->get("alphabankinstallments") > $totalInstalments) {
//                $extInstallmentperiod = (int)$totalInstalments;
//            } else {
//                $extInstallmentperiod = (int)request()->get("alphabankinstallments");
//            }
//
//            $default = [
//                'source' => request()->get(self::$paymentId . '_token'),
//                'total' => $this->convertPrice($this->orderObject->total, $this->orderObject->currency),
//                'currency' => $this->getCurrencyUnit($this->orderObject->currency),
//                'email' => $this->orderObject->email,
//                'name' => $this->orderObject->first_name . ' ' . $this->orderObject->last_name,
//                'phone' => $this->orderObject->phone,
//                "firstName" => request()->get("firstName"),
//                "lastName" => request()->get("lastName"),
//                "address" => request()->get("address"),
//                "city" => request()->get("city"),
//                "country" => request()->get("country"),
//                "postCode" => request()->get("postCode"),
//                "note" => request()->get("note"),
//                "payment" => request()->get("payment"),
//                "alphabank_token" => request()->get('alphabank_token'),
//                "term_condition" => request()->get("term_condition"),
//                "extInstallmentoffset" => 0,
//                "extInstallmentperiod" => (int)$extInstallmentperiod
//            ];
//        } else {
//            $default = [
//                'source' => request()->get(self::$paymentId . '_token'),
//                'total' => $this->convertPrice($this->orderObject->total, $this->orderObject->currency),
//                'currency' => $this->getCurrencyUnit($this->orderObject->currency),
//                'email' => $this->orderObject->email,
//                'name' => $this->orderObject->first_name . ' ' . $this->orderObject->last_name,
//                'phone' => $this->orderObject->phone,
//                "firstName" => request()->get("firstName"),
//                "lastName" => request()->get("lastName"),
//                "address" => request()->get("address"),
//                "city" => request()->get("city"),
//                "country" => request()->get("country"),
//                "postCode" => request()->get("postCode"),
//                "note" => request()->get("note"),
//                "payment" => request()->get("payment"),
//                "alphabank_token" => request()->get('alphabank_token'),
//                "term_condition" => request()->get("term_condition"),
//            ];
//        }
//        $this->params = wp_parse_args($params, $default);
//        session(['order_params' => $this->params]);
    }

    /**
     * @Function   getParams
     * @Author    : Michail Fragkiskos
     * @Created at: 08/01/2022 at 12:38#
     */
    public function getParams() {

        $this->params = session('order_params');

    }

    public function redirect_bank_response_url() {
        return url('alphabank/payment/callback');
    }

    /**
     * @Function  * @static    instalmentsisActive
     * @Author    : Michail Fragkiskos
     * @Created at: 08/01/2022 at 12:37
     * @return bool
     */
    public static function instalmentsisActive() {
        $enable = get_payment_setting('installments', ALPHABANK_PAYMENT_METHOD_NAME, 0);

        return ((int)$enable > 1);
    }

    /**
     * @Function   createForm
     * @param $orderID
     * @param $form_data_array
     * @Author    : Michail Fragkiskos
     * @Created at: 06/01/2022 at 16:35
     * @param $orderID
     * @param $form_data_array
     */
    public function createForm($order) {
        $Alphabank_key = get_payment_setting('client_id', ALPHABANK_PAYMENT_METHOD_NAME);
        $Alphabank_secret = get_payment_setting('secret', ALPHABANK_PAYMENT_METHOD_NAME);
        $Alphabank_installments = get_payment_setting('installments', ALPHABANK_PAYMENT_METHOD_NAME);
        if (!$Alphabank_key || !$Alphabank_secret) {
            return false;
        }
        $this->getParams();
        if ($this->getEnviroment()) {
            $total = '0.10';
        } else {
            $total = $this->params['total'] ?? '0.0';
        }
        if (self::instalmentsisActive() && isset($order['installments']) && $order['installments'] > 1) {
            $extInstallmentoffset = 0;
            if ((int)$order["installments"] > $Alphabank_installments) {
                 $extInstallmentperiod = (int)$Alphabank_installments;
             } else {
                $extInstallmentperiod = (int)$order["installments"];
             }
             \DB::table('ec_orders')->where('id', '=', Arr::get($order, 'created_order_id', 0))->update(['installments'=> $extInstallmentperiod]);
            $form_data_array = array(
                'version' => self::$version,
                'mid' => $Alphabank_key,
                'lang' => 'EL',//get_current_language(),
                $Alphabank_installments,
                'deviceCategory' => 0,
                'orderid' => Arr::get($order, 'created_order_id', 0) . 'at' . date('Ymdhisu'),
                'orderDesc' => 'Order #' . Arr::get($order, 'created_order_id', 0),
                'orderAmount' => $total,
                'currency' => $this->params['currency'] ?? 'EUR',
                'payerEmail' => $this->params['email'] ?? 'N/A',
                'billCountry' => $this->params['country'] ?? 'GR',
                'billZip' => $this->params['postCode'] ?? 'N/A',
                'billCity' => $this->params['city'] ?? 'N/A',
                'billAddress' => $this->params['address'] ?? 'N/A',
                'trType' => $this->getTransactionType(),
                'extInstallmentoffset' => $extInstallmentoffset,
                'extInstallmentperiod' => $extInstallmentperiod,
                'confirmUrl' => url($this->redirect_bank_response_url() . "/?gateway=" . self::getName() . "&result=success"),
                'cancelUrl' => url($this->redirect_bank_response_url() . "/?gateway=" . self::getName() . "&result=failure" ),
                'var1' => Arr::get($order, 'created_order_id', 0)
            );
        } else {
            $form_data_array = array(
                'version' => self::$version,
                'mid' => $Alphabank_key,
                'lang' => 'EL',// get_current_language(),
                'deviceCategory' => 0,
                'orderid' => Arr::get($order, 'created_order_id', 0) . 'at' . date('Ymdhisu'),
                'orderDesc' => 'Order #' . Arr::get($order, 'created_order_id', 0),
                'orderAmount' => $total,
                'currency' => $this->params['currency'] ?? 'EUR',
                'payerEmail' => $this->params['email'] ?? 'N/A',
                'billCountry' => $this->params['country'] ?? 'GR',
                'billZip' => $this->params['postCode'] ?? 'N/A',
                'billCity' => $this->params['city'] ?? 'N/A',
                'billAddress' => $this->params['address'] ?? 'N/A',
                'trType' => $this->getTransactionType(),
                'confirmUrl' => url($this->redirect_bank_response_url() . "/?gateway=" . self::getName() . "&result=success&id=". Arr::get($order, 'created_order_id', 0)),
                'cancelUrl' => url($this->redirect_bank_response_url() . "/?gateway=" . self::getName(). "&result=failure&id=" . Arr::get($order, 'created_order_id', 0)),
                'var1' => Arr::get($order, 'created_order_id', 0)
            );
        }

        $form_data_array = array_map('trim', $form_data_array);
        return $form_data_array;

    }

    /*Encryption*/

    public function validate_eb_PayMerchantKey_field($array) {
        $secret = get_payment_setting('secret', ALPHABANK_PAYMENT_METHOD_NAME);
        $form_data = '';
        foreach ($array as $k => $v) {
            if (!in_array($k, array('_charset_', 'digest', 'submitButton'))) {
                $form_data .= filter_var($array[$k], FILTER_SANITIZE_STRING);
            }
        }

        $form_data .= $secret;
        $digest = base64_encode(hash('sha256', ($form_data), true));
        return $digest;
    }
    public function validate_eb_PayMerchant_responce($array) {
        if(!isset($array['digest'])) return false;
        $existingdigest= $array['digest'];
        unset($array['digest']);
        $secret = get_payment_setting('secret', ALPHABANK_PAYMENT_METHOD_NAME);
        $form_data = '';
        foreach ($array as $k => $v) {
            if (!in_array($k, array('_charset_', 'digest', 'submitButton'))) {
                $form_data .= filter_var($array[$k], FILTER_SANITIZE_STRING);
            }
        }
        $form_data .= $secret;
        $digest = base64_encode(hash('sha256', ($form_data), true));
        return ($digest==$existingdigest);
    }


}
