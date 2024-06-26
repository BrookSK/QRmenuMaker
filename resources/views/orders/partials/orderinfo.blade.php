
<div class="card-body">
    @include('partials.flash')
    @if ($order->restorant)
        <h6 class="heading-small text-muted mb-4" style="font-size: 18px;">{{ __('Restaurant information') }}</h6>
        <div class="pl-lg-4">
            <h3 style="font-size: 18px;">{{ $order->restorant->name }}</h3>
            <h4 style="font-size: 18px;">{{ $order->restorant->address }}</h4>
            <h4 style="font-size: 18px;">{{ $order->restorant->phone }}</h4>
            <h4 style="font-size: 18px;">{{ $order->restorant->user->name.", ".$order->restorant->user->email }}</h4>
        </div>
        <hr class="my-4" />
    @endif

     @if (config('app.isft')&&$order->client)
         <h6 style="font-size: 18px;" class="heading-small text-muted mb-4">{{ __('Client Information') }}</h6>
         <div class="pl-lg-4">
             <h3 style="font-size: 18px;">{{ $order->client?$order->client->name:"" }}</h3>
             <h4 style="font-size: 18px;">{{ $order->client?$order->client->email:"" }}</h4>
             <h4 style="font-size: 18px;">{{ $order->address?$order->address->address:"" }}</h4>

             @if(!empty($order->address->apartment))
                 <h4 style="font-size: 18px;">{{ __("Apartment number") }}: {{ $order->address->apartment }}</h4>
             @endif
             @if(!empty($order->address->entry))
                 <h4 style="font-size: 18px;">{{ __("Entry number") }}: {{ $order->address->entry }}</h4>
             @endif
             @if(!empty($order->address->floor))
                 <h4 style="font-size: 18px;">{{ __("Floor") }}: {{ $order->address->floor }}</h4>
             @endif
             @if(!empty($order->address->intercom))
                 <h4 style="font-size: 18px;">{{ __("Intercom") }}: {{ $order->address->intercom }}</h4>
             @endif
             @if($order->client&&!empty($order->client->phone))
             <br/>
             <h4 style="font-size: 18px;">{{ __('Contact')}}: {{ $order->client->phone }}</h4>
             @endif
         </div>
         <hr class="my-4" />
     @else
         @if ($order->table)
             <h6 class="heading-small text-muted mb-4" style="font-size: 18px;">{{ __('Table Information') }}</h6>
             <div class="pl-lg-4">

                     <h3 style="font-size: 18px;">{{ __('Table:')." ".$order->table->name }}</h3>
                     @if ($order->table->restoarea)
                         <h4 style="font-size: 18px;">{{ __('Area:')." ".$order->table->restoarea->name }}</h4>
                     @endif


             </div>
             <hr class="my-4" />
         @endif
     @endif



    <?php
        $currency=config('settings.cashier_currency');
        $convert=config('settings.do_convertion');
    ?>

    @if ($order->driver)
        @hasrole('admin|owner|staff')
            <h6 class="heading-small text-muted mb-4" style="font-size: 18px;">{{ __('Driver') }}</h6>
            <p><a href="/drivers/{{ $order->driver->id}}/edit">{{ $order->driver->name }}</a></p>
            <hr class="my-4" />
        @endhasanyrole
    @endif
     @if(count($order->items)>0)
     <h6 class="heading-small text-muted mb-4" style="font-size: 18px;">Pedido</h6>

     <ul id="order-items">
         @foreach($order->items as $item)
             <?php
                 $theItemPrice = ($item->pivot->variant_price ? $item->pivot->variant_price : $item->price);
             ?>
            @if ( $item->pivot->qty > 0)
            <li><h4 style="font-size: 18px;">({{ $item->pivot->qty. " X " . $item->name }} -  @money($theItemPrice, $currency,$convert)  =  ( @money( $item->pivot->qty*$theItemPrice, $currency,true) )

                @if($item->pivot->vatvalue > 0))
                    <span class="small"> -- {{ __('VAT').' '.$item->pivot->vat."%: "}} ( @money( $item->pivot->vatvalue, $currency,$convert) )</span>
                @endif
                @isset($item->pivot->created_at)
                    <span class="small"> - Criado em : {{$item->pivot->created_at->format('d/m/Y H:i')}}</span>
                @endisset

                 @hasrole('admin|owner|staff')
                    <?php $lasStatusId=$order->status->pluck('id')->last(); ?>
                    @if ($lasStatusId!=7&&$lasStatusId!=11)
                        <span class="small">
                            <button
                            data-toggle="modal"
                            data-target="#modal-order-item-count"
                            type="button"
                            onclick="$('#item_qty').val('{{$item->pivot->qty}}'); $('#pivot_id').val('{{$item->pivot->id}}');   $('#order_id').val('{{$order->id}}');"
                            class="btn btn-outline-danger btn-sm">
                                <span class="btn-inner--icon">
                                    <i class="ni ni-ruler-pencil"></i>
                                </span>
                            </button>
                        </span>
                    @endif
                 @endif
                 @if($item->pivot->is_item_fulfilled)
                    <span class="small">(Atendido)</span>
                 @else
                    <span class="small">(Novo)</span>
                 @endif
             </h4>
                 @if (strlen($item->pivot->variant_name)>2)
                     <br />
                     <table class="table align-items-center">
                         <thead class="thead-light">
                             <tr>
                                 @foreach ($item->options as $option)
                                     <th>{{ $option->name }}</th>
                                 @endforeach


                             </tr>
                         </thead>
                         <tbody class="list">
                             <tr>
                                 @foreach (explode(",",$item->pivot->variant_name) as $optionValue)
                                     <td>{{ $optionValue }}</td>
                                 @endforeach
                             </tr>
                         </tbody>
                     </table>
                 @endif

                 @if (strlen($item->pivot->extras)>2)
                     <br /><span>{{ __('Extras') }}</span><br />
                     <ul>
                         @foreach(json_decode($item->pivot->extras) as $extra)
                             <li> {{  $extra }}</li>
                         @endforeach
                     </ul><br />
                 @endif
                 <br />
             </li>
            @else
                <li>
                    {{ __('Removed') }}
                    <h4 class="text-muted" style="font-size: 22px;">{{$item->name }} -  @money($theItemPrice, $currency,$convert)

                        @if($item->pivot->vatvalue>0))
                            <span class="small">-- {{ __('VAT ').$item->pivot->vat."%: "}} ( @money( $item->pivot->vatvalue, $currency,$convert) )</span>
                        @endif
                    </h4>
                    <br />
                </li>
            @endif

         @endforeach
     </ul>
     @endif
     @if(!empty($order->whatsapp_address))
        <br/>
        <h4 style="font-size: 18px;">{{ __('Address') }}: {{ $order->whatsapp_address }}</h4>
     @endif
     @if(!empty($order->comment))
        <br/>
        <h4 style="font-size: 18px;">{{ __('Comment') }}: {{ $order->comment }}</h4>
     @endif
     @if(strlen($order->phone)>2)
        <h4 style="font-size: 18px;">{{ __('Phone') }}: {{ $order->phone }}</h4>
     @endif
     <br />
     @if(!empty($order->time_to_prepare))
     <br/>
     <h4 style="font-size: 18px;">{{ __('Time to prepare') }}: {{ $order->time_to_prepare ." " .__('minutes')}}</h4>
     <br/>
     @endif
     <h5 style="font-size: 18px;">{{ __("NET") }}: @money( $order->order_price-$order->vatvalue, $currency ,true)</h5>
     <h5 style="font-size: 18px;">{{ __("VAT") }}: @money( $order->vatvalue, $currency,$convert)</h5>
     <h4 style="font-size: 18px;">{{ __("Sub Total") }}: @money( $order->order_price, $currency,$convert)</h4>
     @if($order->delivery_method==1)
     <h4 style="font-size: 18px;">{{ __("Delivery") }}: @money( $order->delivery_price, $currency,$convert)</h4>
     @endif
     @if ($order->discount>0)
        <h4 style="font-size: 18px;">{{ __("Discount") }}: @money( $order->discount, $currency,$convert)</h4>
        <h4 style="font-size: 18px;">{{ __("Coupon code") }}: {{$order->coupon}}</h4>
     @endif
     @if ($order->tip>0)
        <h4>{{ __("Tip") }}: @money( $order->tip, $currency,$convert)</h4>
     @endif
     <hr/>
        <h3 style="font-size: 18px;">Resumo do Pedido:</h3>
        <ul id="order-items">
            @php
                $orderAgroupeds = $order->items->groupBy('name')->toArray();
                $items = [];
                $theItemPrice = 0;
                $total = 0;
                foreach ($orderAgroupeds as $key => $agroupeds) {
                    if(count($agroupeds) <= 1){
                        $items[$key]['qty']        = 0;
                        $items[$key]['item_price'] = 0;
                        $items[$key]['total']      = 0;
                        $theItemPrice              = ($agroupeds[0]['pivot']['variant_price'] ? $agroupeds[0]['pivot']['variant_price'] : $agroupeds[0]['price']);
                        $total                     = $agroupeds[0]['pivot']['qty'] * $theItemPrice;
                        $items[$key]['qty']        = $agroupeds[0]['pivot']['qty'];
                        $items[$key]['item_price'] = $theItemPrice;
                        $items[$key]['total']      = $total;
                        continue;
                    }else{
                        $items[$key]['qty']        = 0;
                        $items[$key]['item_price'] = 0;
                        $items[$key]['total']      = 0;
                        foreach ($agroupeds as $item) {
                            $theItemPrice               = ($item['pivot']['variant_price'] ? $item['pivot']['variant_price'] : $item['price']);
                            $total                      = $item['pivot']['qty'] * $theItemPrice;
                            $items[$key]['qty']        += $item['pivot']['qty'];
                            $items[$key]['item_price']  = $theItemPrice;
                            $items[$key]['total']      += $total;

                        }
                    }
                    $total = 0;
                    $theItemPrice = 0;
                }
            @endphp

            @forelse ( $items as $name => $item )
            <li>
                <h4 style="font-size: 18px;">
                    {{$item['qty']}} X {{$name}} - {{money($item['item_price'],$currency,$convert)}} - Total: {{money($item['total'],$currency,$convert)}}
                </h4>
            </li>
            @empty
                <li> -- </li>
            @endforelse
        </ul>
     <hr />
     <h3 style="font-size: 18px;">{{ __("TOTAL") }}: @money( $order->delivery_price+$order->order_price_with_discount, $currency,true)</h3>
     <hr />
     <h4 style="font-size: 18px;">{{ __("Payment method") }}: {{ __(strtoupper($order->payment_method)) }}</h4>
     <h4 style="font-size: 18px;">{{ __("Payment status") }}: {{ __(ucfirst($order->payment_status)) }}</h4>
     @if ($order->payment_status=="unpaid"&&strlen($order->payment_link)>5)
         <button onclick="location.href='{{$order->payment_link}}'" class="btn btn-success">{{ __('Pay now') }}</button>
     @endif
     <hr />
     @if(config('app.isft') || config('app.iswp'))
         <h4 style="font-size: 18px;">{{ __("Delivery method") }}: {{ $order->getExpeditionType() }}</h4>
         <h3 style="font-size: 18px;">{{ __("Time slot") }}: @include('orders.partials.time', ['time'=>$order->time_formated])</h3>
     @else
         <h4 style="font-size: 18px;">{{ __("Dine method") }}: {{ $order->getExpeditionType() }}</h4>
         @if ($order->delivery_method!=3)
             <h3 style="font-size: 18px;">{{ __("Time slot") }}: @include('orders.partials.time', ['time'=>$order->time_formated])</h3>
         @endif
     @endif

     @if(isset($custom_data)&&count($custom_data)>0)
        <hr />
        <h3 style="font-size: 18px;">{{ __(config('settings.label_on_custom_fields')) }}</h3>
        @foreach ($custom_data as $keyCutom => $itemValue)
            <h4 style="font-size: 18px;">{{ __("custom.".$keyCutom) }}: {{ $itemValue }}</h4>
        @endforeach
     @endif
 </div>
