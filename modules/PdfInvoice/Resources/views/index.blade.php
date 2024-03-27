@extends('pdf-invoice::layouts.master')

@section('content')
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />


<!-- POS invoice Modal -->
@include('pdf-invoice::receipt')
<!-- End POS invoice Modal -->

<div class="page-content container ">
    <div class="page-header text-blue-d2">
        <h1 class="display-4">
            
        

        <div class="page-tools">
        </h1>
            <div class="action-buttons no-print">
                <a href="#" id="printBtn" class="btn bg-white btn-light mx-1px text-95" href="#" data-title="Print">
                    <i class="mr-1 fa fa-print text-primary-m1 text-120 w-2"></i>
                    {{__('Print invoice') }}
                </a>
                <a href="#" id="printReciptBtn" class="btn bg-white btn-light mx-1px text-95" href="#" data-title="Print">
                    <i class="mr-1 fa fa-print text-primary-m1 text-120 w-2"></i>
                    {{__('Print recipt') }}
                </a>
                <a id="exportBtn" href="#" class="btn bg-white btn-light mx-1px text-95" href="#" data-title="PDF">
                    <i class="mr-1 fa fa-file-pdf-o text-danger-m1 text-120 w-2"></i>
                    {{__('Export') }}
                </a>
                <a style="display: none" id="sendBtn" href="#" class="btn bg-white btn-light mx-1px text-95" href="#" data-title="PDF">
                    <i class="mr-1 fa fa-envelope-o text-primary-m1 text-120 w-2"></i>
                    {{__('Send') }}
                </a>
            </div>
        </div>
    </div>

    <div id="print_area" class="container px-0 card">
        <div class="row mt-4">
            <div class="col-12 col-lg-10 offset-lg-1">
                

                
                <div class="row">
                    <div class="col-12">
                        <h1>{{ __(config('pdf-invoice.invoiceTitle','Order'))}} #{{$order->id}} </h1>
                        @if ($order->payment_status=="unpaid")
                            <span class="badge badge-danger">{{__('Unpaid')}}</span>
                        @else
                            <span class="badge badge-success">{{__('Paid')}}</span>
                        @endif
                        
                        
                        <small class="text-muted">{{$order->created_at->format(config('settings.datetime_display_format'))}}</small><br />
                        <small class="text-muted">{{ __("Payment method") }}: {{ __(strtoupper($order->payment_method)) }}</small>
                        <hr />
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        @if (config('app.isft'))
                        
                        <div class="">
                            <h3>{{ $order->client->name }}</h3>
                            <h4>{{ $order->client->email }}</h4>
                            @if(!empty($order->client->phone))
                            <br/>
                            <h4>{{ __('Contact')}}: {{ $order->client->phone }}</h4>
                            @endif
                        </div>
                        
                    @else
                      
                        @if ($order->table)
                            
                        
                            <div class="">
                                
                                    <h3>{{ __('Table:')." ".$order->table->name }}</h3>
                                    @if ($order->table->restoarea)
                                        <h4>{{ __('Area:')." ".$order->table->restoarea->name }}</h4>
                                    @endif
                                
                                
                            </div>
                            
                        @endif
                    @endif

                    @if (!empty($order->address))
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
                    @endif

                    @if (!empty($order->whatsapp_address))
                    <h4>{{ __("Address") }}: {{ $order->whatsapp_address }}</h4>
                    @endif

                    <!-- Custom Fields -->
                    <div id="custom_fields_in_pdf">
                                
                        @if(isset($custom_data)&&count($custom_data)>0)
                            <hr />
                            <h3>{{ __(config('settings.label_on_custom_fields')) }}</h3>
                            @foreach ($custom_data as $keyCutom => $itemValue)
                                <h4>{{ __("custom.".$keyCutom) }}: {{ $itemValue }}</h4>
                            @endforeach
                        @endif
                    
                    
                    </div>
                    <!-- END Custom Fields -->


                    </div>
                    <!-- /.col -->

                    <div class="text-95 col-sm-6 align-self-start d-sm-flex justify-content-end">
                       
                        <div class="">
                            <h3>{{ $order->restorant->name }}</h3>
                            <h4>{{ $order->restorant->address }}</h4>
                            <h4>{{ $order->restorant->phone }}</h4>
                            @if ($order->restorant->getConfig('number_de_siren',false))
                                <h4>{{__('Numéro de inscription')}}: {{ $order->restorant->getConfig('number_de_siren','') }}</h4>
                                <h4>{{__('Numéro Intracommunautaire')}}: {{ $order->restorant->getConfig('number_de_intercommunication',false) }}</h4>
                            @endif
                            <h4>{{ $order->restorant->user->name.", ".$order->restorant->user->email }}</h4>
                        </div>
                        

                    </div>
                    <!-- /.col -->
                </div>

                <div class="mt-4">
                    
                    <div class="row text-600 text-white bgc-default-tp1 py-25">
                        <div class="d-none d-sm-block col-1">#</div>
                        <div class="col-9 col-sm-5">{{__('Name')}}</div>
                        <div class="d-none d-sm-block col-4 col-sm-2">{{ __('Qty') }}</div>
                        <div class="d-none d-sm-block col-sm-2">{{ __('Unit Price') }}</div>
                        <div class="col-2">{{ __('Amount') }}</div>
                    </div>

                    

                    <div class="text-95 text-secondary-d3">

                        @foreach ($order->items as $key => $item)
                            <div class="row mb-2 mb-sm-0 py-25">
                                <div class="d-none d-sm-block col-1">{{$key+1}}</div>
                                <div class="col-9 col-sm-5">{{$item->name}}<br />
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
                                    <span class="small text-muted">{{ __('Extras') }}</span><br />
                                    <table class="table align-items-center">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>{{__('Item') }}</th>
                                                <th>{{__('Price') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="list" >
                                            @foreach(json_decode($item->pivot->extras) as $extra)
                                                <?php $extraItem=explode(" + ",$extra); ?>
                                                <tr>
                                                    <td>{{$extraItem[0]}}</td>
                                                    <td>{{$extraItem[1]}}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                <br />
                                @endif
                            <?php 
                                $currency=config('settings.cashier_currency');
                                $convert=config('settings.do_convertion');
                                $theItemPrice= ($item->pivot->variant_price?$item->pivot->variant_price:$item->price);
                            ?> 
                            </div>
                                <div class="d-none d-sm-block col-2">{{$item->pivot->qty}}</div>
                                <div class="d-none d-sm-block col-2 text-95">@money($theItemPrice, $currency,$convert)</div>
                                <div class="col-2 text-secondary-d2">@money( $item->pivot->qty*$theItemPrice, $currency,$convert)</div>
                            </div>
                        @endforeach

                    </div>

                    <div class="row border-b-2 brc-default-l2"></div>

                    <?php 
                        $currency=config('settings.cashier_currency');
                        $convert=config('settings.do_convertion');
                    ?>

                    <div class="row mt-3">
                        <div class="col-12 col-sm-7 text-grey-d2 text-95 mt-2 mt-lg-0">
                            {{$order->comment}}
                        </div>

                        <div class="col-12 col-sm-5 text-grey text-90 order-first order-sm-last">
                            
                            <!-- NET -->
                            <div class="row my-2">
                                <div class="col-7 text-right">
                                    {{ __('NET') }}
                                </div>
                                <div class="col-5">
                                    <span class="text-110">@money( $order->order_price-$order->vatvalue, $currency ,true)</span>
                                </div>
                            </div>

                            <!-- VAT -->
                            <div class="row my-2">
                                <div class="col-7 text-right">
                                    {{ __('VAT') }}
                                </div>
                                <div class="col-5">
                                    <span class="text-110">@money( $order->vatvalue, $currency,$convert)</span>
                                </div>
                            </div>

                            <!-- Sub Total -->
                            <div class="row my-2">
                                <div class="col-7 text-right">
                                    {{ __('Sub Total') }}
                                </div>
                                <div class="col-5">
                                    <span class="text-110">@money( $order->order_price, $currency,$convert)</span>
                                </div>
                            </div>

                            <!-- Delivery -->
                            @if($order->delivery_method==1 )
                                <div class="row my-2">
                                    <div class="col-7 text-right">
                                        {{ __('Delivery') }}
                                    </div>
                                    <div class="col-5">
                                        <span class="text-110">@money( $order->delivery_price, $currency,$convert)</span>
                                    </div>
                                </div>
                            @endif

                            @if($order->discount>0)
                                <div class="row my-2">
                                    <div class="col-7 text-right">
                                        {{ __('Discount') }}
                                    </div>
                                    <div class="col-5">
                                        <span class="text-110">@money( $order->discount, $currency,$convert)</span>
                                    </div>
                                </div>
                            @endif

                            @if($order->tip>0)
                            <div class="row my-2">
                                <div class="col-7 text-right">
                                    {{ __('Tip') }}
                                </div>
                                <div class="col-5">
                                    <span class="text-110">@money( $order->tip, $currency,$convert)</span>
                                </div>
                            </div>
                        @endif

                             <!-- Sub Total -->
                             <div class="row my-2">
                                <div class="col-7 text-right">
                                    <b>{{ __('Total') }}</b>
                                </div>
                                <div class="col-5">
                                    <b><span class="text-110 text-success-d3">@money( $order->delivery_price+$order->order_price_with_discount, $currency,true)</span></b>
                                </div>
                            </div>


                            

                        </div>
                    </div>

                    <hr />

                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script>
    var LOCALE="<?php echo  App::getLocale() ?>";
    var CASHIER_CURRENCY = "<?php echo  config('settings.cashier_currency') ?>";
    var order="{{$order->id}}";
    var orderJSON=<?php echo $order->toJson(); ?>;

    receiptPOS=new Vue({
        el:"#modalPOSInvoice",
        data:{
        order:null
        },

        methods: {
        moment: function (date) {
            return moment(date);
        },
        decodeHtml: function (html) {
            var txt = document.createElement("textarea");
            txt.innerHTML = html;

            console.log("specia");
            console.log(txt.value)
            return txt.value;
        },
        formatPrice(price){
            var locale=LOCALE;
            if(CASHIER_CURRENCY.toUpperCase()=="USD"){
                locale=locale+"-US";
            }
        
            var formatter = new Intl.NumberFormat(locale, {
                style: 'currency',
                currency:  CASHIER_CURRENCY,
            });
        
            var formated=formatter.format(price);
        
            return formated;
        },
        showIt: function(params){
            var extras=JSON.parse(params);
            var s="";
            extras.forEach(element => {
                s+=element+" ";
            });

            if(s.length>1){
                s=" ( "+s+" ) ";
            }
            
            return s;
        },
        date: function (date) {
            return moment(date).format('MMMM Do YYYY, h:mm:ss a');
        }
        },
  })
  receiptPOS.order=orderJSON;
  


    $('#printBtn').on("click", function () {
        window.print();  
    });
    $('#printReciptBtn').on("click", function () {
        $('#modalPOSInvoice').modal('show');
    });
    $('#printPos').on("click", function () {
          $("#posRecipt").printThis(); 
        });
    $('#exportBtn').on("click", function () {
        var element = document.getElementById('print_area');
        var opt = {
           // margin:       1,
            filename:     'order_'+order+'.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2 },
            jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
        };

        html2pdf().set(opt).from(element).save();
    });
  </script>
@endsection
