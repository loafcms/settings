@php( $type = Settings::getSettingType( $field ) )
@php( $value = Settings::get( $field->getPath() ) )
<div class="setting-field">
    {!! $type->getEditView( $value ) !!}
</div>