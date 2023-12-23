@extends('general.index', $setup)
@section('tbody')
    @foreach ($setup['items'] as $item)
        <tr>
            <td>{{ $item->name }}</td>
            <td>{{ $item->size }}</td>
            <td>{{ $item->restoarea?$item->restoarea->name:"" }}</td>
            <td>
            <?php
                $param=[];
                $param[$setup['parameter_name']]=$item->id;
            ?>
             @if ($setup['hasQR'])
                <a href="{{ route('download.menu')."?table_id=".$item->id }}" class="btn btn-success btn-sm"><span class="btn-inner--icon"><i class="fas fa-qrcode"></i></span> {{ __('QR') }}</a>
            @endif
            <a href="{{ route( $setup['webroute_path']."edit",$param) }}" class="btn btn-primary btn-sm"><span class="btn-inner--icon"><i class="ni ni-ruler-pencil"></i></span> {{ __('crud.edit') }}</a>
            <a href="{{ route( $setup['webroute_path']."delete",$param) }}" class="btn btn-danger btn-sm"><span class="btn-inner--icon"><i class="ni ni-fat-remove"></i></span> {{ __('crud.delete') }}</a>
            </td>
        </tr> 
    @endforeach
@endsection