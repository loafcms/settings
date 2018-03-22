{{-- TODO: Remove the json encode --}}
<input type="text" class="form-control" id="input-{{ $field->getKey() }}" placeholder="{{ $field->getLabel() }}" value="{{ json_encode( $field->get() ) }}">