@extends('general.index', $setup)
@section('tbody')
@foreach ($setup['items'] as $item)
<tr>
    <td>{{ $item->name }}</td>
    <?php
        $param=[];
        $param[$setup['parameter_name']]=$item->id;
    ?>
    <td>

        @if ($setup['hasFloorPlan'])
            <a href="{{ route('floorplan.edit',$item->id) }}" class="btn btn-success btn-sm"><span class="btn-inner--icon"><i class="ni ni-vector"></i></span> {{ __('Floor Plan') }}</a>
        @endif
        <a href="{{ route( $setup['webroute_path']."edit",$param) }}" class="btn btn-primary btn-sm"><span class="btn-inner--icon"><i class="ni ni-ruler-pencil"></i></span> {{ __('crud.edit') }}</a>
        <a href="{{ route( $setup['webroute_path']."delete",$param) }}" class="btn btn-danger btn-sm"><span class="btn-inner--icon"><i class="ni ni-fat-remove"></i></span> {{ __('crud.delete') }}</a>
    </td>
</tr> 
@endforeach

@endsection