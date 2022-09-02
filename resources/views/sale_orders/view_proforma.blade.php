@extends('layouts.app')

@section('title')
    @parent() | PI #{{ $order->order_number }}
@endsection

@section('page-title')
    Proforma Invoice #{{ $order->order_number}}
@endsection

@section('content')
    @include('sale_orders.proforma')
@endsection