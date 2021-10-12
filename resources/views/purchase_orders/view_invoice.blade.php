@extends('layouts.app')

@section('title')
    @parent() | PO #{{ $order->order_number }}
@endsection

@section('page-title')
    Invoice Purchase Order #{{ $order->order_number}}
@endsection

@section('content')

    @include('purchase_orders.invoice')

@endsection