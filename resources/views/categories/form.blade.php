@extends('layouts.app')

@section('page-title', 'Categories')

@section('content')

<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <p>@if ($category->id) {{ __('app.edit_title', ['field' => 'category']) }}: <span class="text-primary">{{ $category->name }}</span> @else {{ __('app.add_title', ['field' => 'category']) }} @endif </p>
                </div>
                <x-forms.errors class="mb-4" :errors="$errors" />
                    <form action="@if ($category->id) {{ route('categories.update', $category->id) }} @else {{ route('categories.store') }} @endif" method="POST" class="needs-validation" novalidate>
                        @csrf()
                        @if ($category->id)
                            @method('PUT')
                        @endif
                        @if ($category->id)
                            <div class="mb-3">
                                <input type="hidden" name="id" value="{{ old('id', $category->id) }}" />
                            </div>
                        @endif
                        <div class="mb-3">
                            <x-forms.input label="Name" name="name" value="{{ old('name', $category->name) }}" message="Please provide a name" required="true"/>
                        </div>
                        
                       
                        <button class="btn btn-primary" type="submit">@if ($category->id) {{ __('app.edit_title', ['field' => 'category']) }} @else {{ __('app.add_title', ['field' => 'category']) }} @endif</button>

                    </form>
                </div>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col -->
</div>


@endsection
