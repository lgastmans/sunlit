@extends('layouts.app')

@section('title')
    @parent() | POI #{{ $order->invoice_number }}
@endsection

@section('page-title')
    Purchase Order Invoice #{{ $order->invoice_number}}
@endsection

@section('content')
    {{-- @include('purchase_order_invoices.proforma') --}}
@endsection