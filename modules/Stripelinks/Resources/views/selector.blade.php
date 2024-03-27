@if (
    (config('stripelinks.useAdmin')&&config('settings.stripe_secret')!=""&&config('stripelinks.enabled')) ||
    (config('stripelinks.useVendor')&&strlen($restorant->getConfig('stripe_secret',""))>3&&$restorant->getConfig('stripelinks_enable','false')!='false')
)
    <div class="custom-control custom-radio mb-3">
        <input name="paymentType" class="custom-control-input" id="paymentStripelinks" type="radio" value="stripelinks" {{ config('settings.default_payment')=="stripelinks"?"checked":""}}>
        <label class="custom-control-label" for="paymentStripelinks">{{ __('Pay with Stripe Checkout') }}</label>
    </div>
@endif