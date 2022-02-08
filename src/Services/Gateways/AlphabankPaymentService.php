<?php

namespace Botble\Alphabank\Services\Gateways;

use Botble\Alphabank\Services\Abstracts\AlphabankPaymentAbstract;
use Exception;
use Illuminate\Http\Request;

class AlphabankPaymentService extends AlphabankPaymentAbstract
{
    /**
     * Make a payment
     *
     * @param Request $request
     *
     * @return mixed
     * @throws Exception
     */
    public function makePayment(Request $request)
    {
    }

    /**
     * Use this function to perform more logic after user has made a payment
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function afterMakePayment(Request $request)
    {
    }
}
