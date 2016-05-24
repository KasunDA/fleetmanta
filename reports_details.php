<?php
include('Functions/select_locations.php');
//include('Functions/require.php');
$reportid = $_GET['reportid'];
if (!isset($reportid)) {
    header('Location: reports.php');
    die();
}
include('includes/header.php');
?>
<main>
    <div id="reportMap"></div>
    <div class="row">
        <div class="col s12">
            <div class="card">
                <div class="card-content blue-grey darken-1">
                    <span class="card-title white-text">R-00<?php echo $reportid; ?></span>
                </div>
                <div class="card-content">
                    <table class="striped highlight">
                        <thead>
                            <tr>
                                <th data-field="desc">Routes</th>
                                <th data-field="idorder">ID. Order</th>
                                <th data-field="qty">Load</th>
                                <th data-field="dist">Distance</th>
                                <th data-field="dist">Trucks</th>
                                <th data-field="dist">Cost</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            include('Functions/select_reports_details.php');
                            foreach ($orders->array as $order) {
                                echo "
                                <tr>
                                <td>{$order['description']}</td>
                                <td>OR-TS-00{$order['orderid']}</td>
                                <td>{$order['quantity']}</td>
                                <td>{$order['distance_in_m']} m</td>
                                <td>TR-00{$order['trucks_idtruck']}</td>
                                <td>IDR {$order['cost']}.00</td>
                                </tr>
                                ";
                            }
                            ?>
                        </tbody>
                    </table>
                    <?php include('Functions/select_cost.php');
                        echo "<button class='waves-effect waves-light btn long'>Total Cost: IDR {$totalcost->array[0]['cost']}</button>";
                    ?>
                </div>
            </div>
        </div>
    </div>
</main>
<?php include('includes/scripts.php');
include('Functions/select_optim_route.php');
?>
<!--Google maps API-->
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBZXB3DMls7nIoTpVeC2PjLy4dQmjPAe0E&callback=initialize"
type="text/javascript"></script>
<script>
//initialize google maps
var waypoint = <?=json_encode($stops->array);?>;
var optim = <?=json_encode($optim->array);?>;
function initialize() {
    var markerArray = [];
    // Instantiate a directions service.
    var directionsService = new google.maps.DirectionsService;

    var onChangeHandler = function () {
        storeDistance(orilat,orilang,destlat,destlang);
    };

    //map prpoerty set to PELINDO and TPKS locations
    var mapProp = {
        center:new google.maps.LatLng(-7.207069000596542, 112.72695779800415),
        zoom:8,
        mapTypeId:google.maps.MapTypeId.HYBRID,
        draggable:true
    };

    //initialize google maps
    var map=new google.maps.Map(document.getElementById("reportMap"),mapProp);

    // Create a renderer for directions and bind it to the map.
    var directionsDisplay = new google.maps.DirectionsRenderer({map: map, polylineOptions: {
      strokeColor: 'red'
    }});

    // Instantiate an info window to hold step text.
    var stepDisplay = new google.maps.InfoWindow;

    calculateAndDisplayRoute(directionsDisplay, directionsService, markerArray, stepDisplay, map);

    function calculateAndDisplayRoute(directionsDisplay, directionsService, markerArray, stepDisplay, map) {
        var wypoint = [];
        for (var i = 1; i < optim.length; i++) {
            wypoint.push({
                location: new google.maps.LatLng(optim[i].lat,optim[i].lang)}
            );
        }
        // First, remove any existing markers from the map.
        for (var i = 0; i < markerArray.length; i++) {
            markerArray[i].setMap(null);
        }

        // Retrieve the start and end locations and create a DirectionsRequest using
        // WALKING directions.
        directionsService.route({
            origin: new google.maps.LatLng(waypoint[0].lat, waypoint[0].lang),
            destination: new google.maps.LatLng(waypoint[0].lat, waypoint[0].lang),
            waypoints: wypoint,
            travelMode: google.maps.TravelMode.WALKING
        }, function(response, status) {
            // Route the directions and pass the response to a function to create
            // markers for each step.
            if (status === google.maps.DirectionsStatus.OK) {
                directionsDisplay.setDirections(response);

                // showSteps(response, markerArray, stepDisplay, map);
            } else {
                window.alert('Directions request failed due to ' + status);
            }
        });
    }

    function showDestinationMarker(directionResult) {
        var marker = new google.maps.Marker({
            position: directionResult.routes[0].legs[0].end_location,
            map: map
        });
        attachMarkerText(marker, "The end.");
        markerArray[0] = marker;
    }

    function attachMarkerText(marker, text) {
        google.maps.event.addListener(marker, 'click', function() {
            // Open an info window when the marker is clicked on,
            // containing the text of the step.
            stepDisplay.setContent(text);
            stepDisplay.open(map, marker);
        });
    }

    function showSteps(directionResult, markerArray, stepDisplay, map) {
        // For each step, place a marker, and add the text to the marker's infowindow.
        // Also attach the marker to an array so we can keep track of it and remove it
        // when calculating new routes.
        var myRoute = directionResult.routes[0].legs[0];
        for (var i = 0; i < myRoute.steps.length; i++) {
            var marker = markerArray[i] = markerArray[i] || new google.maps.Marker;
            marker.setMap(map);
            marker.setPosition(myRoute.steps[i].start_location);
            // attachInstructionText(stepDisplay, marker, myRoute.steps[i].instructions, map);
        }
    }

    function attachInstructionText(stepDisplay, marker, text, map) {
        google.maps.event.addListener(marker, 'click', function() {
            // Open an info window when the marker is clicked on, containing the text
            // of the step.
            stepDisplay.setContent(text);
            stepDisplay.open(map, marker);
        });
    }
}

</script>
<script>
$(document).ready(function(){
    $(".button-collapse").sideNav();
    $( ".collapsible-header" ).click(function() {
        if ($(this).find(".material-icons.right").css( "transform" ) == "none" ){
            $(this).find(".material-icons.right").css("transform","rotate(90deg)");
        } else {
            $(this).find(".material-icons.right").css("transform","" );
        }
    });
})
</script>
</body>
</html>
