@props(['errors'])

@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <div >
            <i class="dripicons-wrong me-2"></i><strong>{{ __('Whoops! Something went wrong.') }}</strong>
        </div>

        <ul class="mt-3 list-disc list-inside text-sm text-red-600">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif