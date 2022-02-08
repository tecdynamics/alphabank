<?php

namespace Botble\Alphabank\Providers;

use Botble\Alphabank\Services\Gateways\AlphabankPaymentService;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Enums\PaymentStatusEnum;
use Html;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use OrderHelper;
use Throwable;
use Assets;

class HookServiceProvider extends ServiceProvider {


    public function boot() {
        add_filter(PAYMENT_FILTER_ADDITIONAL_PAYMENT_METHODS, [$this, 'registerAlphabankMethod'], 19, 2);
        $this->app->booted(function () {
            add_filter(PAYMENT_FILTER_AFTER_POST_CHECKOUT, [$this, 'checkoutWithAlphabank'], 19, 2);
        });

        add_filter(PAYMENT_METHODS_SETTINGS_PAGE, [$this, 'addPaymentSettings'], 93);

        add_filter(BASE_FILTER_ENUM_ARRAY, function ($values, $class) {
            if ($class == PaymentMethodEnum::class) {
                $values['ALPHABANK'] = ALPHABANK_PAYMENT_METHOD_NAME;
            }

            return $values;
        }, 29, 2);

        add_filter(BASE_FILTER_ENUM_LABEL, function ($value, $class) {
            if ($class == PaymentMethodEnum::class && $value == ALPHABANK_PAYMENT_METHOD_NAME) {
                $value = 'Alphabank';
            }

            return $value;
        }, 29, 2);

        add_filter(BASE_FILTER_ENUM_HTML, function ($value, $class) {
            if ($class == PaymentMethodEnum::class && $value == ALPHABANK_PAYMENT_METHOD_NAME) {
                $value = Html::tag('span', PaymentMethodEnum::getLabel($value),
                    ['class' => 'label-success status-label'])
                    ->toHtml();
            }

            return $value;
        }, 29, 2);

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


    /**
     * @param string $html
     * @param array $data
     * @return string
     */
    public function registerAlphabankMethod($html, $data) {
        $order = OrderHelper::getOrderSessionData();
        $marketplace = $order['marketplace'] ?? false;
        if (!$marketplace) return $html;
        $marketplace = reset($marketplace);

        $Alphabank_key = get_payment_setting('client_id', ALPHABANK_PAYMENT_METHOD_NAME);
        $Alphabank_secret = get_payment_setting('secret', ALPHABANK_PAYMENT_METHOD_NAME);

        if (!$Alphabank_key || !$Alphabank_secret) {
            return $html;
        }
        $data['errorMessage'] = null;
        $data['orderId'] = Arr::get($marketplace, 'created_order_id', 0);
        $data['paymentId'] = Str::random(20);
      Assets::addScriptsDirectly(['vendor/core/plugins/alphabank/public/js/alphabank.js' ]);

        return $html . view('plugins/alphabank::paymentpage', $data)->render();
    }


    /**
     * @param Request $request
     * @param array $data
     * @return array
     */
    public function checkoutWithAlphabank(array $data, Request $request) {
        $order = OrderHelper::getOrderSessionData();
        $marketplace = $order['marketplace'] ?? false;

        if (!$marketplace) {
            $data['message'] = 'No Valid Order Provided1';
            $data['error'] = true;
            return $data;
        }
        $marketplace = reset($marketplace);

        $data['paymentObject'] = $this;
        $data['status'] = 'pending';
        $data['formname'] = OrderHelper::getOrderSessionToken();
        $data['form_data_array'] = $this->createForm($marketplace);
        $data['errorMessage'] = null;
        $data['orderId'] = Arr::get($marketplace, 'created_order_id', 0);
        $data['message'] = 'No Valid Order Provided';
        $data['error'] = false;
        return $data;
    }


}
