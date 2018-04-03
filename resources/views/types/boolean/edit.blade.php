@include('loaf/admin::partials.forms.fields.checkbox', [
    'id' => $type->getId(),
    'name' => $type->getFormName(),
    'description' => $type->getDescription(),
    'label' => $type->getLabel(),
    'checked' => Settings::get( $type->getField()->getPath() ),
    'horizontal' => [3,9]
])