@extends('loaf/admin::layouts.admin.card.single')

@section('card-1-before', Form::open())
@section('card-1-title', ucfirst(__('loaf/settings::settings.edit settings', ['section' => $section->label])) )

@section('card-1-content')
    <b-tabs pills card vertical>
        @each('loaf/settings::group', $section->groups, 'group', 'loaf/settings::empty-section')
    </b-tabs>
@endsection

@section('card-1-footer')
    <div class="text-right">
        {{ Form::submit(ucfirst(__('loaf/admin::general.save')), ['class'=>'btn btn-primary']) }}
    </div>
@endsection

@section('card-1-after', Form::close())