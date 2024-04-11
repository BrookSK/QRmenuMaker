@if (
    (config('asaas.useAdmin')&&config('asaas.access_token')!=""&&config('asaas.enabled')) ||
    (config('asaas.useVendor')&&strlen($restorant->getConfig('asaas_access_token',""))>3&&$restorant->getConfig('asaas_enable','false')!='false')
)
    <div class="custom-control custom-radio mb-3">
        <input name="paymentType" class="custom-control-input" id="paymentAsaas" type="radio" value="asaas" {{ config('settings.default_payment')=="asaas"?"checked":""}}>
        <label class="custom-control-label" for="paymentAsaas">{{ __('Pay with Asaas') }}</label>
    </div>
@endif