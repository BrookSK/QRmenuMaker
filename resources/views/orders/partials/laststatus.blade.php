<?php
$badgeTypes=['badge-primary','badge-primary','badge-warning','badge-info','badge-default','badge-warning','badge-success','badge-success','badge-danger','badge-danger','badge-success','badge-success','badge-danger','badge-success','badge-success'];
?>
@if($order->status->count()>0)
    <span class="badge {{ $badgeTypes[$order->status->pluck('id')->last()] }} badge-pill">{{ __($order->status->pluck('alias')->last()) }}</span>
@endif  