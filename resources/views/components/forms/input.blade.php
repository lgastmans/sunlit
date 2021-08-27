<div>
    <label class="form-label" for="{{ $name }}">{{ ucfirst($label)}}</label>
    <input type="text" class="form-control" name="{{ $name }}" id="{{ $name }}" placeholder="" value="{{ $value }}" @if ($required == "true") required @endif>
    <div class="invalid-feedback">
        {{ __('error.form_invalid_field', ['field' => strtolower($label) ]) }}
    </div>
</div>