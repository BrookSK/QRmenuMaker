@extends('layouts.app', ['title' => __($title)])
@section('head')
  <!-- Import Interact --->
  <script src="{{ asset('vendor') }}/interact/interact.min.js"></script>
   <style>
       .canva {
            width: 1024px;
            height: 540px;
            background-color: #dee2e6;
            display: inline-block;
            text-align: center; 
            justify-content: center; 
            align-items: center;
            margin: auto;
            border-radius: 6px;
            border: dashed 2px rgba(0,0,0,0.2);
       }
       .resize-drag {
            width: 120px;
            border-radius: 2px;
            padding: 20px;
            background-color: #29e;
            color: white;
            font-size: 20px;
            font-family: sans-serif;
            margins: "0px 2px 0px 2px";
            marginm: 1rem;
            touch-action: none;
            position: absolute;

            border: dashed 2px rgba(255, 255, 255,0.5);

            /* This makes things *much* easier */
            box-sizing: border-box;
            text-align: center;
          }
          .resize-drag.circle {
            border-radius: 60px;
            height: 120px;
          }
        .resize-drag p {
            text-align: center;
            justify-content:center;
            top: 0;
            opacity: 1
        }
        .resize-drag span {
            text-align: center;
            position: absolute;
            bottom: 0;
            opacity: 0.6
        }
   </style> 
@endsection
@section('admin_title')
{{__('Restaurant Management')}}
@endsection

@section('content')


<!-- Modal -->
<div class="modal fade" id="tableModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">{{__('Manage Table')}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="exampleFormControlInput1">{{ __('Table name') }}</label>
          <input type="text" class="form-control" id="table_name" name="table_name" placeholder="{{ __('Table name') }}">
        </div>
        <div class="form-group">
          <label for="exampleFormControlInput1">{{ __('Table size') }}</label>
          <input type="number" class="form-control" id="table_size" name="table_size" placeholder="4">
        </div>
        <div class="custom-control custom-checkbox">
          <input type="checkbox" class="custom-control-input" id="table_round" name="table_round">
          <label class="custom-control-label" for="table_round">{{ __('Round table') }}</label>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close')}}</button>
        <button type="button" data-dismiss="modal" onclick="javascript:saveTable()"  class="btn btn-success"><span class="text-white">{{ __('Save')}}</span></button>
      </div>
    </div>
  </div>
</div>



<div class="header bg-gradient-info pb-6 pt-5 pt-md-8">
    <div class="container-fluid">
    </div>
</div>

<div class="container-fluid mt--7">
        <div class="row">

            <div class="col">
                <div class="card bg-secondary shadow">

                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __($title) }}</h3>
                            </div>
                            <div class="col-4 text-right">

                              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tableModal">
                                <i class="ni ni-fat-add"></i></span> {{ __('Add new table') }}
                              </button>
                              
                                <a href="javascript:saveFloor()" class="btn  btn-success" data-dismiss="modal"><span class="btn-inner--icon"><i class="ni ni-check-bold"></i></span> {{ __('Save') }}</a>
                                
                            </div>
                        </div>
                    </div>

                    <div class="card-body" style="display: inline-block; text-align: center; justify-content: center; align-items: center; background-color:#f4f5f7">
                        <div class="canva" id="canvaHolder">
                            @foreach ($restoarea->tables as $table)
                                <?php
                              
                                $whString="";
                                if($table->w||$table->h){
                                    $whString="width: ".$table->w."px; height: ".$table->h."px;";
                                }
                                ?>
                                <div 
                                id="drag-{{$table->id}}" 
                                data-id="{{$table->id}}" 
                                data-x="{{$table->x}}"
                                data-y="{{$table->y}}"
                                data-name="{{$table->name}}"
                                data-rounded="{{$table->rounded?$table->rounded:"no"}}"
                                data-size="{{$table->size}}"
                                class="resize-drag {{ $table->rounded=="yes"?"circle":""}}" style="transform: translate({{$table->x}}px, {{$table->y}}px); {{$whString}}" >
                                    <p> {{$table->name}} </p>
                                    <span>{{$table->size}}</span>
                                </div>
                            @endforeach
                        </div>
                        <br />
                        
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection
@section('js')
<script>
  var area_id="{{$restoarea->id}}";
  var table_id=null;
  var edit_id=null;

  function deleteTable(itemid){
    var element=$('#'+itemid);
    element.hide();
    element.attr('data-deleted','yes')
  }

  function editItem(itemid){
    reset();
    var element=$('#'+itemid);
    $('#table_name').val(element.attr('data-name'));
    $('#table_size').val(element.attr('data-size'));
    if(element.attr('data-rounded')=="yes"){
      $('#table_round').prop('checked', true);
    }else{
      $('#table_round').prop('checked', false);
    }
    
    edit_id=itemid;

    if(element.attr('data-id')){
      table_id=element.attr('data-id');
    }else{
      table_id=true; //Since it is edit
    }
    

    jQuery.noConflict(); 
    $('#tableModal').modal('toggle');
  }

  function reset(){
    $('#table_name').val("");
    $('#table_size').val("");
    $('#table_round').prop('checked', false);
    table_id=null;
    edit_id=null;
  }

  function saveTable(){
    if(table_id){
      updateTable();
    }else{
      addTable();
    }
  }

  function addTable(){
    $( ".canva" ).append( '<div class="resize-drag '+($('#table_round').prop('checked')?"circle":"")+'" id="'+Math.floor((Math.random() * 10000) + 1)+'" data-rounded="'+($('#table_round').prop('checked')?"yes":"no")+'" data-name="'+$('#table_name').val()+'" data-size="'+$('#table_size').val()+'"  data-x="0" data-y="0" class="resize-drag"><p>'+$('#table_name').val()+"</p><span>"+$('#table_size').val()+"</span></div>" );
    reset();
  }

  function updateTable(){
    var element=$('#'+edit_id);
    element.attr('data-name',$('#table_name').val());
    element.attr('data-size',$('#table_size').val())
    $('span:first', element).html($('#table_size').val());
    $('p:first', element).html($('#table_name').val());

    if($('#table_round').prop('checked')){
      element.attr('data-rounded',"yes");
      element.addClass('circle');
    }else{
      element.attr('data-rounded',"no");
      element.removeClass('circle');
    }

    reset();
  }

  function saveFloor(){
    var items=[];
    $.each( $('.canva'), function(i, element) {
      $('div', element).each(function(it,item) {
        var element=$('#'+item.id);
        var item={
          "table_id":element.attr('data-id'),
          "x":element.attr('data-x'),
          "y":element.attr('data-y'),
          "w":element.width()+44,
          "h":element.height()+44,
          "name":element.attr('data-name'),
          "deleted":element.attr('data-deleted'),
          "rounded":element.attr('data-rounded'),
          "size":element.attr('data-size')
        }
        items.push(item);
        
      });
    })

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        type:'post',
        data: {"items":items},
        url: "{{ route('floorplan.save',$restoarea->id) }}",
        success:function(response){
            if(response.status){
              alert("{{ __('Floor plan is saved.') }}");
            }else{
              alert(response.message);
            }
        }, error: function (response) {
          alert("{{ __('Error on save') }}");
        }
    })

  }
</script>
<script type="module">
    
    interact('.resize-drag')
    .on('doubletap', function (event) {
      editItem(event.currentTarget.id);
      event.preventDefault()
    })
    .on('hold', function (event) {
      if(confirm("{{ __('Delete this table') }}")){
        deleteTable(event.currentTarget.id);
      }

      event.preventDefault()

    })
    .resizable({
    // resize from all edges and corners
    edges: { left: false, right: true, bottom: true, top: false },

    listeners: {
      move (event) {

        var target = event.target
        var x = (parseFloat(target.getAttribute('data-x')) || 0)
        var y = (parseFloat(target.getAttribute('data-y')) || 0)

        // update the element's style
        target.style.width = event.rect.width + 'px'
        target.style.height = event.rect.height + 'px'

        // translate when resizing from top or left edges
        x += event.deltaRect.left
        y += event.deltaRect.top

        target.style.webkitTransform = target.style.transform ='translate(' + x + 'px,' + y + 'px)'

        target.setAttribute('data-x', x);
        target.setAttribute('data-y', y);
      }
    },
    modifiers: [
      // keep the edges inside the parent
      interact.modifiers.restrictEdges({
        outer: 'parent'
      }),

      // minimum size
      interact.modifiers.restrictSize({
        min: { width: 100, height: 50 }
      })
    ],

    inertia: true
  })
  .draggable({
    listeners: { move: dragMoveListener },
    inertia: true,
    modifiers: [
      interact.modifiers.restrictRect({
        restriction: 'parent',
        endOnly: true
      })
    ]
  })

  function dragMoveListener (event) {
        var target = event.target
        // keep the dragged position in the data-x/data-y attributes
        var x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx
        var y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy

        // translate the element
        target.style.webkitTransform =
            target.style.transform =
            'translate(' + x + 'px, ' + y + 'px)'

        // update the posiion attributes
        target.setAttribute('data-x', x)
        target.setAttribute('data-y', y)

    }

    // this function is used later in the resizing and gesture demos
    window.dragMoveListener = dragMoveListener



    </script>  
@endsection
