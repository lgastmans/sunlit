@extends('layouts.app')

@section('page-title', 'Users')

@section('content')

<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-body">

                    <x-forms.errors class="mb-4" :errors="$errors" />
                    <form action="@if ($user->id) {{ route('users.update', $user->id) }} @else {{ route('users.store') }} @endif" method="POST" class="needs-validation" novalidate>
                        @csrf()
                        @if ($user->id)
                            @method('PUT')
                        @endif
                        <div class="row mb-3">
                            <div class="col-xl-5">
                                <x-forms.input label="Name" name="name" value="{{ old('name', $user->name) }}" required="true"/>
                            </div>
                            <div class="col-xl-3">
                                <label class="form-label" for="role-select">Role</label>
                                <select class="role-select form-control" name="role-select" required>
                                    @if ($user->id)
                                        <option value="{{$user->roles->first()->id}}" selected="selected">{{ucfirst($user->roles->first()->name)}}</option>
                                    @endif
                                </select>
                                <div class="invalid-feedback">
                                    {{ __('error.form_invalid_field', ['field' => 'role']) }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-xl-8">
                                <label class="form-label" for="email">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text" id="inputGroupPrepend">@</span>
                                    <input class="form-control" type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required="true" />
                                    <div class="invalid-feedback">
                                        {{ __('error.form_invalid_field', ['field' => 'email']) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                       
                        <button class="btn btn-primary" type="submit">@if ($user->id) {{ __('app.edit_title', ['field' => 'user']) }} @else {{ __('app.add_title', ['field' => 'user']) }} @endif</button>

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