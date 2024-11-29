@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div id="app">
            <reading-monitor></reading-monitor>
        </div>
        @vite('resources/js/app.js')
    </div>
</div>
@endsection

@push('page_scripts')
    <script>
        $(document).ready(function() {
            $('body').addClass('sidebar-collapse')
            $('#page-title').html("<span class='text-muted'>Meter Reading Monitoring</span>")
        })
    </script>
@endpush