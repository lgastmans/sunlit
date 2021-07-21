@extends('layouts.app')

@section('page-title', 'Users')

@section('content')

<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <p>{{ __('app.edit_title', ['field' => 'user']) }}: <span class="text-primary">{{ $user->name }}</span></p>
                </div>
                    <x-forms.errors class="mb-4" :errors="$errors" />
                    <form action="{{ route('users.update', $user->id) }}" method="POST" class="needs-validation" novalidate>
                        @csrf()
                        @method('PUT')
                        <div class="mb-3">
                            <input type="hidden" name="id" value="{{ old('id', $user->id) }}" />
                            <input type="hidden" name="role" value="{{ old('role', $user->role) }}" />
                        </div>
                        <div class="mb-3">
                            <x-forms.input label="Name" name="name" value="{{ old('name', $user->name) }}" required="true"/>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="email">Email</label>
                            <div class="input-group">
                                <span class="input-group-text" id="inputGroupPrepend">@</span>
                                <input class="form-control" type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required="true" />
                                <div class="invalid-feedback">
                                    {{ __('error.form_invalid_field', ['field' => 'email']) }}
                                </div>
                            </div>
                        </div>
                       
                       
                        <button class="btn btn-primary" type="submit">{{ __('app.save_profile') }}</button>

                    </form>
                </div>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col -->
</div>
@endsection

@section('page-scripts')
    <script>
        var roleSelect = $(".role-select").select2();
        roleSelect.select2({
            minimumResultsForSearch: Infinity,
            ajax: {
                url: '{{route('ajax.roles')}}',
                dataType: 'json'
            }
        });
    </script>
@endsection