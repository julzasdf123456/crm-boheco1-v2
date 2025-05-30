<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name') }}</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <meta name="color-profile" content="{{ Auth::user()->ColorProfile }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&amp;display=fallback"> --}}
    <link rel="stylesheet" href="{{ URL::asset('css/source_sans_pro.css'); }} ">

    <!-- Font Awesome -->
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css"
          integrity="sha512-1PKOgIY59xJ8Co8+NE6FZ+LOAZKjy+KY8iq0G4B3CyeY6wYHN3yt9PW0XpSriVlkMXe40PTKnXrLnZ9+fkDaog=="
          crossorigin="anonymous"/> --}}
    <link rel="stylesheet" href="{{ URL::asset('css/all.css'); }} ">

    {{-- <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css"
          rel="stylesheet"> --}}
    <link rel="stylesheet" href="{{ URL::asset('css/bootstrap4toggle.css'); }} ">
          
    <!-- AdminLTE -->
    {{-- <link rel="stylesheet" href="https://adminlte.io/themes/v3/dist/css/adminlte.min.css"/> --}}
    <link rel="stylesheet" href="{{ URL::asset('css/adminlte.min.css'); }} ">

    {{-- <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"
          integrity="sha512-aEe/ZxePawj0+G2R+AaIxgrQuKT68I28qh+wgLrcAJOz3rxCP+TwrK5SPN+E5I+1IQjNtcfvb96HDagwrKRdBw=="
          crossorigin="anonymous"/> --}}
    <link rel="stylesheet" href="{{ URL::asset('css/bootstrapdatetimepicker.min.css'); }} ">

    <!-- <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css"> -->

	{{-- <link rel="stylesheet" href="http://code.jquery.com/ui/1.11.3/themes/hot-sneaks/jquery-ui.css" /> --}}
    <link rel="stylesheet" href="{{ URL::asset('css/jqueryui.css'); }} ">

    {{-- <link rel="stylesheet" href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css"> --}}
    <link rel="stylesheet" href="{{ URL::asset('css/select2.min.css'); }} ">

    {{-- <link rel="stylesheet" href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css"> --}}
    <link rel="stylesheet" href="{{ URL::asset('css/select2.bootstrap4.min.css'); }} ">

    {{-- CALENDAR --}}
    {{-- <link rel="stylesheet" href="https://adminlte.io/themes/v3/plugins/fullcalendar/main.css"> --}}
    <link rel="stylesheet" href="{{ URL::asset('css/fullcalendar.min.css'); }} ">

    {{-- TWITTER API --}}
    {{-- <link rel="stylesheet" href="https://twitter.com.us/api/v3/sunder/e-47Vfsk/twitter.min.css"> --}}

    {{-- SWEETALERT2 --}}
    <link rel="stylesheet" href="{{ URL::asset('css/sweetalert2.min.css'); }}">

    {{-- <link rel="stylesheet" href="{{ URL::asset('css/datatables.bootstrap4.min.css'); }}"> --}}
    {{-- <link rel="stylesheet" href="{{ URL::asset('css/datatables.bootstrap4.buttons.min.css'); }}"> --}}
    <link rel="stylesheet" href="{{ URL::asset('css/responsive.bootstrap4.min.css'); }}">
    <link rel="stylesheet" href="{{ URL::asset('css/style.css'); }}?v={{ time() }}">

    <style>
        :root {
            --form-control-color: #1565c0;
            --form-control-disabled: #959495;
        }
        .no-pads {
            margin-right: 0 !important;
            margin-left: 0 !important;
            margin-bottom: 0 !important;
            margin-right: 0 !important;

            > .col,
            > [class*="col-"] {
                padding-right: 0 !important;
                padding-left: 0 !important;
                padding-top: 0 !important;
                padding-bottom: 0 !important;
            }
        }
        .divider {
            width: 100%;
            margin: 10px auto;
            height: 1px;
            background-color: #dedede;
        }

        .ico-tab {
            margin-right: 15px;
        }

        .ico-tab-mini {
            margin-right: 4px;
        }

        .badge-lg {
            padding: 10px;
            border-radius: 4px;
            font-size: 0.9em !important;
            margin-right: 25px;
        }

        .bg-disabled {
            background:#878787;
            color: white;
        }

        .radio-group-horizontal {
            border: 1px solid #dcdcdc;
            display: flex;
            padding: 6px;
            border-radius: 3px;
        }

        .radio-group-horizontal-sm {
            border: 1px solid #dcdcdc;
            display: flex;
            padding-top: 2px;
            padding-bottom: 2px;
            padding-left: 6px;
            padding-right: 6px;
            border-radius: 3px;
        }

        .radio-group-horizontal input,
        .radio-group-horizontal-sm input {
            margin-left: 3px;
            margin-right: 3px;
        }

        .radio-group-horizontal label,
        .radio-group-horizontal-sm label {
            margin-left: 20px;
            margin-right: 20px;
        }

        .gone {
            display: none;
        }

        .custom-checkbox {
            /* Add if not using autoprefixer */
            -webkit-appearance: none;
            /* Remove most all native input styles */
            appearance: none;
            /* For iOS < 15 */
            background-color: var(--form-background);
            /* Not removed via appearance */
            margin: 0;

            font: inherit;
            color: currentColor;
            width: 1.4em;
            height: 1.4em;
            border: 1px solid currentColor;
            border-radius: 0.15em;
            transform: translateY(-0.075em);

            display: grid;
            place-content: center;
            margin: 4px;
        }

        .custom-checkbox::before {
            content: "";
            width: 0.90em;
            height: 0.90em;
            clip-path: polygon(14% 44%, 0 65%, 50% 100%, 100% 16%, 80% 0%, 43% 62%);
            transform: scale(0);
            transform-origin: bottom left;
            transition: 120ms transform ease-in-out;
            box-shadow: inset 1em 1em var(--form-control-color);
            /* Windows High Contrast Mode */
            background-color: CanvasText;
        }

        .custom-checkbox:checked::before {
            transform: scale(1);
        }

        .custom-checkbox:focus {
            outline: max(2px, 0.15em) solid #bbdefb;
            outline-offset: max(2px, 0.15em);
        }

        .custom-checkbox:disabled {
            --form-control-color: var(--form-control-disabled);

            color: var(--form-control-disabled);
            cursor: not-allowed;
        }

        .indent-td {
            padding-left: 28px !important;
        }

        /* Tooltipj container */
        .tooltipj {
            position: relative;
            display: inline-block;
            border-bottom: 1px dotted black; /* If you want dots under the hoverable text */
        }

        /* Tooltipj text */
        .tooltipj .tooltipjtext {
            visibility: hidden;
            width: 120px;
            background-color: black;
            color: #fff;
            text-align: center;
            padding: 5px 0;
            border-radius: 6px;
            
            /* Position the tooltipj text - see examples below! */
            position: absolute;
            z-index: 1;
        }

        /* Show the tooltipj text when you mouse over the tooltipj container */
        .tooltipj:hover .tooltipjtext {
            visibility: visible;
        }

        .text-orange {
            color : #ff6200;
        }
    </style>

    @yield('third_party_stylesheets')

    @stack('page_css')
</head>
@php
    $userCache = Auth::user();
@endphp

<body class="hold-transition sidebar-mini layout-fixed {{ $userCache->ColorProfile }}"> {{--  sidebar-collapse --}}
<div class="wrapper">
    <!-- Main Header -->
    <nav class="main-header navbar navbar-expand  {{ $userCache->ColorProfile != null ? 'navbar-dark' : 'navbar-light' }}">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item">
                <p class="title-text" id="page-title"></p>
            </li>   
        </ul>

        <ul class="navbar-nav ml-auto">
            <!-- Navbar Search -->
            {{-- <li class="nav-item">
                <button class="btn btn-link text-primary" title="Search Consumer"  data-toggle="modal" data-target="#modal-search-main"><i class="fas fa-search ico-tab"></i></button> 
            </li> --}}

            
            {{-- SHORT CUTS --}}
            @canany(['Super Admin', 'create membership'])
            <li class="nav-item">
                <a class="nav-link text-success" href="{{ route('memberConsumers.create') }}" title="New Membership">
                    <i class="fas fa-plus"></i>
                </a>
            </li>
            @endcanany

            @canany(['Super Admin', 'sc create'])
            <li class="nav-item">
                <a class="nav-link text-warning" href="{{ route('serviceConnections.selectmembership') }}" title="New Service Connection Application">
                    <i class="fas fa-plus"></i>
                </a>
            </li>
            @endcanany

            @canany(['Super Admin', 'tickets create'])
            <li class="nav-item dropdown">
                <a class="nav-link text-danger" data-toggle="dropdown" href="#">
                    <i class="fas fa-plus"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    {{-- <span class="dropdown-item dropdown-header">15 Notifications</span> --}}
                    <span>
                        <a href="{{ route('tickets.create-select') }}"
                            class="nav-link">
                            <i class="fas fa-circle nav-icon text-danger ico-tab"></i>Ordinary Ticket
                        </a>
                        <a href="{{ route('tickets.relocation-search') }}"
                            class="nav-link">
                            <i class="fas fa-circle nav-icon text-danger ico-tab"></i>Relocation Ticket
                        </a>
                    </span>                    
                </div>
            </li>
            @endcanany
    
            <!-- Notifications Dropdown Menu -->
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-bell"></i>
                    <span class="badge badge-warning navbar-badge">15</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" id="notifier-bin">
                    {{-- <span class="dropdown-item dropdown-header">15 Notifications</span> --}}
                    <span>
                        
                    </span>                    
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                    <i class="fas fa-expand-arrows-alt"></i>
                </a>
            </li>
        </ul>

        <ul class="navbar-nav">
            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                    {{-- <img src="https://boheco1.com/wp-content/uploads/2018/06/boheco-1-1024x1012.png" class="user-image img-circle elevation-2" alt="User Image"> --}}
                    <img src="{{ URL::asset('imgs/company_logo.png'); }}"
                         class="user-image img-circle" alt="User Image"> 
                    <span class="d-none d-md-inline">{{ Auth::check() ? Auth::user()->name : '' }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <!-- User image -->
                    <li class="user-header">
                        {{-- <img src="https://boheco1.com/wp-content/uploads/2018/06/boheco-1-1024x1012.png" class="user-image img-circle" alt="User Image"> --}}
                        <img src="{{ URL::asset('imgs/company_logo.png'); }}"
                             class="img-circle"
                             alt="User Image"> 
                        <br>
                        <h4 style="margin-top: 10px;"> {{ Auth::check() ? Auth::user()->name : '' }} </h4>
                    </li>
                    <table class="table table-borderless table-hover table-sm">
                        <tr>
                            <td>
                                <a href="{{ route('users.profile', [Auth::id()]) }}" class="btn btn-link {{ $userCache->ColorProfile != null ? 'text-light' : 'text-dark' }}"><i class="fas fa-user-circle ico-tab"></i>My Account</a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                {{-- <div class="custom-control custom-switch">
                                    <label class="custom-control-label" for="color-modes" id="color-modes">Dark Mode</label>
                                    <input type="checkbox" class="custom-control-input" id="color-modes">
                                </div> --}} 
                                <div class="custom-control custom-switch" style="margin-left: 10px; margin-top: 6px; margin-bottom: 6px;">
                                    <input type="checkbox" {{ $userCache->ColorProfile != null ? 'checked' : '' }} class="custom-control-input" id="color-switch">
                                    <label style="font-weight: normal;" class="custom-control-label" for="color-switch" id="color-switchLabel">Dark Mode</label>
                                </div>
                                
                            </td>
                        </tr>
                        <tr>
                            <td style="border-top: 1px solid #e6e6e6;">
                                <a href="#" class="btn btn-link {{ $userCache->ColorProfile != null ? 'text-light' : 'text-dark' }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt ico-tab"></i>Sign out
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </td>
                        </tr>
                    </table>
                </ul>
            </li>
        </ul>
    </nav>

    <!-- Left side column. contains the logo and sidebar -->
@include('layouts.sidebar')

<!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <section class="content">
            @yield('content')
        </section>
    </div>

    <!-- Main Footer -->
    {{-- <footer class="main-footer">
        <div class="float-right d-none d-sm-block">
            <b>Version</b> 3.7.1
        </div>
        {{ env('APP_COMPANY') }} | CRM &copy; @php echo date('Y') @endphp - <strong class="badge badge-primary">All Rights Reserved</strong>
    </footer> --}}
</div>

{{-- MODAL FOR SEARCHING OF CONSUMERS --}}
<div class="modal fade" id="modal-search-main" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Search Consumer</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                {{-- SEARCH --}}
                <div class="row">                    
                    <div class="form-group col-lg-4">
                        <input class="form-control" id="old-account-no-main" autocomplete="off" data-inputmask="'alias': 'phonebe'" maxlength="12" value="{{ env('APP_AREA_CODE') }}" style="font-size: 1.5em; color: #b91400; font-weight: bold;">
                    </div>                   
                    <div class="form-group col-lg-7">
                        <input type="text" id="search-global" placeholder="Account Number, Account Name, or Meter Number" class="form-control" autofocus="true">
                    </div>
                    <div class="form-group col-lg-1">
                        <button id="search-consumer-global" class="btn btn-primary"><i class="fas fa-search-dollar"></i></button>
                    </div>
                </div>

                {{-- RESULTS --}}
                <p class="text-muted"><i id="count">Results</i></p>
                <table class="table table-sm table-hover" id="res-table-global">
                    <thead>
                        <th>Account Number</th>
                        <th>Account Name</th>
                        <th>Address</th>
                        <th>Status</th>
                        <th></th>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"
        integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg=="
        crossorigin="anonymous"></script> --}}
<script src="{{ URL::asset('js/jquery.min.css'); }}"></script>

{{-- <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> --}}
<script src="{{ URL::asset('js/jqueryui.css'); }}"></script>

{{-- <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"
        integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" 
        crossorigin="anonymous"></script> --}}
<script src="{{ URL::asset('js/popper.min.js'); }}"></script>

{{-- <script src="https://adminlte.io/themes/v3/plugins/bootstrap/js/bootstrap.bundle.min.js"></script> --}}
<script src="{{ URL::asset('js/bootstrap.bundle.min.js'); }}"></script>
        
{{-- <script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script> --}}
<script src="{{ URL::asset('js/bscustomfileinput.min.js'); }}"></script>


<!-- AdminLTE App -->
{{-- <script src="https://adminlte.io/themes/v3/dist/js/adminlte.min.js"></script> --}}
<script src="{{ URL::asset('js/adminlte.min.js'); }}"></script>

{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.27.0/moment.min.js"
        integrity="sha512-rmZcZsyhe0/MAjquhTgiUcb4d9knaFc7b5xAfju483gbEXTkeJRUMIPk6s3ySZMYUHEcjKbjLjyddGWMrNEvZg=="
        crossorigin="anonymous"></script> --}}
<script src="{{ URL::asset('js/moment.js'); }}"></script>

{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"
        integrity="sha512-GDey37RZAxFkpFeJorEUwNoIbkTwsyC736KNSYucu1WJWFK9qTdzYub8ATxktr6Dwke7nbFaioypzbDOQykoRg=="
        crossorigin="anonymous"></script> --}}
<script src="{{ URL::asset('js/datetimepicker.min.js'); }}"></script>

{{-- <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script> --}}
<script src="{{ URL::asset('js/bootstrap4toggle.min.js'); }}"></script>

{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/3.3.4/js/bootstrap-switch.min.js" integrity="sha512-J+763o/bd3r9iW+gFEqTaeyi+uAphmzkE/zU8FxY6iAvD3nQKXa+ZAWkBI9QS9QkYEKddQoiy0I5GDxKf/ORBA==" crossorigin="anonymous"></script> --}}
<script src="{{ URL::asset('js/bootstrapswitch.min.js'); }}"></script>

{{-- <script src="https://cdn.lordicon.com/libs/mssddfmo/lord-icon-2.1.0.js"></script> --}}
<script src="{{ URL::asset('js/lordicon.js'); }}"></script>

{{-- <script src="https://adminlte.io/themes/v3/plugins/chart.js/Chart.min.js"></script> --}}
<script src="{{ URL::asset('js/chart.min.js'); }}"></script>

{{-- <script src="https://www.jqueryscript.net/demo/jQuery-Plugin-To-Connect-HTML-Elements-with-Lines-HTML-SVG-Connect/jquery.html-svg-connect.js"></script> --}}
<script src="{{ URL::asset('js/svgconnect.js'); }}"></script>

{{-- <script src="https://adminlte.io/themes/v3/plugins/select2/js/select2.full.min.js"></script> --}}
<script src="{{ URL::asset('js/select2min.js'); }}"></script>

{{-- CALENDAR --}}
{{-- <script src="https://adminlte.io/themes/v3/plugins/jquery-ui/jquery-ui.min.js"></script> --}}
{{-- <script src="https://adminlte.io/themes/v3/plugins/fullcalendar/main.js"></script> --}}
<script src="{{ URL::asset('js/jqueryuicalendar.min.js'); }}"></script>
<script src="{{ URL::asset('js/calendarfulljs.min.js'); }}"></script>

{{-- SWEETALERT2 --}}
<script src="{{ URL::asset('js/sweetalert2.all.min.js'); }}"></script>

{{-- INPUT MASK --}}
<script src="{{ URL::asset('js/inputmask.min.js'); }}"></script>

<script src="{{ URL::asset('js/knob.min.js'); }}"></script>

<script src="{{ URL::asset('js/datatables.min.js'); }}"></script>

<script src="{{ URL::asset('js/datatables.buttons4.min.js'); }}"></script>

{{-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script> --}}
{{--  --}}

<script>
    
    $(function () {
        bsCustomFileInput.init();
    });
    
    $("input[data-bootstrap-switch]").each(function(){
        $(this).bootstrapSwitch('state', $(this).prop('checked'));
    });

    $('.knob').knob()

        /** add active class and stay opened when selected */
    var url = window.location;

    // for sidebar menu entirely but not cover treeview
    $('ul.nav-sidebar a').filter(function() {
        return this.href == url;
    }).addClass('active');

    // for treeview
    $('ul.nav-treeview a').filter(function() {
        return this.href == url;
    }).parentsUntil(".nav-sidebar > .nav-treeview").addClass('menu-open').prev('a').addClass('active');

    // APPLICATION JS
    $(document).ready(function() {

        $('.select2').select2({
            theme: 'bootstrap4'
        })

        /**
         * TOWN CHANGE
         */
        fetchBarangayFromTown($('#Town').val(), $('#Def_Brgy').text());

        $('#Town').on('change', function() {
            fetchBarangayFromTown(this.value, $('#Def_Brgy').text());
        });

        /**
         * SERVICE CONNECTION SCRIPTS
         */
        $('#organizationNo').hide();

        /**
         * METERING DASH AND MENU COUNTER
         * */
        // $.ajax({
        //     url :"{{ route('home.get-unassigned-meters') }}", 
        //     type: "GET",
        //     dataType : "json",
        //     success : function(response) {
        //         // $.each(response, function(index, element) {
        //         //     console.log(response[index]['id']);
        //         // });
        //         console.log(response.length);
        //         $('#metering-unassigned').text(response.length);
        //         $('#assign-badge-count').text(response.length);
        //     },
        //     error : function(error) {
        //         // alert(error);
                
        //     }
        // });

        /**
         * NOTIFICATIONS
         **/
        // window.setInterval(getNotifications(), 2000)
        
        /**
         * SEARCH ACCOUNTS
         **/ 
         $('#search-global').keyup(function() {
            var letterCount = this.value.length;

            if (letterCount > 5) {
                performSearch(this.value)
            }
        })

        $('#search-consumer-global').on('click', function() {
            performSearch($('#search').val())
        })

        /**
         * COLOR MODES CONTROLLER 
         **/
         $('#color-switch').on('change', function(e) {
            var col = ''
            if (e.target.checked) {
               col = 'dark-mode'
            } else {
               col = null
            }

            $.ajax({
                url : "{{ route('users.switch-color-modes') }}",
                type : "GET",
                data : {
                    id : "{{ Auth::id() }}",
                    Color : col,
                },
                success : function(res) {
                    location.reload()
                },
                error : function(err) {
                    Swal.fire({
                        icon : 'error',
                        text : 'Error changing color profile'
                    })
                }
            })
         })
        
    });

    function getNotifications() {
        $.ajax({
            url : "{{ route('notifiers.get-notifications') }}",
            type : 'GET',
            success : function(res) {
                $('#notifier-bin span').remove()
                $('#notifier-bin').append('<span>' + res + '</span>')
            },
            error : function(err) {
                console.log('Error getting notifs')
            }
        })
    }

    /**
     * FUNCTIONS
     */
    function fetchBarangayFromTown(townId, prevValue) {
        var selectedBarangay = $('#selectedBarangay').val();
        $.ajax({
            url : "{{ url('/barangays/get-barangays-json/') }}/" + townId,
            type: "GET",
            // dataType : "json",
            success : function(data) {
                $('#Barangay option').remove();
                $('#Barangay').append("<option value=''>-- Select --</option>");
                $.each(data, function(index, element) {
                    $('#Barangay').append("<option value='" + element + "' " + (element==prevValue || index == selectedBarangay  ? "selected='selected'" : " ") + ">" + index + "</option>");
                });
            },
            error : function(error) {
                // alert(error);
                console.log(error);
            }

        });
    }

    function performSearch(regex) {
        $.ajax({
            url : '{{ route("serviceAccounts.search-global") }}',
            type : 'GET',
            data : {
                query : regex,
            },
            success : function(res) {
                try {
                    if (jQuery.isEmptyObject(res)) {
                        $('#res-table-global tbody tr').remove()
                    } else {
                        $('#res-table-global tbody tr').remove()
                        $('#res-table-global tbody').append(res)
                    }   
                } catch (err) {
                    $('#res-table-global tbody tr').remove()
                }                                     
            },
            error : function(error) {
                $('#res-table-global tbody tr').remove()
                // alert('Error fetching data')
                console.log(error)
            }
        })
    }

    function goToAccount(id) {
        window.location.href = "{{ url('/serviceAccounts') }}" + "/" + id
    }

    var Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });
</script>

@yield('third_party_scripts')

@stack('page_scripts')
</body>
</html>
