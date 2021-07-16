@extends('layouts.app')

@section('page-title', 'Users')

@section('content')

<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <p>@if ($user->id) {{ __('app.edit_title', ['field' => 'user']) }}: <span class="text-primary">{{ $user->name }}</span> @else {{ __('app.add_title', ['field' => 'user']) }} @endif </p>
                </div>
                    <x-forms.errors class="mb-4" :errors="$errors" />
                    <form action="@if ($user->id) {{ route('users.update', $user->id) }} @else {{ route('users.store') }} @endif" method="POST" class="needs-validation" novalidate>
                        @csrf()
                        @if ($user->id)
                            @method('PUT')
                        @endif
                        @if ($user->id)
                            <div class="mb-3">
                                <input type="hidden" name="id" value="{{ old('id', $user->id) }}" />
                            </div>
                        @endif
                        <div class="mb-3">
                            <x-forms.input label="Name" name="name" value="{{ old('name', $user->name) }}" message="Please provide a name" required="true"/>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="email">Email</label>
                            <div class="input-group">
                                <span class="input-group-text" id="inputGroupPrepend">@</span>
                                <input class="form-control" type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required="true" />
                                <div class="invalid-feedback">
                                    Please provide a valid email address
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="role-select">Role</label>
                            <select class="role-select form-control" name="role-select" required>
                                @if ($user->id)
                                    <option value="{{$user->roles->first()->id}}" selected="selected">{{ucfirst($user->roles->first()->name)}}</option>
                                @endif
                            </select>
                            <div class="invalid-feedback">
                                Please provide a valid role.
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