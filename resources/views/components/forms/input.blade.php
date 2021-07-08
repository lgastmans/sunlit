<div>
    <label class="form-label" for="{{ $name }}">{{ $label}}</label>
    <input type="text" class="form-control" name="{{ $name }}" id="{{ $name }}" placeholder="" value="{{ $value }}" @if ($required) required @endif>
    <div class="invalid-feedback">
        {{ $message }}
    </div>
</div>