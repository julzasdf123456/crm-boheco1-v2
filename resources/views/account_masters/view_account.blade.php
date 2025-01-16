@extends('layouts.app')
<meta name="accountNumber" content="{{ $accountNumber }}">
@section('content')
    <div class="row">
        <div class="col-lg-12">

            <div id="app">
                <view-account></view-account>
            </div>
            @vite('resources/js/app.js')
        </div>
    </div>
@endsection

@push('page_scripts')
    <script>
        $(document).ready(function() {
            $('body').addClass('sidebar-collapse')
            $('#page-title').html("<span class='text-muted'>Account Management</span>")
        })
    </script>
@endpush
