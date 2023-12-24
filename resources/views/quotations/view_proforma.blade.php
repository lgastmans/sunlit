@extends('layouts.app')

@section('title')
    @parent() | Q #{{ $order->quotation_number }}
@endsection

@section('page-title')
    Quotation #{{ $order->quotation_number}}
@endsection

@section('content')
    @include('quotations.proforma')
@endsection