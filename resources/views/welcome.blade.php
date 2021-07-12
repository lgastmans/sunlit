@extends('layouts.app')

@section('page-title', 'Dashboard page')

@section('content')
Welcome my dudes!
@endsection

@section('page-scripts')
    <script>
        @if(Session::has('error'))
            $.NotificationApp.send("Error","{{ session('error') }}","top-right","","error")
         @endif
    </script>
@endsection