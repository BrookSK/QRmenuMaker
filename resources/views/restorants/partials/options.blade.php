@if(!config('settings.makePureSaaS',false)  && !(config('app.isdrive',false)||config('app.issd',false)) )
            @include('partials.fields',['fields'=>[
                ['ftype'=>'bool','name'=>"Escolher",'id'=>"can_pickup",'value'=>$restorant->can_pickup == 1 ? "true" : "false"],
                ['ftype'=>'bool','name'=>"Entrega",'id'=>"can_deliver",'value'=>$restorant->can_deliver == 1 ? "true" : "false"],
                ['ftype'=>'bool','name'=>"Entrega grátis",'id'=>"free_deliver",'value'=>$restorant->free_deliver == 1 ? "true" : "false"],
                ['ftype'=>'bool','name'=>"Desativar pedido",'id'=>"disable_ordering",'value'=>$restorant->getConfig('disable_ordering', false) ? "true" : "false"],
            ]])
            @if(config('app.isft')&&auth()->user()->hasRole('admin'))
                @include('partials.fields',['fields'=>[
                    ['ftype'=>'bool','name'=>"Self Delivery",'id'=>"self_deliver",'value'=>$restorant->self_deliver == 1 ? "true" : "false"],
                ]])
            @endif
       
        @if (config('app.isqrexact'))
            @include('partials.fields',['fields'=>[
                ['ftype'=>'bool','name'=>"Desabilitar Chamar garçom",'id'=>"disable_callwaiter",'value'=>$restorant->getConfig('disable_callwaiter', 0) ? "true" : "false"],
                ['ftype'=>'bool','name'=>"Desativar pedidos contínuos",'id'=>"disable_continues_ordering",'value'=>$restorant->getConfig('disable_continues_ordering', 0) ? "true" : "false"],
                ['ftype'=>'bool','name'=>"Comer no local",'id'=>"can_dinein",'value'=>$restorant->can_dinein == 1 ? "true" : "false"],
                
            ]])
        @endif
@endif  