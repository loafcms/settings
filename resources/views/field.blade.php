@php( $type = Settings::getSettingType( $field ) )
<div class="setting-field">
    {!! $type->getEditView() !!}
</div>