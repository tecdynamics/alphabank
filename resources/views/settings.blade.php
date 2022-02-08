@php $alphabankStatus= get_payment_setting('status',ALPHABANK_PAYMENT_METHOD_NAME); @endphp
<table class="table payment-method-item">
    <tbody>
    <tr class="border-pay-row">
        <td class="border-pay-col"><i class="fa fa-theme-payments"></i></td>
        <td style="width: 20%;">
            <img class="filter-black" src="{{ url('vendor/core/plugins/alphabank/images/alphabank.png') }}" alt="Alphabank">
        </td>
        <td class="border-right">
            <ul>
                <li>
                    <a href="https://cardlink.gr/" target="_blank">Alphabank</a>
                    <p>{{ clean(trans('plugins/alphabank::alphabank.alphabank_description'))  }}</p>
                </li>
            </ul>
        </td>
    </tr>
    <tr class="bg-white">
        <td colspan="3">
            <div class="float-start" style="margin-top: 5px;">
                <div class="payment-name-label-group @if ($alphabankStatus== 0) hidden @endif">
                    <span class="payment-note v-a-t">{{ clean(trans('plugins/alphabank::alphabank.use')) }}:</span> <label
                        class="ws-nm inline-display method-name-label">{{ setting('payment_alphabank_name') }}</label>
                </div>
            </div>
            <div class="float-end">
                <a class="btn btn-secondary toggle-payment-item edit-payment-item-btn-trigger @if ($alphabankStatus== 0) hidden @endif">{{ trans('plugins/payment::payment.edit') }}</a>
                <a class="btn btn-secondary toggle-payment-item save-payment-item-btn-trigger @if ($alphabankStatus== 1) hidden @endif">{{ trans('plugins/payment::payment.settings') }}</a>
            </div>
        </td>
    </tr>
    <tr class="paypal-online-payment payment-content-item hidden">
        <td class="border-left" colspan="3">
            {!! Form::open() !!}
            {!! Form::hidden('type', ALPHABANK_PAYMENT_METHOD_NAME, ['class' => 'payment_type']) !!}
            <div class="row">
                <div class="col-sm-6">
                    <ul>
                        <li>
                            <label>{{ trans('plugins/payment::payment.configuration_instruction', ['name' => 'Alphabank']) }}</label>
                        </li>
                        <li class="payment-note">
                            <p>{{ trans('plugins/payment::payment.configuration_requirement', ['name' => 'Alphabank']) }}
                                :</p>
                            <ul class="m-md-l" style="list-style-type:decimal">
                                <li style="list-style-type:decimal">
                                    <a href="https://cardlink.gr/" target="_blank">
                                        {{ trans('plugins/payment::payment.service_registration', ['name' => 'Alphabank']) }}
                                    </a>
                                </li>
                                <li style="list-style-type:decimal">
                                    <p>{{  clean(trans('plugins/alphabank::alphabank.alphabank_after_service_registration_msg')) }}</p>
                                </li>
                                <li style="list-style-type:decimal">
                                    <p>{{  clean(trans('plugins/alphabank::alphabank.alphabank_enter_client_id_and_secret')) }}</p>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="col-sm-6">
                    <div class="well bg-white">
                        <div class="form-group mb-3">
                            <label class="text-title-field"
                                   for="alphabank_name">{{ trans('plugins/payment::payment.method_name') }}</label>
                            <input type="text" class="next-input input-name"
                                   name="payment_alphabank_name" id="alphabank_name" data-counter="400"
                                   value="{{ setting('payment_alphabank_name', trans('plugins/payment::payment.pay_online_via', ['name' => 'Alphabank'])) }}">
                        </div>
                        <div class="form-group mb-3">
                            <label class="text-title-field"
                                   for="payment_alphabank_description">{{ trans('core/base::forms.description') }}</label>
                            <textarea class="next-input" name="payment_alphabank_description"
                                      id="payment_alphabank_description">{{ get_payment_setting('description', 'alphabank', __('Payment with Alphabank')) }}</textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label class="text-title-field"
                                   for="payment_alphabank_description">{{ clean(trans('plugins/alphabank::alphabank.installments')) }}</label>
                            @php
                                $payment_Alphabank_installments=setting('payment_alphabank_installments');
                            @endphp
                            <select class="next-input" name="payment_alphabank_installments"
                                    id="payment_alphabank_installments">
                                @for($x=0; $x<=36; $x++)
                                    <option
                                        value="{{$x}}" {{$x==$payment_Alphabank_installments?'selected="selected"':''}}>{{$x}}</option>
                                @endfor
                            </select>
                        </div>
                        <p class="payment-note">
                            {{ trans('plugins/payment::payment.please_provide_information') }} <a
                                target="_blank" href="https://cardlink.gr/">Alphabank</a>:
                        </p>
                        <div class="form-group mb-3">
                            <label class="text-title-field"
                                   for="alphabank_client_id">{{ clean(trans('plugins/alphabank::alphabank.alphabank_key')) }}</label>
                            <input type="text" class="next-input" name="payment_alphabank_client_id"
                                   id="alphabank_client_id" placeholder="0024077786"
                                   value="{{ app()->environment('demo') ? '*******************************' : setting('payment_alphabank_client_id') }}">
                        </div>
                        <div class="form-group mb-3">
                            <label class="text-title-field"
                                   for="alphabank_secret">{{ clean(trans('plugins/alphabank::alphabank.alphabank_secret')) }}</label>
                            <div class="input-option">
                                <input type="password" class="next-input" id="alphabank_secret"
                                       name="payment_alphabank_secret" placeholder="*************"
                                       value="{{ app()->environment('demo') ? '*******************************' : setting('payment_alphabank_secret') }}">
                            </div>
                        </div>
                        {!! apply_filters(PAYMENT_METHOD_SETTINGS_CONTENT, null, 'Alphabank') !!}
                    </div>
                </div>
            </div>
            <div class="col-12 bg-white text-end">
                <button
                    class="btn btn-warning disable-payment-item @if ($alphabankStatus== 0) hidden @endif"
                    type="button">{{ trans('plugins/payment::payment.deactivate') }}</button>
                <button
                    class="btn btn-info save-payment-item btn-text-trigger-save @if ($alphabankStatus== 1) hidden @endif"
                    type="button">{{ trans('plugins/payment::payment.activate') }}</button>
                <button
                    class="btn btn-info save-payment-item btn-text-trigger-update @if ($alphabankStatus== 0) hidden @endif"
                    type="button">{{ trans('plugins/payment::payment.update') }}</button>
            </div>
            {!! Form::close() !!}
        </td>
    </tr>
    </tbody>
</table>

