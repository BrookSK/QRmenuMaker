<?php
    $phoneForOrder=$order->phone;
    if(strlen($phoneForOrder)<2){
        $phoneForOrder=$order->getConfig('client_phone','');
    }
?>
@if(strlen($phoneForOrder)>2&&$order->restorant)
<div class="card card-profile shadow mt-4">
    <div class="card-header">
        <h5 class="h3 mb-0">{{ __("Send status")}}</h5>
    </div>
    <div class="card-body">
        <h4>{{ __('Phone') }}: {{ $phoneForOrder }}</h4>
        <form target="_blank" method="POST" action="{{ route('sendstatus.submit') }}">
       @csrf()
        <input name="phone_hidden" type="hidden" value="{{ $phoneForOrder }}" />
        <?php

            $messages=[];
            $msg1=$order->restorant->getConfig('SendStatusTemplate1','');
            $msg2=$order->restorant->getConfig('SendStatusTemplate2','');
            $msg3=$order->restorant->getConfig('SendStatusTemplate3','');
            $msg4=$order->restorant->getConfig('SendStatusTemplate4','');
            $msg5=$order->restorant->getConfig('SendStatusTemplate5','');

            if(strlen($msg1)>0){
                array_push($messages,$msg1);
            }
            if(strlen($msg2)>0){
                array_push($messages,$msg2);
            }
            if(strlen($msg3)>0){
                array_push($messages,$msg3);
            }
            if(strlen($msg4)>0){
                array_push($messages,$msg4);
            }
            if(strlen($msg5)>0){
                array_push($messages,$msg5);
            }

            $data=[];
            foreach ($messages as $key => $message) {
                $data[$message]=substr($message, 0, 30)."...";
            }
        ?>
        @include('partials.select',[
            'id'=>"template",
            'data'=>$data,
            'name'=>"Template"
        ])
        @include('partials.textarea',[
            'id'=>"message",
            'name'=>"Message"
        ])
        <button class="btn btn-success" type="submit">{{__('Send Whatsapp message')}}</button>
         </form>
    </div>
    
</div>
<script type="text/javascript">
setTimeout(() => {
    $(document).ready(function(){

        $('#template').on('select2:select', function (e) {
            var data = e.params.data;
            $("#message").val(data.id);
        });

    });  
}, 2000);
   
</script>
@endif
