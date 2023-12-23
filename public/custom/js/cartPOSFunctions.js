
"use strict";
var cartContent=null;
var orderContent=null;
var receiptPOS=null;
var cartTotal=null;
var ordersTotal=null;
var footerPages=null;
var total=null;
var expedition=null;
var modalPayment=null;

$('#localorder_phone').hide();
/**
 *
 * @param {Number} net The net value
 * @param {Number} delivery The delivery value
 * @param {String} expedition 1 - Delivery 2 - Pickup 3 - Dine in
 */
function updatePrices(net,delivery,expedition){
  net=parseFloat(net);
  delivery=parseFloat(delivery);
  var formatter = new Intl.NumberFormat(LOCALE, {
    style: 'currency',
    currency:  CASHIER_CURRENCY,
  });
  
  var deduct=parseFloat(cartTotal.deduct);
  console.log("Deduct is "+deduct);

  //totalPrice -- Subtotal
  //withDelivery -- Total with delivery

  //Subtotal
  cartTotal.totalPrice=net;
  cartTotal.totalPriceFormat=formatter.format(net);

  if(expedition==1){
    //Delivery
    cartTotal.delivery=true;
    cartTotal.deliveryPrice=delivery;
    cartTotal.deliveryPriceFormated=formatter.format(delivery);

    //Total
    cartTotal.withDelivery=net+delivery-deduct;
    cartTotal.withDeliveryFormat=formatter.format(net+delivery-deduct);
    total.totalPrice=net+delivery-deduct;

     //modalPayment updated
    modalPayment.totalPrice=cartTotal.withDelivery;
    modalPayment.totalPriceFormat=cartTotal.withDeliveryFormat;
    modalPayment.received=0;


  }else{
    //No delivery
    //Delivery
    cartTotal.delivery=false;

    //Total
    cartTotal.withDelivery=net-deduct;
    cartTotal.withDeliveryFormat=formatter.format(net-deduct);
    total.totalPrice=net-deduct;

     //modalPayment updated
    modalPayment.totalPrice=net-deduct;
    modalPayment.totalPriceFormat=formatter.format(net-deduct);
    modalPayment.received=0;

  }
  total.lastChange=new Date().getTime();
  cartTotal.lastChange=new Date().getTime();

  cartTotal.expedition=1;

  

 
}

function updateSubTotalPrice(net,expedition){
  updatePrices(net,(cartTotal.deliveryPrice?cartTotal.deliveryPrice:0),expedition)
}

function addToCartVUE(){
  var addCartEndpoint='/cart-add';
  if(CURRENT_TABLE_ID!=null&&CURRENT_TABLE_ID!=undefined){
    addCartEndpoint+="?session_id="+CURRENT_TABLE_ID;
  }

    $("#itemsSelect").val([]);
    $('#itemsSelect').trigger('change');

    axios.post(addCartEndpoint, {
        id: $('#modalID').text(),
        quantity: $('#quantity').val(),
        extras:extrasSelected,
        variantID:variantID
      })
      .then(function (response) {
          if(response.data.status){
            $('#productModal').modal('hide');
            getCartContentAndTotalPrice();

            openNav();
          }else{
            $('#productModal').modal('hide');
            js.notify(response.data.errMsg,"warning");
          }
      })
      .catch(function (error) {
        
      });
}

function getAllOrders(){
  axios.get('/poscloud/orders').then(function (response) {


    
    
    
    orderContent.items=response.data.orders;
    makeFree();
    response.data.orders.forEach(element => {
      makeOcccupied(element.id)
    });
    ordersTotal.totalOrders=response.data.count;
    //updateSubTotalPrice(response.data.total,true);
   })
   .catch(function (error) {
     
   });
}

function doMoveOrder(tableFrom,tableTo){
  
  axios.get('/poscloud/moveorder/'+tableFrom+'/'+tableTo).then(function (response) {
    if(response.data.status){
      js.notify(response.data.message, "success");
      getCartContentAndTotalPrice();
    }else{
      js.notify(response.data.message, "warning");
    }
    
    
  }).catch(function (error) {
    
    js.notify(error, "warning");
  });
}

function withSession(endpoint){
   if(CURRENT_TABLE_ID!=null&&CURRENT_TABLE_ID!=undefined){
    endpoint+="?session_id="+CURRENT_TABLE_ID;
   }
   return endpoint;
}


function clearDeduct(){
  cartTotal.deduct=0;
  $('#coupon_code').val("");
}
/**
 * getCartContentAndTotalPrice
 * This functions connect to laravel to get the current cart items and total price
 * Saves the values in vue
 */
function getCartContentAndTotalPrice(){

  //clear select item


   axios.get(withSession('/cart-getContent-POS')).then(function (response) {
    cartContent.items=response.data.data;
    //cartTotal.deduct=0;


    var obj=response.data.config;
    
    if( Object.keys(obj).length != 0 ){
      expedition.config=response.data.config;

      //Set the dd
      if(response.data.config.delivery_area){
        $("#delivery_area").val(response.data.config.delivery_area);
        $('#delivery_area').trigger('change');
        cartTotal.deliveryPrice=DELIVERY_AREAS[response.data.config.delivery_area];
      }
      if(response.data.config.timeslot){
        $("#timeslot").val(response.data.config.timeslot);
        $('#timeslot').trigger('change');
      }
    }
   
    
    updateSubTotalPrice(response.data.total,EXPEDITION);
   })
   .catch(function (error) {
     
   });

   //On the same call if POS, call get order
   if(IS_POS){
    getAllOrders();
   }
   
 };

 function setDeduct(deduction){
  var formatter = new Intl.NumberFormat(LOCALE, {
    style: 'currency',
    currency:  CASHIER_CURRENCY,
  });
  
  cartTotal.deduct=deduction;
  cartTotal.deductFormat=formatter.format(deduction);
  total.lastChange=null;
  cartTotal.lastChange=null;
  getCartContentAndTotalPrice();
}


function applyDiscount(){
  var code = $('#coupon_code').val();

  axios.post('/coupons/apply', {code: code,cartValue:cartTotal.totalPrice}).then(function (response) {
      if(response.data.status){
          //$("#promo_code_btn").attr("disabled",true);
          //$("#promo_code_btn").attr("readonly");

         // $("#promo_code_war").hide();
          //$("#promo_code_succ").show();

          setDeduct(response.data.deduct);
          js.notify(response.data.msg,"success");

          //$('#promo_code_btn').hide();

          //$( "#coupon_code" ).prop( "disabled", true );
      }else{
          //$("#promo_code_succ").hide();
          //$("#promo_code_war").show();

          js.notify(response.data.msg,"warning");
      }
  }).catch(function (error) {
      
  });
}

function updateExpeditionPOS(){
  var dataToSubmit={
    table_id:CURRENT_TABLE_ID,
    client_name:$('#client_name').val(),
    client_phone:$('#client_phone').val(),
    timeslot:$('#timeslot').val(),
  };
  if(EXPEDITION==1){
    dataToSubmit.delivery_area=$('#delivery_area').val();
    dataToSubmit.client_address=$('#client_address').val();
  }
  
  axios.post(withSession('/poscloud/orderupdate'), dataToSubmit).then(function (response) {

    if(response.data.status){
      js.notify(response.data.message, "success");
    }else{
      js.notify(response.data.message, "warning");
    }
    
    
  }).catch(function (error) {
    
    js.notify(error, "warning");
  });

}

function submitOrderPOS(){
  
  $('#submitOrderPOS').hide();
  $('#indicator').show();
  var dataToSubmit={
    table_id:CURRENT_TABLE_ID,
    paymentType:$('#paymentType').val(),
    expedition:EXPEDITION,
  };
  if(EXPEDITION==1||EXPEDITION==2){
    //Pickup OR deliver
    dataToSubmit.custom={
      client_name:$('#client_name').val(),
      client_phone:$('#client_phone').val(),
    }
    dataToSubmit.phone=$('#client_phone').val();
    dataToSubmit.timeslot=$('#timeslot').val();
    if(EXPEDITION==1){
      dataToSubmit.addressID=$('#client_address').val();
      dataToSubmit.custom.deliveryFee=cartTotal.deliveryPrice;
    }
    
  }

  if(cartTotal.deduct>0){
    dataToSubmit.coupon_code=$('#coupon_code').val();
  }
  
  axios.post(withSession('/poscloud/order'), dataToSubmit).then(function (response) {

   
    
    $('#submitOrderPOS').show();
    $('#indicator').hide();

    $('#modalPayment').modal('hide');
    //Call to get the total price and items
    getCartContentAndTotalPrice();

    if(response.data.status){
      window.showOrders();
      js.notify(response.data.message, "success");
      receiptPOS.order=response.data.order;
      $('#modalPOSInvoice').modal('show');
    }else{
      js.notify(response.data.message, "warning");
    }
    
    
  }).catch(function (error) {
    
    $('#modalPayment').modal('hide');
    $('#submitOrderPOS').show();
    $('#indicator').hide();
    js.notify(error, "warning");
  });
}

/**
 * Removes product from cart, and calls getCartConent
 * @param {Number} product_id
 */
function removeProductIfFromCart(product_id){
    axios.post(withSession('/cart-remove'), {id:product_id}).then(function (response) {
      getCartContentAndTotalPrice();
      
    }).catch(function (error) {
      
    });
 }

 /**
 * Update the product quantity, and calls getCartConent
 * @param {Number} product_id
 */
function incCart(product_id){
  axios.get(withSession('/cartinc/'+product_id)).then(function (response) {
    getCartContentAndTotalPrice();
  }).catch(function (error) {
    
  });
}


function decCart(product_id){
  axios.get(withSession('/cartdec/'+product_id)).then(function (response) {
    getCartContentAndTotalPrice();
  }).catch(function (error) {
    
  });
}

//GET PAGES FOR FOOTER
function getPages(){
    axios.get('/footer-pages').then(function (response) {
      footerPages.pages=response.data.data;
    })
    .catch(function (error) {
      
    });

};

function dineTypeSwitch(mod){
  

  $('.tablepicker').hide();
  $('.takeaway_picker').hide();

  if(mod=="dinein"){
    $('.tablepicker').show();
    $('.takeaway_picker').hide();

    //phone
    $('#localorder_phone').hide();
  }

  if(mod=="takeaway"){
      $('.tablepicker').hide();
      $('.takeaway_picker').show();

    //phone
    $('#localorder_phone').show();
  }

}

function orderTypeSwither(mod){
      

      $('.delTime').hide();
      $('.picTime').hide();

      if(mod=="pickup"){
          updatePrices(cartTotal.totalPrice,null,false)
          $('.picTime').show();
          $('#addressBox').hide();
      }

      if(mod=="delivery"){
          $('.delTime').show();
          $('#addressBox').show();
          getCartContentAndTotalPrice();
      }
}

setTimeout(function(){
  if(typeof initialOrderType !== 'undefined'){
    
    orderTypeSwither(initialOrderType);
  }else{
    
  }

},1000);

function chageDeliveryCost(deliveryCost){
  $("#deliveryCost").val(deliveryCost);
  updatePrices(cartTotal.totalPrice,deliveryCost,true);
  
}

 //First we beed to capture the event of chaning of the address
  function deliveryAddressSwithcer(){
    $("#addressID").change(function() {
      //The delivery cost
      var deliveryCost=$(this).find(':selected').data('cost');

      //We now need to pass this cost to some parrent funct for handling the delivery cost change
      chageDeliveryCost(deliveryCost);


    });

  }

  function deliveryTypeSwitcher(){
    $('.picTime').hide();
    $('input:radio[name="deliveryType"]').change(function() {
      orderTypeSwither($(this).val());
    })
  }

  function dineTypeSwitcher(){
    $('input:radio[name="dineType"]').change(function() {
      $('.delTimeTS').hide();
      $('.picTimeTS').show();
      dineTypeSwitch($(this).val());
    })
  }


  

  function paymentTypeSwitcher(){

    $('input:radio[name="paymentType"]').change(
      
      function(){
      
          //HIDE ALL
          $('#totalSubmitCOD').hide()
          $('#totalSubmitStripe').hide()
          $('#stripe-payment-form').hide()

          //One for all
          $('.payment_form_submiter').hide()
          

          if($(this).val()=="cod"){
              //SHOW COD
              $('#totalSubmitCOD').show();
          }else if($(this).val()=="stripe"){
              //SHOW STRIPE
              $('#totalSubmitStripe').show();
              $('#stripe-payment-form').show()
          }else{
            $('#'+$(this).val()+'-payment-form').show()
          }
      });
  }

  function deliveryAreaSwitcher(){
    $('#delivery_area').on('select2:select', function (e) {
      var data = e.params.data;
      
      updatePrices(cartTotal.totalPrice,DELIVERY_AREAS[data.id],1);
    });
}

window.onload = function () {

  

  //Expedition
  expedition=new Vue({
    el: '#expedition',
    data: {
      config:{}
    },
  })

  //VUE CART
  cartContent = new Vue({
    el: '#cartList',
    data: {
      items: [],
      config:{}
    },
    methods: {
      remove: function (product_id) {
        removeProductIfFromCart(product_id);
      },
      incQuantity: function (product_id){
        incCart(product_id)
      },
      decQuantity: function (product_id){
        decCart(product_id)
      },
    }
  })


  orderContent = new Vue({
    el: '#orderList',
    data: {
      items: [],
    },
    methods:
    {
      openDetails:function(id,receipt_number){
        
        window.openTable(id,"#"+receipt_number);
      }
    }
  })

  //GET PAGES FOR FOOTER
  getPages();

  //Payment Method switcher
  paymentTypeSwitcher();

  //Delivery type switcher
  deliveryTypeSwitcher();

  //For Dine in / takeout
  dineTypeSwitcher();

  //Activate address switcher
  deliveryAddressSwithcer();

  //Activate delivery area switcher
  deliveryAreaSwitcher();


  //VUE FOOTER PAGES
  footerPages = new Vue({
      el: '#footer-pages',
      data: {
        pages: []
      }
  })

  //VUE COMPLETE ORDER TOTAL PRICE
  total = new Vue({
    el: '#totalSubmit',
    data: {
      totalPrice:0
    }
  })


  //VUE TOTAL
  cartTotal= new Vue({
    el: '#totalPrices',
    data: {
      totalPrice:0,
      deduct:0,
      deductFormat:"",
      minimalOrder:0,
      totalPriceFormat:"",
      deliveryPriceFormated:"",
      withDeliveryFormat:"",
      delivery:true
    }
  })

  modalPayment= new Vue({
    el: '#modalPayment',
    data: {
      totalPrice:0,
      minimalOrder:0,
      totalPriceFormat:"",
      deliveryPriceFormated:"",
      delivery:true,
      valid:false,
      received:0
    },
    methods: {
      onChange(event) {
          console.log(event.target.value)
          if(event.target.value=="onlinepayments"||event.target.value=="cardterminal"){
            this.received=this.totalPrice;
          }
      }
  }
  })


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
  

  //VUE TOTAL
  ordersTotal= new Vue({
    el: '#ordersCount',
    data: {
      totalOrders:0,
    }
  })

  //Call to get the total price and items
  getCartContentAndTotalPrice();

  var addToCart1 =  new Vue({
    el:'#addToCart1',
    methods: {
        addToCartAct() {

          addToCartVUE();
        },
    },
  });
}
