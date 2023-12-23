<div id="tracking_map" class="form-control form-control-alternative"></div>
<script>

  var start="/images/pin.png"
  var end="/images/green_pin.png"
  var driver="/images/blue_pin.png"

//Not in use - can be if you use direction API
function calculateRoute(from, to) {
        // Center initialized to Naples, Italy
        var myOptions = {
          zoom: 10,
          center: new google.maps.LatLng(41.84, 23.65),
          mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        // Draw the map
        var mapObject = new google.maps.Map(document.getElementById("tracking_map"), myOptions);

        var directionsService = new google.maps.DirectionsService();
        var directionsRequest = {
          origin: from,
          destination: to,
          travelMode: google.maps.DirectionsTravelMode.DRIVING,
          unitSystem: google.maps.UnitSystem.METRIC
        };
        directionsService.route(
          directionsRequest,
          function(response, status)
          {
            if (status == google.maps.DirectionsStatus.OK)
            {
              new google.maps.DirectionsRenderer({
                map: mapObject,
                directions: response
              });
            }
            else
              $("#error").append("Unable to retrieve your route<br />");
          }
        );
}

function changeMarkerPosition(marker) {
    var latlng = new google.maps.LatLng(40.748774, -73.985763);
    marker.setPosition(latlng);
}


function initTheTrackingMap(currentPosiotion,){
    var map = new google.maps.Map(document.getElementById('tracking_map'), {
        zoom: 13,
        center:  new google.maps.LatLng({{ $order->address?$order->address->lat:$order->restorant->lat }}, {{ $order->address?$order->address->lng:$order->restorant->lng }}),
    });

  //Marker Start - Restorant position
  var marker = new google.maps.Marker({
    position: new google.maps.LatLng({{ $order->restorant->lat }}, {{ $order->restorant->lng }}),
    map: map,
    icon: start,
    title: '{{ $order->restorant->name }}'
  });

  //Marker end - Client address
  var marker = new google.maps.Marker({
    position: new google.maps.LatLng({{  $order->address?$order->address->lat:$order->restorant->lat }}, {{  $order->address?$order->address->lng:$order->restorant->lng }}),
    map: map,
    icon: end,
    title: '{{ $order->client?$order->client->name:$order->getConfig("client_name","") }}'
  });

  return map;

}

function getOrderLocation(){
  axios.get('/ordertracingapi/{{ $order->id }}').then(function (response) {
    
    if(response.data.status=="tracing"){
      markerData=new google.maps.LatLng(response.data.lat, response.data.lng);
      if(driverMarker==null){
          //Create the marker
          driverMarker = new google.maps.Marker({
            position: markerData,
            map: map,
            icon:driver,
            title: 'Driver'
          });
          
        }else{

          //Update marker location
          driverMarker.setPosition(markerData);
        }
    }else{
      return null
    }
  })
  .catch(function (error) {
    
  });
};

var map = null;
var driverMarker=null;

window.onload = function () {
    map = initTheTrackingMap();
    setInterval(getOrderLocation,5000)
 }
</script>


<style type="text/css">
    #tracking_map {
      height: 400px;
    }
  </style>