
"use strict";
$(document).ready(function() { 
    $("#show-hide-filters").on("click",function(){

        if($(".orders-filters").is(":visible")){
            $("#button-filters").removeClass("ni ni-bold-up")
            $("#button-filters").addClass("ni ni-bold-down")
        }else if($(".orders-filters").is(":hidden")){
            $("#button-filters").removeClass("ni ni-bold-down")
            $("#button-filters").addClass("ni ni-bold-up")
        }

        $(".orders-filters").slideToggle();
    });

    function itemSearch(){
        var _selectIsOpen=false;

        $("body")
        .on("select2:opening", event => {})
        
        .on("keypress", event => {
          if ($(event.target).is('input, textarea, select')) return;
          if (_selectIsOpen) {
            return;
          }
          
    
          
          const charCode = event.which;
          if (
            !(event.altKey || event.ctrlKey || event.metaKey) &&
            ((charCode >= 48 && charCode <= 57) ||
              (charCode >= 65 && charCode <= 90) ||
              (charCode >= 97 && charCode <= 122))
          ) {
            $('#restaurantSearch').select2("open");
            $('#itemsSearch').select2("open");
            $("input.select2-search__field")
              .eq(0)
              .val(String.fromCharCode(charCode));
          }
        });
    
        $('select').on('change', function() {
          if(this.id=="restaurantSearch"&&this.value!=""){
            window.location.href =resUrl.replace('0',this.value);
          }
          if(this.id=="itemsSearch"&&this.value!=""){
            window.location.href ="/items/"+this.value+"/edit";
          }
        });
    }
    itemSearch();
});
