@extends('layouts.app')

@section('content')
    <div class="tab-pane-app active" id="tab-dashboard">
        @include('partials.dashboard')
    </div>

    <div class="tab-pane-app" id="tab-pronostico">
        @include('partials.pronostico')
    </div>

    <div class="tab-pane-app" id="tab-mapa">
        @include('partials.mapa')
    </div>

    <div class="tab-pane-app" id="tab-graficas">
        @include('partials.graficas')
    </div>

    <div class="tab-pane-app" id="tab-simulador">
        @include('partials.simulador')
    </div>
@endsection
