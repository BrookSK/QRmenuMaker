<div class="card card-profile shadow" id="addressBox">
    <div class="px-4">
      <div class="mt-5">
        <h3>{{ __('Delivery Info') }}<span class="font-weight-light"></span></h3>
      </div>
      <div class="card-content border-top">
        <br />
        @include('partials.fields',
        ['fields'=>[
          ['ftype'=>'input','name'=>"",'id'=>"addressID",'placeholder'=>"Your delivery address here ...",'required'=>true],
          ]])

        <label>{{ __('Delivery area') }}</label>
        <div class="">
          <select name="delivery_area" id="delivery_area" class="noselecttwo form-control{{ $errors->has('deliveryAreas') ? ' is-invalid' : '' }}" >
            <option  value="0">{{ __('Select delivery area') }}</option>
            @foreach ($restorant->deliveryAreas()->get() as $simplearea)
                <option data-cost="{{ $simplearea->cost }}" value="{{ $simplearea->id }}">{{$simplearea->getPriceFormated()}}</option>
            @endforeach
          </select>
        </div>
        
      </div>
      <br />
      <br />
    </div>
</div>
