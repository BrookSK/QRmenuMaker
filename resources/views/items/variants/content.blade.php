@section('thead')
    <th>{{ __('Price') }}</th>
    <th>{{ __('Options') }}</th>
    @if($restorant->getConfig('stock_enabled',"false")!="false")
        @if ($item->qty_management==1)
            <th>{{ __('Stock') }}</th>
        @endif
    @endif
    <th>{{ __('Actions') }}</th>
@endsection
@section('tbody')
@foreach ($setup['items'] as $variant)
<tr>
    <td>{{ $variant->price }}</td>
    <td>
        {{ $variant->optionsList }}
    </td>
    @if($restorant->getConfig('stock_enabled',"false")!="false")
        @if ($item->qty_management==1)
            <td>{{ $variant->qty }}</td>
        @endif
    @endif
    <td><a href="{{ route("items.variants.edit",["variant"=>$variant->id]) }}" class="btn btn-primary btn-sm">{{ __('Edit') }}</a><a href="{{ route("items.variants.delete",["variant"=>$variant->id]) }}" class="btn btn-danger btn-sm">{{ __('Delete') }}</a></td>
</tr> 
@endforeach

@endsection