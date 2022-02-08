@php
     $totalInstalments =   get_payment_setting('installments', ALPHABANK_PAYMENT_METHOD_NAME);
@endphp
{{--@include('plugins/aplhabank::js.alphabank.js')--}}
<script>
    function alphabankinstallments(e) {
        alert(e.target)
    }
</script>
<li class="list-group-item">
    <input class="magic-radio js_payment_method" type="radio" name="payment_method"
           id="payment_{{ ALPHABANK_PAYMENT_METHOD_NAME }}"
           value="{{ ALPHABANK_PAYMENT_METHOD_NAME }}" data-bs-toggle="collapse"
           data-bs-target=".payment_{{ ALPHABANK_PAYMENT_METHOD_NAME }}_wrap"
           data-parent=".list_payment_method"
           @if (setting('default_payment_method') == ALPHABANK_PAYMENT_METHOD_NAME) checked @endif
    >
    <label
        for="payment_{{ ALPHABANK_PAYMENT_METHOD_NAME }}">{{ get_payment_setting('name', ALPHABANK_PAYMENT_METHOD_NAME) }}</label>
    <div
        class="payment_{{ ALPHABANK_PAYMENT_METHOD_NAME }}_wrap payment_collapse_wrap collapse @if (setting('default_payment_method') == ALPHABANK_PAYMENT_METHOD_NAME) show @endif">
        @if ($errorMessage)
            <div class="text-danger my-2">
                {!! clean($errorMessage) !!}
            </div>
        @else
            <p>{!! get_payment_setting('description', ALPHABANK_PAYMENT_METHOD_NAME, __('Payment with Alphabank')) !!}</p>
        @endif
<label for="instalments"> <?php echo __('Instalments'); ?></label><br/>
<select name="alphabankinstallments" id="instalments" onchange="alphabankinstallments(this)" class=" form-control">
    @for ($x = 1; $x <= (int)$totalInstalments; $x++)
    <option value="{{$x}}"  {{($x == 1)?'selected="selected"':''}}>{{$x}}</option>
        @endfor
</select>
 <input type="hidden" id="order_id" name="_order_id" value="{{ $orderId }}">
<input type="hidden" id="{{$paymentId}}_type" name="_type" value="{{ALPHABANK_PAYMENT_METHOD_NAME}}">


    </div>
