@extends('general.index', $setup)
@section('tbody')
    @foreach ($setup['items'] as $item)
        <tr>
            <td>{{ $item->name }}</td>
            <td>{{ $item->cost }}</td>
            <td>{{ $item->phone }}</td>
            @include('partials.tableactions',$setup)
        </tr> 
    @endforeach
@endsection