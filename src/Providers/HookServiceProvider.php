<?php

namespace Botble\Alphabank\Providers;

use Assets;
use Botble\Alphabank\Services\Gateways\AlphabankPaymentService;
use Botble\Alphabank\Services\Models\AlphabankModel;
use Botble\Payment\Enums\PaymentMethodEnum;
use Html;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use OrderHelper;
use Throwable;

class HookServiceProvider extends ServiceProvider {


    public function boot() {
        add_filter(PAYMENT_FILTER_ADDITIONAL_PAYMENT_METHODS, [$this, 'registerAlphabankMethod'], 99, 2);
//        $this->app->booted(function () {
//            add_filter(PAYMENT_FILTER_AFTER_POST_CHECKOUT, [$this, 'checkoutWithAlphabank'], 99, 2);
//      });

        add_filter(PAYMENT_METHODS_SETTINGS_PAGE, [$this, 'addPaymentSettings'], 30);

        add_filter(BASE_FILTER_ENUM_ARRAY, function ($values, $class) {
            if ($class == PaymentMethodEnum::class) {
                $values['ALPHABANK'] = ALPHABANK_PAYMENT_METHOD_NAME;
            }

            return $values;
        }, 30, 2);

        add_filter(BASE_FILTER_ENUM_LABEL, function ($value, $class) {
            if ($class == PaymentMethodEnum::class && $value == ALPHABANK_PAYMENT_METHOD_NAME) {
                $value = 'Alphabank';
            }

            return $value;
        }, 30, 2);

        add_filter(BASE_FILTER_ENUM_HTML, function ($value, $class) {
            if ($class == PaymentMethodEnum::class && $value == ALPHABANK_PAYMENT_METHOD_NAME) {
                $value = Html::tag('span', PaymentMethodEnum::getLabel($value),
                    ['class' => 'label-success status-label'])
                    ->toHtml();
            }

            return $value;
        }, 30, 2);

        add_filter(PAYMENT_FILTER_GET_SERVICE_CLASS, function ($data, $value) {
            if ($value == ALPHABANK_PAYMENT_METHOD_NAME) {
                $data = AlphabankPaymentService::class;
            }

            return $data;
        }, 20, 2);

        add_filter(PAYMENT_FILTER_PAYMENT_INFO_DETAIL, function ($data, $payment) {
              if ($payment->payment_channel == ALPHABANK_PAYMENT_METHOD_NAME) {
                $paymentService = new AlphabankPaymentService;
                $paymentDetail = $paymentService->getPaymentDetails($payment->charge_id);

                if ($paymentDetail) {
                    $data = view('plugins/alphabank::detail', ['payment' => $paymentDetail, 'paymentModel' => $payment])->render();
                }
            }

            return $data;
        }, 20, 2);

//        add_filter(PAYMENT_FILTER_GET_REFUND_DETAIL, function ($data, $payment, $refundId) {
//            if ($payment->payment_channel == Alphabank_PAYMENT_METHOD_NAME) {
//                $refundDetail = (new AlphabankPaymentService)->getRefundDetails($refundId);
//                if (!Arr::get($refundDetail, 'error')) {
//                    $refunds = Arr::get($payment->metadata, 'refunds', []);
//                    $refund = collect($refunds)->firstWhere('id', $refundId);
//                    $refund = array_merge((array) $refund, Arr::get($refundDetail, 'data', []));
//                    return array_merge($refundDetail, [
//                        'view' => view('plugins/Alphabank::refund-detail', ['refund' => $refund, 'paymentModel' => $payment])->render(),
//                    ]);
//                }
//                return $refundDetail;
//            }
//
//            return $data;
//        }, 20, 3);
    }

    /**
     * @param string $settings
     * @return string
     * @throws Throwable
     */
    public function addPaymentSettings($settings) {
          return $settings . view('plugins/alphabank::settings')->render();
    }
/*array:18 [▼
  "promotion_discount_amount" => 0
  "created_order" => true
  "created_order_id" => 60
  "created_order_product" => Illuminate\Support\Carbon @1644325742 {#3219 ▶}
  "shipping_method" => "default"
  "shipping_option" => "2"
  "shipping_amount" => "20.00"
  "name" => "Michail Fragkiskos"
  "email" => "support@tecdynamics.co.uk"
  "phone" => "+447507745608"
  "country" => "GB"
  "state" => "Berkshire"
  "city" => "READING"
  "address" => "25 Dwyer Road"
  "created_order_address" => true
  "created_order_address_id" => 57
  "order_id" => 60
  "marketplace" => array:2 [▶]
]
array:19 [▼
  "name" => "Michail Fragkiskos"
  "email" => "support@tecdynamics.co.uk"
  "phone" => "+447507745608"
  "country" => "GB"
  "state" => "Berkshire"
  "city" => "READING"
  "created_order" => Illuminate\Support\Carbon @1644326496 {#3209 ▶}
  "created_order_id" => 66
  "created_order_product" => Illuminate\Support\Carbon @1644326496 {#3209 ▶}
  "coupon_discount_amount" => 0
  "applied_coupon_code" => null
  "is_free_shipping" => false
  "promotion_discount_amount" => 0
  "shipping_method" => "default"
  "shipping_option" => 1
  "shipping_amount" => "0.00"
  "shipping" => array:1 [▶]
  "default_shipping_method" => "default"
  "default_shipping_option" => 1
]*/

    /**
     * @param string $html
     * @param array $data
     * @return string
     */
    public function registerAlphabankMethod($html, $data) {
        $order = OrderHelper::getOrderSessionData();
        $marketplace = $order['marketplace'] ?? false;
        if ($marketplace) {
            $marketplace = reset($marketplace);
        }
        Assets::addScripts('vendor/core/plugins/alphabank/js/alphabank.js');
        $Alphabank_key = get_payment_setting('client_id', ALPHABANK_PAYMENT_METHOD_NAME);
        $Alphabank_secret = get_payment_setting('secret', ALPHABANK_PAYMENT_METHOD_NAME);

        if (!$Alphabank_key || !$Alphabank_secret) {
            return $html;
        }
        $data['errorMessage'] = null;
        if (!$marketplace) {
            $data['orderId'] = Arr::get($order, 'order_id', 0);
        } else {
            $data['orderId'] = Arr::get($marketplace, 'created_order_id', 0);
        }
        $data['errorMessage'] = null;
        $data['paymentId'] = Str::random(20);
        return $html . view('plugins/alphabank::paymentpage', $data)->render();
    }


    /**
     * @param Request $request
     * @param array $data
     * @return array
     */
    public function checkoutWithAlphabank( $data, Request $request) {
         $alphabankinstallments= $request->input('alphabankinstallments',0);
        $order = OrderHelper::getOrderSessionData();

        $data=(array)$data;
        $order['installments'] = $alphabankinstallments;
        dd($order, OrderHelper::setOrderSessionData(OrderHelper::getOrderSessionToken(), $order));
        if (!$order) {
            $data['message'] = 'No Valid Order Provided';
            $data['error'] = true;
            return $data;
        }
        $marketplace = $order['marketplace'] ?? false;
        if ($marketplace) {
            $marketplace = reset($marketplace);
        }

        $data['installments'] = $alphabankinstallments;
        $data['paymentObject'] = $this;
        $data['status'] = 'pending';
        $data['formname'] = OrderHelper::getOrderSessionToken();
        $AlphabankModel= new AlphabankModel();
        if ($marketplace) {
            $data['orderId'] = Arr::get($marketplace, 'created_order_id', 0);
            $data['form_data_array'] = $AlphabankModel->createForm($marketplace);
        } else {
            $data['orderId'] = Arr::get($order, 'order_id', 0);
            $data['form_data_array'] = $AlphabankModel->createForm($order);
        }


        $data['errorMessage'] = null;
        $data['message'] = 'Redirecting to Payment gateway';
        $data['error'] = false;
        return $data;
    }


}
