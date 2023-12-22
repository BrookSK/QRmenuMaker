<thead class="thead-light">
    <tr>
        <th scope="col">{{ __('ID') }}</th>
        @if(auth()->user()->hasRole('admin'))
            <th scope="col">{{ __('Restaurant') }}</th>
        @endif
        <th class="table-web" scope="col">{{ __('Created') }}</th>
        <th class="table-web" scope="col">{{ !config('settings.is_whatsapp_ordering_mode') ? __('Table / Method') : __('Method') }}</th>
        @if (!isset($hideAction))
            <th class="table-web" scope="col">{{ __('Items') }}</th>
        @endif
        <th class="table-web" scope="col">{{ __('Price') }}</th>
        <th scope="col">{{ __('Last status') }}</th>
        @if (!isset($hideAction))
            <th scope="col">{{ __('Actions') }}</th>
        @endif
    </tr>
</thead>
<tbody>
@foreach($orders as $order)
<tr>
    <td>
        
        <a class="btn badge badge-success badge-pill" href="{{ route('orders.show',$order->id )}}">#{{ $order->id }}</a>
    </td>
    @hasrole('admin|driver')
    <th scope="row">
        <div class="media align-items-center">
            <a class="avatar-custom mr-3">
                <img class="rounded" alt="..." src={{ $order->restorant->icon }}>
            </a>
            <div class="media-body">
                <span class="mb-0 text-sm">{{ $order->restorant->name }}</span>
            </div>
        </div>
    </th>
    @endif

    <td class="table-web">
        {{ $order->created_at->locale(Config::get('app.locale'))->isoFormat('LLLL')  }}
    </td>
    <td class="table-web">
        {{ $order->getExpeditionType() }}
    </td>
    @if (!isset($hideAction))
        <td class="table-web">
            {{ count($order->items) }}
        </td>
    @endif
    <td class="table-web">
        @money( $order->order_price_with_discount, config('settings.cashier_currency'),config('settings.do_convertion'))
    </td>
    <td>
        @include('orders.partials.laststatus')
    </td>
    @if (!isset($hideAction))
        @include('orders.partials.actions.table',['order' => $order ]) 
    @endif
    
</tr>
@endforeach
</tbody>
