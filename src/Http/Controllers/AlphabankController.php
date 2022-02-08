<?php

namespace Botble\Alphabank\Http\Controllers;

use Assets;
use Botble\Alphabank\Services\Models\AlphabankModel;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\Order;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Supports\PaymentHelper;
use Doctrine\DBAL\Driver\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use OrderHelper;
use Throwable;

class AlphabankController extends BaseController {
    /**
     * @Function   paymentredirect
     * @param Request $request
     * @param BaseHttpResponse $response
     * @Author    : Michail Fragkiskos
     * @Created at: 08/02/2022 at 19:06
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return array|string
     */
    public function paymentredirect(Request $request, BaseHttpResponse $response) {
        $order = OrderHelper::getOrderSessionData();
        $order['installments'] = $request->input(['alphabankinstallments'],0);
        $order = OrderHelper::setOrderSessionData(OrderHelper::getOrderSessionToken(), $order);
        if (!$order) {
            $data['message'] = 'No Valid Order Provided';
            $data['error'] = true;
            return $data;
        }
        $marketplace = $order['marketplace'] ?? false;
        if ($marketplace) {
            $marketplace = reset($marketplace);
        }else{
            $marketplace= $order;
        }
        $AlphabankModel = new AlphabankModel();
        $data['paymentObject'] = $AlphabankModel;
        $data['status'] = 'pending';
        $data['formname'] = OrderHelper::getOrderSessionToken();
        $AlphabankModel = new AlphabankModel();
        if ($marketplace) {
            $data['orderId'] = Arr::get($marketplace, 'created_order_id', 0);
            $data['form_data_array'] = $AlphabankModel->createForm($marketplace);
        } else {
            $data['orderId'] = Arr::get($order, 'order_id', 0);
            $data['form_data_array'] = $AlphabankModel->createForm($order);
        }
        $data['errorMessage'] = null;

        Assets::addStylesDirectly(['css/vendors/bootstrap.min.css'])
            ->addStylesDirectly(['css/vendors/uicons-regular-straight.css'])
            ->addScriptsDirectly([
                'vendor/core/plugins/ecommerce/js/edit-product.js',
            ]);
        return view('plugins/alphabank::redirect', $data)->render();
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Throwable
     */
    public function paymentCallback(Request $request, BaseHttpResponse $response) {
        /* +request: Symfony\Component\HttpFoundation\InputBag {#46 ▼
    #parameters: array:13 [▼
      "version" => "2"
      "mid" => "0024077786"
      "orderid" => "51at20220206092816000000"
      "status" => "AUTHORIZED"
      "orderAmount" => "0.1"
      "currency" => "EUR"
      "paymentTotal" => "0.1"
      "message" => "OK, 00 - Approved"
      "riskScore" => "0"
      "payMethod" => "mastercard"
      "txId" => "92639547133311"
      "paymentRef" => "100386"
      "digest" => "Emta7v/M2D3MjjXnLfdi3VPUZIz9ExVQTrKyycZSlZQ="
    ]
  }
        array:13 [▼
  "version" => "2"
  "mid" => "0024077786"
  "orderid" => "51at20220206092816000000"
  "status" => "AUTHORIZED"
  "orderAmount" => "0.1"
  "currency" => "EUR"
  "paymentTotal" => "0.1"
  "message" => "OK, 00 - Approved"
  "riskScore" => "0"
  "payMethod" => "mastercard"
  "txId" => "92639547133311"
  "paymentRef" => "100386"
  "digest" => "Emta7v/M2D3MjjXnLfdi3VPUZIz9ExVQTrKyycZSlZQ="
]
array:3 [▼
  "gateway" => "Alphabank"
  "result" => "success"
  "id" => "51"
]*/
          $data = $_POST??[];
          $getData= $_GET??[];
//        $getData = [
//            "gateway" => "Alphabank",
//            "result" => "success",
//            "id" => "54"
//        ];
//        $data = [
//            "version" => "2",
//            "mid" => "90003092",
//            "orderid" => "54at20220207084145000000",
//            "status" => "AUTHORIZED",
//            "orderAmount" => "0.1",
//            "currency" => "EUR",
//            "paymentTotal" => "0.1",
//            "riskScore" => "0",
//            "payMethod" => "mastercard",
//            "txId" => "92639547138621",
//            "paymentRef" => "100041",
//            "digest" => "jt/1h0PDqxlBMxszZpBr62/ZkWATvol6oASiHtv8Wgg="];
        /*
         5188340000000011
         * */
        try {
            $AlphabankModel = new AlphabankModel();
            //check if is success or not the transaction;
            if (!isset($getData['result'], $data['status']) || !in_array(strtoupper($data['status']), [$AlphabankModel::_CAPTURED, $AlphabankModel::_AUTHORIZED])) {
                return $response
                    ->setError()
                    ->setNextUrl(PaymentHelper::getCancelURL())
                    ->setMessage(__('Error when processing payment via Alphabank!'));
            }

            if (!$AlphabankModel->validate_eb_PayMerchant_responce($data)) {
                return $response
                    ->setError()
                    ->setNextUrl(PaymentHelper::getCancelURL())
                    ->setMessage(__('Payment failed!'));
            }

            $orderInfo = explode('at', $data['orderid']);
            $order_id = (int)reset($orderInfo);
            $status = PaymentStatusEnum::PENDING;

            if (in_array(strtoupper($data['status']), [$AlphabankModel::_CAPTURED, $AlphabankModel::_AUTHORIZED])) {
                $status = PaymentStatusEnum::COMPLETED;
            }

            $order = Order::Where('id', '=', (int)$order_id)->first();
            //set historical data
            \DB::table('ec_order_histories')->insert([
                'action' => 'confirm_payment',
                'order_id' => (int)$order->id,
                'user_id' => (int)$order->user->id,
                'description' => $data['status'] ?? 'AlphaBank Checkout Error',
                'extras' => $data['paymentRef'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            do_action(PAYMENT_ACTION_PAYMENT_PROCESSED, [
                'amount' => $data['paymentTotal'] ?? 0,
                'currency' => $data['currency'] ?? 'EUR',
                'charge_id' => $data['txId'] ?? $data['orderid'],
                'payment_channel' => ALPHABANK_PAYMENT_METHOD_NAME,
                'status' => $status,
                'customer_id' =>(int) $order->user->id??0,
                'customer_type' => Customer::class,
                'payment_type' => $data['payMethod'] ?? 'Card',
                'order_id' => (int) $order->id,
            ]);

            return
            $response
                ->setNextUrl(route('public.checkout.success', $order->token))
                ->setMessage(__('Checkout successfully!'));

        }
        catch (Exception $x) {
            return $response
                ->setError()
                ->setNextUrl(PaymentHelper::getCancelURL())
                ->setMessage(__('Payment failed!'));
        }
    }


}
