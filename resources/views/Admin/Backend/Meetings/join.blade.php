@extends('layout.master')
@section('title', 'Dashboard | Meetings')
@section('main')
    <div class="main-content">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>{{ $meeting->title }}</h3>
            <!-- Back Button -->
            <a href="{{ route('dashboard.meetings.index') }}" class="btn btn-primary">
                {{ __('dashboard.back') }}
            </a>
        </div>

        {{-- <iframe
            src="https://meet.jit.si/{{ $meeting->room_name }}#userInfo.displayName={{ urlencode(auth()->user()->name) }}"
            style="width:100%; height:600px; border:0;" allow="camera; microphone; fullscreen; display-capture">
        </iframe> --}}

        <iframe src="https://meet.jit.si/{{ $meeting->room_name }}" style="width:100%; height:600px; border:0;"
            allow="camera; microphone; fullscreen; display-capture">
        </iframe>


    </div>
@endsection
