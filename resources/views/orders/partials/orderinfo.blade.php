
<div class="card-body">
    @include('partials.flash') 
    @if ($order->restorant)
        <h6 class="heading-small text-muted mb-4">{{ __('Restaurant information') }}</h6>
        <div class="pl-lg-4">
            <h3>{{ $order->restorant->name }}</h3>
            <h4>{{ $order->restorant->address }}</h4>
            <h4>{{ $order->restorant->phone }}</h4>
            <h4>{{ $order->restorant->user->name.", ".$order->restorant->user->email }}</h4>
        </div>
        <hr class="my-4" />
    @endif
    
    
 
     @if (config('app.isft')&&$order->client)
         <h6 class="heading-small text-muted mb-4">{{ __('Client Information') }}</h6>
         <div class="pl-lg-4">
             <h3>{{ $order->client?$order->client->name:"" }}</h3>
             <h4>{{ $order->client?$order->client->email:"" }}</h4>
             <h4>{{ $order->address?$order->address->address:"" }}</h4>
 
             @if(!empty($order->address->apartment))
                 <h4>{{ __("Apartment number") }}: {{ $order->address->apartment }}</h4>
             @endif
             @if(!empty($order->address->entry))
                 <h4>{{ __("Entry number") }}: {{ $order->address->entry }}</h4>
             @endif
             @if(!empty($order->address->floor))
                 <h4>{{ __("Floor") }}: {{ $order->address->floor }}</h4>
             @endif
             @if(!empty($order->address->intercom))
                 <h4>{{ __("Intercom") }}: {{ $order->address->intercom }}</h4>
             @endif
             @if($order->client&&!empty($order->client->phone))
             <br/>
             <h4>{{ __('Contact')}}: {{ $order->client->phone }}</h4>
             @endif
         </div>
         <hr class="my-4" />
     @else
         @if ($order->table)
             <h6 class="heading-small text-muted mb-4">{{ __('Table Information') }}</h6>
             <div class="pl-lg-4">
                 
                     <h3>{{ __('Table:')." ".$order->table->name }}</h3>
                     @if ($order->table->restoarea)
                         <h4>{{ __('Area:')." ".$order->table->restoarea->name }}</h4>
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
            <h6 class="heading-small text-muted mb-4">{{ __('Driver') }}</h6>
            <p><a href="/drivers/{{ $order->driver->id}}/edit">{{ $order->driver->name }}</a></p>
            <hr class="my-4" />
        @endhasanyrole
    @endif
     @if(count($order->items)>0)
     <h6 class="heading-small text-muted mb-4">{{ __('Order') }}</h6>
     
     <ul id="order-items">
         @foreach($order->items as $item)
             <?php 
                 $theItemPrice= ($item->pivot->variant_price?$item->pivot->variant_price:$item->price);
             ?>
            @if ( $item->pivot->qty>0)
            <li><h4>{{ $item->pivot->qty." X ".$item->name }} -  @money($theItemPrice, $currency,$convert)  =  ( @money( $item->pivot->qty*$theItemPrice, $currency,true) )
                 
                @if($item->pivot->vatvalue>0))
                    <span class="small">-- {{ __('VAT ').$item->pivot->vat."%: "}} ( @money( $item->pivot->vatvalue, $currency,$convert) )</span>
                @endif
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
                    <h4 class="text-muted">{{$item->name }} -  @money($theItemPrice, $currency,$convert) 
                 
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
        <h4>{{ __('Address') }}: {{ $order->whatsapp_address }}</h4>
     @endif
     @if(!empty($order->comment))
        <br/>
        <h4>{{ __('Comment') }}: {{ $order->comment }}</h4>
     @endif
     @if(strlen($order->phone)>2)
        <h4>{{ __('Phone') }}: {{ $order->phone }}</h4>
     @endif
     <br />
     @if(!empty($order->time_to_prepare))
     <br/>
     <h4>{{ __('Time to prepare') }}: {{ $order->time_to_prepare ." " .__('minutes')}}</h4>
     <br/>
     @endif
     <h5>{{ __("NET") }}: @money( $order->order_price-$order->vatvalue, $currency ,true)</h5>
     <h5>{{ __("VAT") }}: @money( $order->vatvalue, $currency,$convert)</h5>
     <h4>{{ __("Sub Total") }}: @money( $order->order_price, $currency,$convert)</h4>
     @if($order->delivery_method==1)
     <h4>{{ __("Delivery") }}: @money( $order->delivery_price, $currency,$convert)</h4>
     @endif
     @if ($order->discount>0)
        <h4>{{ __("Discount") }}: @money( $order->discount, $currency,$convert)</h4>
        <h4>{{ __("Coupon code") }}: {{$order->coupon}}</h4>
     @endif
     <hr />
     <h3>{{ __("TOTAL") }}: @money( $order->delivery_price+$order->order_price_with_discount, $currency,true)</h3>
     <hr />
     <h4>{{ __("Payment method") }}: {{ __(strtoupper($order->payment_method)) }}</h4>
     <h4>{{ __("Payment status") }}: {{ __(ucfirst($order->payment_status)) }}</h4>
     @if ($order->payment_status=="unpaid"&&strlen($order->payment_link)>5)
         <button onclick="location.href='{{$order->payment_link}}'" class="btn btn-success">{{ __('Pay now') }}</button>
     @endif
     <hr />
     @if(config('app.isft') || config('app.iswp'))
         <h4>{{ __("Delivery method") }}: {{ $order->getExpeditionType() }}</h4>
         <h3>{{ __("Time slot") }}: @include('orders.partials.time', ['time'=>$order->time_formated])</h3>
     @else
         <h4>{{ __("Dine method") }}: {{ $order->getExpeditionType() }}</h4>
         @if ($order->delivery_method!=3)
             <h3>{{ __("Time slot") }}: @include('orders.partials.time', ['time'=>$order->time_formated])</h3>
         @endif
     @endif

     @if(isset($custom_data)&&count($custom_data)>0)
        <hr />
        <h3>{{ __(config('settings.label_on_custom_fields')) }}</h3>
        @foreach ($custom_data as $keyCutom => $itemValue)
            <h4>{{ __("custom.".$keyCutom) }}: {{ $itemValue }}</h4>
        @endforeach
     @endif

     
 
 
 </div>