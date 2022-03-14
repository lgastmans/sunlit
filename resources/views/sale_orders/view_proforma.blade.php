@extends('layouts.app')

@section('title')
    @parent() | SO #{{ $order->order_number }}
@endsection

@section('page-title')
    Sale Order #{{ $order->order_number}}
@endsection

@section('content')
    @include('sale_orders.proforma')
@endsection