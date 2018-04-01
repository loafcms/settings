@include('loaf/admin::partials.forms.fields.checkbox', [
    'id' => $type->getId(),
    'name' => $type->getFormName(),
    'description' => $type->getDescription(),
    'label' => $type->getLabel(),
    'checked' => $type->getField()->get(),
    'horizontal' => [3,9]
])