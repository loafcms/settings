@include('loaf/admin::partials.forms.fields.checkbox', [
    'id' => $type->getId(),
    'name' => $type->getFormName(),
    'description' => $type->getDescription(),
    'label' => $type->getLabel(),
    'checked' => $value,
    'horizontal' => [3,9]
])