@if(config('app.isft')||true)
            @include('partials.fields',['fields'=>[
                ['ftype'=>'bool','name'=>"Pickup",'id'=>"can_pickup",'value'=>$restorant->can_pickup == 1 ? "true" : "false"],
                ['ftype'=>'bool','name'=>"Delivery",'id'=>"can_deliver",'value'=>$restorant->can_deliver == 1 ? "true" : "false"],
                ['ftype'=>'bool','name'=>"Free Delivery",'id'=>"free_deliver",'value'=>$restorant->free_deliver == 1 ? "true" : "false"],
                ['ftype'=>'bool','name'=>"Disable ordering",'id'=>"disable_ordering",'value'=>$restorant->getConfig('disable_ordering', false) ? "true" : "false"],
            ]])
            @if(config('app.isft')&&auth()->user()->hasRole('admin'))
                @include('partials.fields',['fields'=>[
                    ['ftype'=>'bool','name'=>"Self Delivery",'id'=>"self_deliver",'value'=>$restorant->self_deliver == 1 ? "true" : "false"],
                ]])
            @endif
        @endif  
        @if (config('app.isqrexact'))
            @include('partials.fields',['fields'=>[
                ['ftype'=>'bool','name'=>"Disable Call Waiter",'id'=>"disable_callwaiter",'value'=>$restorant->getConfig('disable_callwaiter', 0) ? "true" : "false"],
                ['ftype'=>'bool','name'=>"Disable continues orders",'id'=>"disable_continues_ordering",'value'=>$restorant->getConfig('disable_continues_ordering', 0) ? "true" : "false"],
                ['ftype'=>'bool','name'=>"Dine In",'id'=>"can_dinein",'value'=>$restorant->can_dinein == 1 ? "true" : "false"],
                
            ]])
        @endif