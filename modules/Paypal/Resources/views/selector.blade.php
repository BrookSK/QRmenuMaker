@if (
    (config('paypal.useAdmin')&&config('paypal.secret')!=""&&config('paypal.enabled')) ||
    (config('paypal.useVendor')&&strlen($restorant->getConfig('paypal_secret',""))>3&&$restorant->getConfig('paypal_enable','false')!='false')
)
    <div class="custom-control custom-radio mb-3">
        <input name="paymentType" class="custom-control-input" id="paymentPayPal" type="radio" value="paypal" {{ config('settings.default_payment')=="paypal"?"checked":""}}>
        <label class="custom-control-label" for="paymentPayPal">{{ __('Pay with PayPal') }}</label>
    </div>
@endif