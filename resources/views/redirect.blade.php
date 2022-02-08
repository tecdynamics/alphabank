@extends('core/base::layouts.base')

@section ('page')
    @include('core/base::layouts.partials.svg-icon')

    <div class="page-wrapper bg-white">

        @include('core/base::layouts.partials.top-header')
    <div class="hh-checkout-redirecting pb-5">
    <div class="container">
        <div class="row mt-4 text-center w-100">
            <div class="col-8 mx-auto border rounded-3 p-4 mb-3 text-center">
                    <div class="row w-100 text-center p-4">
                        <img src="{!! $paymentObject::getLogo() !!}" class="img-fluid img-thumbnail h-25 mx-auto border-0" style="max-width: 300px"
                             alt="{!! $paymentObject::getName() !!}"/>
                    </div>
                    <div class="col-12 p-3 text-center w-100">
                        <i class="fab fa-cc-visa payment-icon fa-3x inline-block"></i>
                        <i class="fab fa-cc-mastercard payment-icon fa-3x inline-block"></i>
                        <i class="fab fa-cc-amex payment-icon fa-3x inline-block"></i>
                    </div>

                    <form id="paymentform" name="<?php echo $formname; ?>"
      method="POST" enctype="application/x-www-form-urlencoded"
      action="<?php echo $paymentObject->AlphabankUrl(); ?>" accept-charset="UTF-8">
    <input type="hidden" name="version" value="<?php echo $form_data_array['version']; ?>"/>
    <input type="hidden" name="mid" value="<?php echo $form_data_array['mid']; ?>"/>
    <input type="hidden" name="lang" value="<?php echo $form_data_array['lang']; ?>"/>
    <input type="hidden" name="deviceCategory" value="<?php echo $form_data_array['deviceCategory']; ?>"/>
    <input type="hidden" name="orderid" value="<?php echo $form_data_array['orderid']; ?>"/>
    <input type="hidden" name="orderDesc" value="<?php echo $form_data_array['orderDesc']; ?>"/>
    <input type="hidden" name="orderAmount" value="<?php echo $form_data_array['orderAmount']; ?>"/>
    <input type="hidden" name="currency" value="<?php echo $form_data_array['currency']; ?>"/>
    <input type="hidden" name="payerEmail" value="<?php echo $form_data_array['payerEmail']; ?>"/>
    <input type="hidden" name="billCountry" value="<?php echo $form_data_array['billCountry']; ?>"/>
    <input type="hidden" name="billZip" value="<?php echo $form_data_array['billZip']; ?>"/>
    <input type="hidden" name="billCity" value="<?php echo $form_data_array['billCity']; ?>"/>
    <input type="hidden" name="billAddress" value="<?php echo $form_data_array['billAddress']; ?>"/>
    <input type="hidden" name="trType" value="<?php echo $form_data_array['trType']; ?>"/>
    <?php

    if ($paymentObject::instalmentsisActive() && isset($form_data_array['extInstallmentperiod']) && $form_data_array['extInstallmentperiod'] > 1) { ?>
    <input type="hidden" name="extInstallmentoffset" value="<?php echo $form_data_array['extInstallmentoffset']; ?>"/>
    <input type="hidden" name="extInstallmentperiod" value="<?php echo $form_data_array['extInstallmentperiod']; ?>"/>
    <?php }
    ?>
    <input type="hidden" name="confirmUrl" value="<?php echo $form_data_array['confirmUrl']; ?>"/>
    <input type="hidden" name="cancelUrl" value="<?php echo $form_data_array['cancelUrl']; ?>"/>
    <input type="hidden" name="var1" value="<?php echo $form_data_array['var1']; ?>"/>
    <input type="hidden" name="digest"
           value="<?php echo $paymentObject->validate_eb_PayMerchantKey_field($form_data_array); ?>"/>
{{--    <button type="submit">Send</button>--}}
</form>

                    <div class="col-12 text-center p-2 ">
                        {{sprintf(__('Please wait we redirect you to %s secure payment'), $paymentObject::getName()) }}
                    </div>

            </div>
        </div>
    </div>

        @include('core/base::layouts.partials.footer')

    </div>
        <script>
            (function ($) {
                'use strict';
               setTimeout(function () {
                   $('form#paymentform').submit();
                }, 2000);


            })(jQuery);
        </script>

@stop
