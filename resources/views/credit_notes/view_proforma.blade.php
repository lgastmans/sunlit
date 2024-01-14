@extends('layouts.app')

@section('title')
    @parent() | C.N. #{{ $order->credit_note_number }}
@endsection

@section('page-title')
    Credit Note #{{ $order->credit_note_number}}
@endsection

@section('content')
    @include('credit_notes.proforma')
@endsection