@extends('layouts.app')

@section('title')
    @parent() | Add a Category
@endsection

@section('page-title', 'Categories' )

@section('content')

<div class="row">
    <div class="col-5">
        <div class="card">
            <div class="card-body">
                <x-forms.errors class="mb-4" :errors="$errors" />
                    <form action="@if ($category->id) {{ route('categories.update', $category->id) }} @else {{ route('categories.store') }} @endif" method="POST" class="needs-validation" novalidate>
                        @csrf()
                        @if ($category->id)
                            @method('PUT')
                        @endif
                        <div class="row mb-3">
                            <div class="col-xl-5">
                                <x-forms.input label="Name" name="name" value="{{ old('name', $category->name) }}" required="true"/>
                            </div>
                        </div>                      
                        <div class="row mb-3">
                            <div class="col-xl-5">
                                <x-forms.input label="HSN" name="hsn_code" value="{{ old('hsn_code', $category->hsn_code) }}" required="true"/>
                            </div>
                        </div>                      
                        <button class="btn btn-primary" type="submit">@if ($category->id) {{ __('app.edit_title', ['field' => 'category']) }} @else {{ __('app.add_title', ['field' => 'category']) }} @endif</button>
                    </form>
                </div>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col -->
</div>


@endsection
