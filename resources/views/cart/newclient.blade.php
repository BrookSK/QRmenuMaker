<div class="card card-profile shadow  mt-4" id="clientBox">
    <div class="px-4">
      <div class="mt-5">
        <h3>{{ __('customers_him_self') }}<span class="font-weight-light"></span></h3>
      </div>
      <div class="card-content border-top">
        <br />
        @include('partials.fields',
        ['fields'=>[
          ['ftype'=>'input','name'=>"Customer name",'id'=>"custom[client_name]",'placeholder'=>"Customer name",'required'=>true],
          ['ftype'=>'input','name'=>"Customer phone",'id'=>"custom[client_phone]",'placeholder'=>"Please enter phone number.",'required'=>true],
          ]])

        
      </div>
      <br />
      <br />
    </div>
</div>
