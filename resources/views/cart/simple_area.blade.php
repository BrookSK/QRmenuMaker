<div class="card card-profile shadow">
    <div class="px-4">
      <div class="mt-5">
        <h3><span class="delArea">{{ __('Delivery area') }}</span></h3>
      </div>
      <div class="card-content border-top">
        <br />
        <select name="delivery_areas" id="delivery_areas" class="form-control{{ $errors->has('deliveryAreas') ? ' is-invalid' : '' }}" >
          @foreach ($deliveryAreas as $value => $text)
              <option value={{ $value }}>{{$text}}</option>
          @endforeach
      </select>
      </div>
      <br />
      <br />
    </div>
  </div>
  <br />