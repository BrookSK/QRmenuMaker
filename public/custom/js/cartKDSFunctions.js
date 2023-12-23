"use strict";
var orderContent=null;
var showFinishedOrders="false";

function getAllOrders(){
    axios.get('/api/v2/kds/orders/'+showFinishedOrders+'?api_token='+TOKEN).then(function (response) {
      console.log("Data received");
      orderContent.items=response.data.data;
     })
     .catch(function (error) {
       console.log(error);
     });
  }

  function doFinishItem(orderID,itemID,isFromDbOrder,call="finishItem"){
    console.log(call+itemID);
    axios.get('/api/v2/kds/orders/'+call+'/'+orderID+'/'+itemID+'/'+(isFromDbOrder?"true":"false")+'?api_token='+TOKEN).then(function (response) {
        console.log("Item finished");
        getAllOrders();
     })
     .catch(function (error) {
       console.log(error);
     });
  }

  function doFinishUnfinishOrder(orderID,isFromDbOrder,call="finishOrder"){
    console.log(call+orderID);
    axios.get('/api/v2/kds/orders/'+call+'/'+orderID+'/'+(isFromDbOrder?"true":"false")+'?api_token='+TOKEN).then(function (response) {
        console.log("Order finished");
        getAllOrders();
     })
     .catch(function (error) {
       console.log(error);
     });
  }

  function showActive(){
    showFinishedOrders="false";
    $('#activeOrders').hide();
    $('#finishedOrders').show();
    getAllOrders();
  }

  function showFinished(){
    showFinishedOrders="true";
    $('#activeOrders').show();
    $('#finishedOrders').hide();
    getAllOrders();
  }

window.onload = function () {
orderContent = new Vue({
    el: '#orderList',
    data: {
      items: [],
    },
    methods:
    {
        finishItem: function (orderID,itemID,isFromDbOrder) {
            doFinishItem(orderID,itemID,isFromDbOrder);
        },
        unfinishItem: function (orderID,itemID,isFromDbOrder) {
            doFinishItem(orderID,itemID,isFromDbOrder,"unfinishItem");
        },
        finishOrUnfinishOrder: function (orderID,isFromDbOrder,isFinished) {
            doFinishUnfinishOrder(orderID,isFromDbOrder,isFinished=="1"?"unfinishOrder":"finishOrder");
        },
    }
  })
  setTimeout(() => {
    getAllOrders();
  }, 1000);
  
};