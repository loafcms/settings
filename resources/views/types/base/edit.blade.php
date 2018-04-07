{{-- TODO: Remove the json encode --}}
@include('loaf/admin::partials.forms.fields.text', [
    'id' => $type->getId(),
    'label' => $type->getLabel(),
    'name' => $type->getFormName(),
    'value' => $value,
    'placeholder' => $type->getLabel(),
    'horizontal' => [3,9]
])