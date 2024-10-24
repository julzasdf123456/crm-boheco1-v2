@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div id="app">
            <search-accounts></search-accounts>
        </div>
        @vite('resources/js/app.js')
    </div>
</div>
@endsection

@push('page_scripts')
    <script>
        $(document).ready(function() {
            // $('body').addClass('sidebar-collapse')
            $('#page-title').html("<span class='text-muted'>Browse </span> <strong>All Consumer Accounts</strong>")
        })
    </script>
@endpush