@foreach ($order->actions['buttons'] as $next_status)
    <?php
      $btnType="btn-primary";
      if(str_contains($next_status,'accept')){
        $btnType="btn-success";
      }else if(str_contains($next_status,'reject')){
        $btnType="btn-danger";
      }
    ?>
    @if (in_array("timprepare", config('global.modules',[])) && $next_status=="accepted_by_restaurant")
      <!-- This is special case when owneer can set prepare time -->
      <button data-toggle="modal" data-target="#modal-time-to-prepare" onclick="$('#form-time-to-prepare').attr('action', '/updatestatus/accepted_by_restaurant/{{$order->id}}');" class="btn btn-sm {{$btnType   }}" value="{{ __($next_status) }}" />{{ __($next_status) }}</button>
    @elseif ($next_status=="assigned_to_driver")
      <!-- This is special case when owneer can set driver -->
      <script>
        function setSelectedOrderId(id){
            $("#form-assing-driver").attr("action", "/updatestatus/assigned_to_driver/"+id);
        }
    </script>
      <button type="button" class="btn btn-primary btn-sm" onClick=(setSelectedOrderId({{ $order->id }}))  data-toggle="modal" data-target="#modal-asign-driver">{{ __('Assign to driver') }}</button>
       @else
      <a href="{{ url('updatestatus/'.$next_status.'/'.$order->id) }}" class="btn btn-sm {{$btnType   }}">{{ __($next_status) }}</a>
    @endif
     
@endforeach
@if (strlen($order->actions['message'])>0)
    <p><small class="text-muted">{{ $order->actions['message'] }}</small><p>
@endif