@extends('layouts.app')

@section('title')
    @parent() | Products
@endsection

@section('page-title', 'Products')

@section('content')

    <livewire:product-table/>

@endsection

@section('page-scripts')
    <script>
        
        $(document).ready(function () {
            "use strict";
        });

    
    </script>    
@endsection
