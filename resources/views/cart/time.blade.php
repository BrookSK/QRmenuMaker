<div class="card card-profile shadow">
    <div class="px-4">
      <div class="mt-5">
        <h3><span class="delTime delTimeTS">{{ __('Delivery time') }}</span><span class="picTime picTimeTS">{{ __('Pickup time') }}</span><span class="font-weight-light"></span></h3>
      </div>
      <div class="card-content border-top">
        <br />


        @if (($doWeHaveOrderAfterHours&&count($timeSlots)==1&&$timeSlots[0]==null)|| $restorant->getConfig('always_order_date_time_enable',false)=="true")
          <input type="datetime-local" id="timeslot" name="timeslot" class="form-control{{ $errors->has('timeslot') ? ' is-invalid' : '' }}">
        @else
          <select name="timeslot" id="timeslot" class="form-control{{ $errors->has('timeslot') ? ' is-invalid' : '' }}" required>
            @foreach ($timeSlots as $value => $text)
                <option value={{ $value }}>{{$text}}</option>
            @endforeach
          </select>
        @endif
        
      </div>
      <br />
      <br />
    </div>
  </div>
  <br />