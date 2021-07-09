@extends('layouts.app')

@section('page-title', 'Categories')

@section('content')

<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <h4>Add a category</h4>
                </div>
                    <form action="{{ route('categories.store') }}" method="POST" class="needs-validation" novalidate>
                        @csrf()
                        <div class="mb-3">
                            <x-forms.input label="name" name="name" value="{{ old('name', $category->name) }}" message="Please provide a name" required="true"/>
                        </div>
                        
                       
                        <button class="btn btn-primary" type="submit">Create category</button>

                    </form>
                </div>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col -->
</div>


@endsection
