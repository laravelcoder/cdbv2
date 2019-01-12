@extends('layouts.app')

@section('page-styles')
    <link rel="stylesheet" href="{{ asset('editor/public/lib/bootstrap/dist/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('editor/public/lib/jquery-ui/themes/smoothness/jquery-ui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('editor/public/assets/css/icomoon/style.css') }}">
    <link rel="stylesheet" href="{{ asset('editor/public/assets/css/styles.css') }}">
@stop

@section('content')



    <div class="card card-default">
        <div class="card-block">

@include('editor.templates.main')

        </div>
    </div>

{{-- @include('editor.templates.default') --}}


@stop

@section('javascript')
    @parent

        <script src="{{ asset('editor/public/lib/tether/dist/js/tether.min.js') }}"></script>

        <script src="{{ asset('editor/public/lib/underscore/underscore-min.js') }}"></script>

        <script src="{{ asset('editor/public/assets/js/webvideoedit.js') }}"></script>
        {{-- <script src="{{ asset('editor/public/assets/js/app.min.js') }}"></script> --}}

    <script>
        var webVideoEditor = new WebVideoEditor({
            baseUrl: '/',
            requestHandler: '/'
        });
    </script>
@stop
