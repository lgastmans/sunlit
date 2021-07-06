<div>
    <label class="form-label" for="{{ $name }}">{{ $label }}</label>
    <div class="input-group">
        @if ($position=="before")
            <span class="input-group-text" id="inputGroupPrepend">{{ $symbol }}</span>
        @endif

        <input type="text" class="form-control" name="{{ $name }}" id="{{ $name }}" placeholder="" value="{{ $value }}"
            aria-describedby="inputGroupPrepend" @if ($required) required @endif>

        @if ($position=="after")
            <span class="input-group-text" id="inputGroupPrepend">{{ $symbol }}</span>
        @endif
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    
    </div>
</div>