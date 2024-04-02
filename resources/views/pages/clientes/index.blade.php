@extends('layout.master')

@push('plugin-styles')
    <link href="{{ asset('assets/plugins/fullcalendar/main.min.css') }}" rel="stylesheet" />
@endpush

@section('content')

@endsection

@push('custom-scripts')
  <script src="{{ asset('assets/js/fullcalendar.js') }}"></script>
@endpush
