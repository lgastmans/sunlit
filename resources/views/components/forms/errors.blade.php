@props(['errors'])

@if ($errors->any())
{{-- @if (count($errors)) --}}
    <div class="alert alert-danger" role="alert">
        <ul class="mt-3 list-disc list-inside text-sm text-red-600">
            @foreach ($errors->all() as $error)
            {{-- @foreach ($errors as $error) --}}
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
