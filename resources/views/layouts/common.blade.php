<link rel="stylesheet" href="{{ asset('vendor') }}/intltelinput/build/css/intlTelInput.css">
<style type="text/css">
    .iti__flag {background-image: url("/vendor/intltelinput/build/img/flags.png");}

@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
  .iti__flag {background-image: url("/vendor/intltelinput/build/img/flags@2x.png");}
}
</style>

<script src="{{ asset('vendor') }}/intltelinput/build/js/intlTelInput.js"></script>
<script src="{{ asset('vendor') }}/intltelinput/build/js/utils.js"></script>
<script>
    var defCountry="<?php echo config('settings.default_country','US'); ?>";
    function initPhone(name){
        var input = document.querySelector("input[name='"+name+"']");
        if(input!=null){
            var iti=window.intlTelInput(input, {
              nationalMode:true,
                hiddenInput: name,
                //customContainer:"form-controls",
                autoHideDialCode:true,
                separateDialCode:true,
                autoPlaceholder:"aggressive",
                initialCountry: defCountry,
                utilsScript: "/vendor/intltelinput/build/js/utils.js",
            });


        var reset = function() {
		  input.classList.remove("error");
          setTheHidden();
		};

        var setTheHidden =function(){
            var theHidden=document.querySelector("input[type=hidden][name='"+name+"']");
            theHidden.value = iti.getSelectedCountryData().dialCode+input.value;
            console.log(theHidden.value);
        }


		input.addEventListener('change', reset);
		input.addEventListener('keyup', reset);	 

        input.addEventListener("countrychange", function() {
            setTheHidden();
        });

        setTheHidden();

        }
    }
    setTimeout(() => {
            initPhone('whatsapp_phone');
            initPhone('phone');
            initPhone('phone_owner');
            initPhone('phone_number');
            //initPhone('phone_driver');
            
            
        }, 3000);
  
</script>
