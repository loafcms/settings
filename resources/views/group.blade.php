<b-tab title="{{ $group->getLabel() }}">
    <h4>{{ $group->getLabel() }}</h4>

    @if( $description = $group->getDescription() )
        <p>{{ $description }}</p>
    @endif

    @each('loaf/settings::field', $group->fields, 'field', 'loaf/settings::empty-group')

</b-tab>