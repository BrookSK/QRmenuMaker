
"use strict";
$(document).ready(function() {
    // Enable pusher logging - don't include this in production
    if(PUSHER_APP_KEY){

        var audio = new Audio('https://soundbible.com/mp3/old-fashioned-door-bell-daniel_simon.mp3');

        Pusher.logToConsole = true;

        var pusher = new Pusher(PUSHER_APP_KEY, {
            cluster: PUSHER_APP_CLUSTER
        });

        var channel = pusher.subscribe('user.'+USER_ID);
        channel.bind('callwaiter-event', function(data) {
            js.notify(data.msg + " " + data.table.restoarea.name+" "+data.table.name,"primary");
            audio.play();
        });

        channel.bind('neworder-event', function(data) {
            js.notify(data.msg + " #" + data.order.id,"primary");
            audio.play();
        });
    }

    function subscribePerOrder(orderID){
        var audio = new Audio('https://soundbible.com/mp3/old-fashioned-door-bell-daniel_simon.mp3');

        Pusher.logToConsole = true;

        var pusherForOrder = new Pusher(PUSHER_APP_KEY, {
            cluster: PUSHER_APP_CLUSTER
        });

        var channelForOrder = pusherForOrder.subscribe('order.'+orderID);
        channelForOrder.bind('updateorder-event', function(data) {
            js.notify(data.msg + " #" + data.order.id,"primary");
            audio.play();
        });
    }
});
