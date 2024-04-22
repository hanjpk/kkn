@extends('layouts.admin')

@section('main-content')

<div class="container-fluid">
    <!-- Top Bar: Date, Time, and Weather Warnings -->
    <div class="row">
        <div class="col-12">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Weather Alert:</strong> High winds expected today.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <h1 class="h3 mb-0 text-gray-800 text-center">Maritime Information Dashboard</h1>
            <p class="text-gray-800 text-center"><strong>Date/Time:</strong> <span id="datetime"></span></p>
        </div>
    </div>

    <!-- Main Dashboard Layout -->
    <div class="row">
        <!-- Left Side: Weather and Tidal Information -->
        <div class="col-md-3">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Weather Details</h6>
                </div>
                <div class="card-body">
                    <p><strong>Temperature:</strong> 23°C</p>
                    <p><strong>Humidity:</strong> 78%</p>
                    <p><strong>Wind Speed:</strong> 12 km/h</p>
                    <p><strong>Tide:</strong> High at 15:00</p>
                </div>
            </div>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">24H Forecast</h6>
                </div>
                <div class="card-body">
                    <div id="forecast-chart" style="width: 100%; height: 200px;">
                        <!-- Forecast Chart Will Go Here -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Center: Map with Radar and Wind Direction -->
        <div class="col-md-6">
            <!-- Weather Details Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="weather-card">
                        <div class="weather-info">
                            <div class="temperature">
                                <span class="degree">31°</span>
                                <span class="weather-condition">Few Clouds</span>
                            </div>
                            <div class="aqi">
                                <span class="aqi-value">AQI 45</span>
                            </div>
                        </div>
                        <div class="additional-info">
                            <div class="weather-icon">
                                <!-- Insert weather icon here -->
                            </div>
                            <div class="weather-details">
                                <div class="weather-item">
                                    <span class="weather-value">4KM/H</span>
                                    <span class="weather-label">Wind</span>
                                </div>
                                <div class="weather-item">
                                    <span class="weather-value">70%</span>
                                    <span class="weather-label">Humidity</span>
                                </div>
                                <!-- More weather details here -->
                            </div>
                        </div>
                        <div class="summary">
                            <p>Today: It's Thundershower, the temperature is about the same as yesterday. AQI is good.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Map with Radar and Wind Direction -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div id="map" style="width: 100%; height: 500px;"></div>
                </div>
            </div>
        </div>

        <!-- Right Side: Moon Phases, Sun Phases, Marine Traffic -->
        <div class="col-md-3">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Moon and Sun Phases</h6>
                </div>
                <div class="card-body">
                    <p><strong>Moon Phase:</strong> Waxing Crescent</p>
                    <p><strong>Sunrise:</strong> 06:22 AM</p>
                    <p><strong>Sunset:</strong> 07:45 PM</p>
                </div>
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Marine Traffic</h6>
                </div>
                <div class="card-body">
                    <p>Vessels nearby: 5</p>
                    <p>Nearest vessel: 2 km N</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet -->
<script src="https://npmcdn.com/leaflet@1.0.3/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://onaci.github.io/leaflet-velocity/dist/leaflet-velocity.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://npmcdn.com/leaflet@1.0.3/dist/leaflet.js/leaflet.icon-pulse.js"></script>
<script>
    var Esri_WorldImagery = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, ' +
            'AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
    });

    var baseLayers = {
        "Satellite": Esri_WorldImagery,
    };

    var map = L.map('map', {
        layers: [Esri_WorldImagery]
    });

    map.setView([-6.0322, 106.7141], 11);

    var layerControl = L.control.layers(baseLayers);
    layerControl.addTo(map);

    var livePinLocation = [-6.097557918736245, 106.71410276591264];
    var marker = L.marker(livePinLocation, {
        icon: L.icon({
            iconUrl: '{{ asset('img/location.png') }}', // Specify your icon image path
            iconSize: [24, 24], // Size of the icon
            iconAnchor: [12, 12] // Point of the icon which will correspond to marker's location
        }),
        bounceOnAdd: true, // Enable bouncing effect
        bounceOnAddOptions: {duration: 1000, height: 100}, // Duration and height of the bouncing
        bounceOnAddCallback: function() {console.log("marker added");} // Callback for completion
    }).addTo(map);

    
    
    getModelRunDates();

    function getModelRunDates() {
        $.get('https://maritim.bmkg.go.id/pusmar/api23/modelrun', function(res) {
            const dateTime = formatDate(res.inaflows[0]);
            console.log("Formatted Model Run Date Time:", dateTime); // Print the formatted date to the console
            getCurrentArray(dateTime);
            getWaveArray(dateTime);
            getWindArray(dateTime);
        }).fail(function(error) {
            console.error('Failed to fetch model run dates:', error);
        });
    }

    function formatDate(dateStr) {
    const date = new Date(dateStr);
    // Create a zero-padded function for single-digit components
    function pad(number) {
        if (number < 10) {
            return '0' + number;
        }
        return number;
    }
    // Format date to 'YYYYMMDDHHMM'
    return date.getUTCFullYear() +
           pad(date.getUTCMonth() + 1) +
           pad(date.getUTCDate()) +
           pad(date.getUTCHours()) +
           pad(date.getUTCMinutes());
    }

    function getCurrentArray(dateTime) {
        const url = `https://maritim.bmkg.go.id/pusmar/api23/arr_req/inaflows/cur/${dateTime}/${dateTime}/0`;
        console.log("Current Array URL:", url); // Debug the URL
        $.get(url, function(res) {
            var data = JSON.parse(res);
            console.log(data);
            var vel = L.velocityLayer({
                displayValues: true,
                displayOptions: {
                    velocityType: 'GBR Water',
                    displayPosition: 'bottomleft',
                    displayEmptyString: 'No water data',
                },
                data: data,
                maxVelocity: 2.5,
                velocityScale: 0.2
            });
            layerControl.addOverlay(vel, 'Current - Inaflows');
            vel.addTo(map);
        }).fail(function(error) {
            console.error(error);
         });
    }

    function getWaveArray(dateTime) {
        const url = `https://maritim.bmkg.go.id/pusmar/api23/arr_req/inawaves/dir/${dateTime}/${dateTime}`;
        console.log("Wave Array URL:", url); // Debug the URL
        $.get(url, function(res) {
            var data = JSON.parse(res);
            console.log(data);
            var vel = L.velocityLayer({
                displayValues: true,
                displayOptions: {
                    velocityType: 'GBR Water',
                    displayPosition: 'bottomleft',
                    displayEmptyString: 'No water data',
                },
                data: data,
                maxVelocity: 4,
                velocityScale: 0.1 
            });
            layerControl.addOverlay(vel, 'Wave - Inawaves');
            // vel.addTo(map);
        }).fail(function(error) {
            console.error('API Call Failed:', error);
        });
    }

    function getWindArray(dateTime) {
        const url = `https://maritim.bmkg.go.id/pusmar/api23/arr_req/inawaves/wind/${dateTime}/${dateTime}`;
        console.log("Wind Array URL:", url); // Debug the URL
        $.get(url, function(res) {
            var data = JSON.parse(res);
            console.log(data);
            var vel = L.velocityLayer({
                displayValues: true,
                displayOptions: {
                    velocityType: 'GBR Water',
                    displayPosition: 'bottomleft',
                    displayEmptyString: 'No water data',
                },
                data: data,
                maxVelocity: 20,
                velocityScale: 0.01 
            });
            layerControl.addOverlay(vel, 'Wind - Inawaves');
            // vel.addTo(map);
        }).fail(function(error) {
            console.error('API Call Failed:', error);
        });
    }


</script>

@endsection