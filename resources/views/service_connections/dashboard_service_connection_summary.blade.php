<div>
    <span class="text-muted" style="margin-left: 3px;"><i class="fas fa-hard-hat ico-tab-mini"></i><strong>Service Connections</strong> Statistics</span>
    <div class="row mt-3">
        {{-- STATUS COUNT --}}
        <div class="col-lg-3">
            <div class="card shadow-none">
                <div class="card-body p-0 mt-3 mb-2">
                    <div class="inner">
                        <p class="no-pads text-muted text-center">New Applications</p>
                        <h1 class="text-center strong text-xxl text-success mt-3" id="new-applications">...</h1>
                    </div>
                    <div class="px-3">
                        <a href="#" id="new-applications-btn" class="btn btn-block btn-transparent" title="New Applications for Inspection"  data-toggle="modal" data-target="#modal-stats">View <i class="fas fa-arrow-circle-right ico-tab-left-mini"></i></a>
                    </div>
                </div>               
            </div>  
        </div>

        <div class="col-lg-3"> 
            <div class="card shadow-none">
                <div class="card-body p-0 mt-3 mb-2">
                    <div class="inner">
                        <p class="no-pads text-muted text-center">For Meter Assigning</p>
                        <h1 class="text-center strong text-xxl text-success mt-3" id="for-meter-assigning">...</h1>
                    </div>
                    <div class="px-3">
                        <a href="{{ route('serviceConnectionMtrTrnsfrmrs.assigning') }}" class="btn btn-block btn-transparent" title="Applications to be assigned with an electric meter">View <i class="fas fa-arrow-circle-right ico-tab-left-mini"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card shadow-none">
                <div class="card-body p-0 mt-3 mb-2">
                    <div class="inner">
                        <p class="no-pads text-muted text-center">For Energization</p>
                        <h1 class="text-center strong text-xxl text-success mt-3" id="for-energization">...</h1>
                    </div>
                    <div class="px-3">
                        <a href="{{ route('serviceConnections.energization') }}" class="btn btn-block btn-transparent" title="Applications for Energization">View <i class="fas fa-arrow-circle-right ico-tab-left-mini"></i></a>
                    </div>
                </div>
            </div>
        </div>

        {{-- APPLICATION AND ENERGIZATION TREND GRAPH --}}
        <div class="col-lg-6">            
            <div class="card shadow-none" style="height: 40vh;">
                <div class="card-header border-0">
                    <span class="card-title"><i class="fas fa-chart-area ico-tab"></i>Trend of Service Connection Applications and Energizations</span>
                </div>
                <div class="card-body">
                    <canvas id="application-chart-canvas" height="300" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL FOR APPROVED AND FOR PAYMENT --}}
<div class="modal fade" id="modal-stats" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal-title">Approved Applicants</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-hover" id="approved-table">
                    <thead>
                        <th>ID</th>
                        <th>Service Account Name</th>
                        <th>Type</th>
                        <th>Address</th>
                        <th>Verifier</th>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

@push('page_scripts')
    <script>
        $(document).ready(function() {
            /**
             * FOR INSPECTION/ NEW APPLICATIONS
             */
            $.ajax({
                url : '{{ route("home.get-new-service-connections") }}',
                type: "GET",
                dataType : "json",
                success : function(response) {
                    console.log(response.length);
                    $('#new-applications').text(response.length);
                },
                error : function(error) {
                    $('#new-applications').text("Error!");
                }
            })

            $('#new-applications-btn').on('click', function() {
                $('#modal-title').text('New Applications')
                $.ajax({
                    url : '{{ route("home.get-new-service-connections") }}',
                    type: "GET",
                    dataType : "json",
                    success : function(response) {
                        $('#approved-table tbody tr').remove();
                        $.each(response, function(index, element) {
                            console.log(response[index]['id']);
                            $('#approved-table tbody').append(`<tr><td><a href="{{ url('serviceConnections/') }}/` + response[index]["id"] + `">` + response[index]['id'] + `</a></td><td>` + response[index]['ServiceAccountName'] + `</td><td>` + response[index]['ConnectionApplicationType'] + `</td><td>` + response[index]['Barangay'] + `, ` + response[index]['Town'] + `</td><td>` + (jQuery.isEmptyObject(response[index]['name']) ? 'n/a' : response[index]['name']) + `</td></tr>`);
                        });
                    },
                    error : function(error) {
                        // alert(error);
                        Toast.fire({
                            icon : 'error',
                            text : 'Error showing new applications'
                        })
                    }
                })
            })

            /**
             * UNASSIGNED METERS 
             */
            $.ajax({
                url : '{{ route("home.get-unassigned-meters") }}',
                type: "GET",
                dataType : "json",
                success : function(response) {
                    console.log(response.length);
                    $('#for-meter-assigning').text(response.length)
                },
                error : function(error) {
                    
                }
            })

            /**
             * FOR ENERGIZATION 
             */
            $.ajax({
                url : '{{ route("home.get-for-engergization") }}',
                type: "GET",
                dataType : "json",
                success : function(response) {
                    console.log(response.length);
                    $('#for-energization').text(response.length);
                },
                error : function(error) {
                    // alert(error);
                    console.log(error)
                    console.log('Server error!');
                }
            })

            getApplicationChartData()
        })

        function getApplicationChartData() {
            /**
             * APPLICATION TREND CHART
             */      
            var applicationsChartCanvas = document.getElementById('application-chart-canvas').getContext('2d')
            // $('#application-chart-canvas').get(0).getContext('2d');
            //get previous 6 months
            var prevMonths = [];
            for (var i=0; i<6; i++) {
                prevMonths.push(moment().subtract(i, 'months').format('MMM Y'))
            }

            $.ajax({
                url : '{{ url("/service_connections/fetch-application-count-via-status") }}',
                type : 'GET',
                success : function(res) {
                    var applicationData = []
                    var energizationData = []

                    applicationData.push(res[0]['ApplicationOne'])
                    applicationData.push(res[0]['ApplicationTwo'])
                    applicationData.push(res[0]['ApplicationThree'])
                    applicationData.push(res[0]['ApplicationFour'])
                    applicationData.push(res[0]['ApplicationFive'])
                    applicationData.push(res[0]['ApplicationSix'])

                    energizationData.push(res[0]['EnergizationOne'])
                    energizationData.push(res[0]['EnergizationTwo'])
                    energizationData.push(res[0]['EnergizationThree'])
                    energizationData.push(res[0]['EnergizationFour'])
                    energizationData.push(res[0]['EnergizationFive'])
                    energizationData.push(res[0]['EnergizationSix'])
                    
                    var applicationChartData = {
                        labels: prevMonths,
                        datasets: [
                        {
                            label: 'Received',
                            backgroundColor: 'rgba(196, 189, 93, .8)',
                            borderColor: '#c4bd5d',
                            pointRadius: true,
                            pointColor: '#3b8bba',
                            pointStrokeColor: '##c4bd5d',
                            pointHighlightFill: '#fff',
                            pointHighlightStroke: '#c4bd5d',
                            data: applicationData
                        },
                        {
                            label: 'Energized',
                            backgroundColor: '#17bf87',
                            borderColor: '#17bf87',
                            pointRadius: true,
                            pointColor: '#17bf87',
                            pointStrokeColor: '#c1c7d1',
                            pointHighlightFill: '#fff',
                            pointHighlightStroke: '#0b855c',
                            data: energizationData
                        }
                        ]
                    }

                    var applicationsChartOptions = {
                        maintainAspectRatio: false,
                        responsive: true,
                        legend: {
                            display: true
                        },
                        scales: {
                            xAxes: [{
                                gridLines: {
                                    display: false
                                }
                            }],
                            yAxes: [{
                                gridLines: {
                                    display: false
                                }
                            }]
                        }
                    }

                    // This will get the first returned node in the jQuery collection.
                    // eslint-disable-next-line no-unused-vars
                    var applicationsChart = new Chart(applicationsChartCanvas, { // lgtm[js/unused-local-variable]
                        type: 'line',
                        data: applicationChartData,
                        options: applicationsChartOptions
                    })
                },
                error : function(error) {
                    console.log(error)
                }
            })
        }
    </script>
@endpush