<div class="form-group row">
    <label for="{{ $field->key }}" class="col-sm-3 col-form-label">{{ $field->getLabel() }}</label>
    <div class="col-sm-9">
        {!! Settings::getSettingType( $field )->getEditView() !!}
    </div>
</div>