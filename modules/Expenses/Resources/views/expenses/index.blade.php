
@extends('general.index', $setup)
@section('tbody')
    @foreach ($setup['items'] as $item)
        <tr>
            
            <td>{{ $item->date }}</td>
            
            <td> @money($item->amount, config('settings.cashier_currency'),config('settings.do_convertion'))</td>
            
            <td>{{ $item->reference }}</td>
            
            <td>{{ $item->category?$item->category->name:"" }}</td>
            
            <td>{{$item->vendor?$item->vendor->name:"" }}</td>

            @include('partials.tableactions',$setup)
        </tr> 
    @endforeach
@endsection