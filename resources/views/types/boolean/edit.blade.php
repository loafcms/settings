<div class="form-check">
    <input type="checkbox" class="form-check-input" id="input-{{ $field->getKey() }}" {{ $field->get() ? 'checked' : '' }}>
    <label class="form-check-label" for="input-{{ $field->getKey() }}">
        {{ $field->getDescription()  }}
    </label>
</div>