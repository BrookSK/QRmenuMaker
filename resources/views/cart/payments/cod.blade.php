@if(!config('settings.hide_cod'))
    <div class="text-center" id="totalSubmitCOD"  style="display: {{ config('settings.default_payment')=="cod"&&!config('settings.hide_cod')?"block":"none"}};" >
        <button
            v-if="totalPrice"
            type="button"
            class="btn btn-success mt-4 paymentbutton"
            onclick="document.getElementById('order-form').submit();    "
        >{{ __('Place order') }}</button>
    </div>
@endif
