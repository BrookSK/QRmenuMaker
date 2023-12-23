"use strict";
var map, infoWindow, marker, lng, lat;
function initMap() {}

function initMapR(){
    map = new google.maps.Map(document.getElementById('map'), {center: {lat: -34.397, lng: 150.644}, zoom: 15 });
    marker = new google.maps.Marker({ position: {lat: -34.397, lng: 150.644}, map: map, title: 'Click to zoom'});
    infoWindow = new google.maps.InfoWindow;

    getLocation(function(isFetched, currPost){
        if(isFetched){
            if(currPost.lat != 0 && currPost.lng != 0){
                map.setCenter(currPost);
                marker.setPosition(currPost);
            }else{
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                      var pos = { lat: position.coords.latitude, lng: position.coords.longitude };
                      map.setCenter(pos);
                      marker.setPosition(pos);
                      changeLocation(pos.lat, pos.lng);
                    }, function() {
                    });
                } else {
                }
            }
        }
    });

    map.addListener('click', function(event) {
        var currPos = new google.maps.LatLng(event.latLng.lat(),event.latLng.lng());
        marker.setPosition(currPos);
        changeLocation(event.latLng.lat(), event.latLng.lng());
    });
}

function getLocation(callback){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        type:'GET',
        url: '/get/rlocation/'+$('#rid').val(),
        success:function(response){
            if(response.status){
                return callback(true, response.data)
            }
        }, error: function (response) {
           return callback(false, response.responseJSON.errMsg);
        }
    })
}

function changeLocation(lat, lng){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        type:'POST',
        url: '/updateres/location/'+$('#rid').val(),
        dataType: 'json',
        data: {
            lat: lat,
            lng: lng
        },
        success:function(response){
            if(response.status){
                
            }
        }, error: function (response) {
        }
    })
}

function initMapA() {
    map = new google.maps.Map(document.getElementById('map2'), {center: {lat: -34.397, lng: 150.644}, zoom: 15 });
    marker = new google.maps.Marker({ position: {lat: -34.397, lng: 150.644}, map: map, title: 'Click to zoom'});
    infoWindow = new google.maps.InfoWindow;

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
        var pos = { lat: position.coords.latitude, lng: position.coords.longitude };

            map.setCenter(pos);
            marker.setPosition(pos);
            lat = position.coords.latitude;
            lng = position.coords.longitude;
        }, function() {
        });
    } else {
    }

    map.addListener('click', function(event) {
        var currPos = new google.maps.LatLng(event.latLng.lat(),event.latLng.lng());
        marker.setPosition(currPos);

        lat = event.latLng.lat()
        lng = event.latLng.lng();
    });
}

$("#submitNewAddress").on("click",function() {
    saveLocation(lat, lng);
});

function saveLocation(lat, lng){
    var new_address = $('#new_address').val();

    if(new_address.length > 0){

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type:'POST',
            url: '/addresses',
            dataType: 'json',
            data: {
                new_address: new_address,
                lat: lat,
                lng: lng
            },
            success:function(response){
                if(response.status){
                    window.location.href = "/addresses";
                }
            }, error: function (response) {
            }
        })
    }
}

function handleLocationError(browserHasGeolocation, infoWindow, pos) {
    infoWindow.setPosition(pos);
    infoWindow.setContent(browserHasGeolocation ? 'Error: The Geolocation service failed.' : 'Error: Your browser doesn\'t support geolocation.');
    infoWindow.open(map);
}

