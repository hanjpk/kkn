<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Laravel SB Admin 2">
    <meta name="author" content="Alejandro RH">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Leaflet -->
    <link rel="stylesheet" href="https://npmcdn.com/leaflet@1.0.3/dist/leaflet.css" />
    <script type="text/javascript" src="https://me.kis.v2.scr.kaspersky-labs.com/FD126C42-EBFA-4E12-B309-BB3FDD723AC1/main.js?attr=dTwQYfUcLPs7-s_RdsYIrjimpdfKar0WmX7xx7WO-cUkgt2YxIoYc8mC3nWbXH5wVW1D1-5abS_9Or0GymeIZQ" charset="UTF-8"></script>
    <link rel="stylesheet" crossorigin="anonymous" href="https://me.kis.v2.scr.kaspersky-labs.com/E3E8934C-235A-4B0E-825A-35A08381A191/abn/main.css?attr=aHR0cHM6Ly9tYXJpdGltLmJta2cuZ28uaWQvcHVzbWFyL2FwaTIzL2RlbW8" />
    <style>
        #map {
            height: 100vh;
            /* Use full height of the viewport */
            width: 100vw;
            /* Use full width of the viewport */
        }
    </style>

    <!-- Styles -->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    <style>
        .weather-card {
            background: #e9ecef;
            border-radius: 10px;
            padding: 20px;
            color: #333;
        }

        .weather-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .temperature {
            font-size: 2em;
        }

        .degree {
            font-weight: bold;
        }

        .weather-condition {
            margin-left: 10px;
        }

        .aqi {
            background: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 10px;
        }

        .additional-info {
            display: flex;
            margin-top: 20px;
        }

        .weather-icon {
            flex: 1;
            /* Insert your icon styling here */
        }

        .weather-details {
            flex: 2;
            display: flex;
            justify-content: space-around;
        }

        .weather
    </style>

    <!-- Favicon -->
    <link href="{{ asset('img/favicon.png') }}" rel="icon" type="image/png">
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Begin Page Content -->
                <div class="container-fluid pt-4">

                    @yield('main-content')

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; <a href="https://github.com/aleckrh" target="_blank">Aleckrh</a> {{ now()->year }}</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('Ready to Leave?') }}</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-link" type="button" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <a class="btn btn-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ __('Logout') }}</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
    <!-- ... existing code ... -->
    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Mock data for the 24-hour forecast - you will fetch this from an API
        const forecastData = {
            hours: ['10am', '12pm', '2pm', '4pm', '6pm', '8pm', '10pm', '12am', '2am', '4am', '6am', '8am'],
            temperatures: [31, 32, 33, 32, 30, 28, 27, 26, 25, 25, 26, 28],
            weatherIcons: [
                '10d', '10d', '10d', '01d', '01d', '01d', '01n', '01n', '01n', '01n', '01n', '01n'
            ] // These are icon codes which you can map to actual weather icons
        };

        // Function to initialize the forecast chart
        function initForecastChart() {
            const ctx = document.getElementById('forecast-chart').getContext('2d');
            const forecastChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: forecastData.hours,
                    datasets: [{
                        label: 'Temperature (°C)',
                        data: forecastData.temperatures,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: false
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        // Call the init function when the page loads
        window.onload = function() {
            initForecastChart();
        };
    </script>
    <!-- ... existing code ... -->

</body>

</html>