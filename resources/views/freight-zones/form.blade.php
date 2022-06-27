@extends('layouts.app')

@section('title')

    @parent() | Freight Zones

@endsection

@section('page-title', 'Zones')

@section('content')

<div class="row">
    <div class="col-9">
        <div class="card">
            <div class="card-body">

                <x-forms.errors class="mb-4" :errors="$errors" />
                
                <form action="@if ($zone->id) {{ route('freight-zones.update', $zone->id) }} @else {{ route('freight-zones.store') }} @endif" method="POST" class="needs-validation" novalidate>
                    @csrf()
                    @if ($zone->id)
                        @method('PUT')
                            <input type="hidden" name="id" value="{{ old('id', $zone->id) }}" />
                    @endif

                    <div class="mb-3 row">
                        <div class="col-xl-5">
                            <x-forms.input label="name" name="name" value="{{ old('name', $zone->name) }}" required="true"/>
                        </div>
                        <div class="col-xl-3">
                            <x-forms.input label="rate per kg" name="rate_per_kg" value="{{ old('rate_per_kg', $zone->rate_per_kg) }}" required="true"/>
                        </div>
                    </div>
                
                   
                    <button class="btn btn-primary" type="submit">@if ($zone->id) {{ __('app.edit_title', ['field' => 'zone']) }} @else {{ __('app.add_title', ['field' => 'zone']) }} @endif</button>

                </form>
                
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col -->
</div>
@endsection
