<div class="card card-profile shadow tablepicker">
    <div class="px-4">
      <div class="mt-5">
        <h3>{{ __('Table') }}<span class="font-weight-light"></span></h3>
      </div>
      <div class="card-content border-top">
        <br />
        <input type="hidden" value="{{$restorant->id}}" id="restaurant_id"/>
        @if ($tid==null)
          @include('partials.select',$tables)
        @else
          <p>{{$tableName}}</p>
          <input type="hidden" value="{{$tid}}" name="table_id"  id="table_id"/>
        @endif
       
      </div>
      <br />
      <br />
    </div>
  </div>
  <br />
