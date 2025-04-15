<div class="row">
    <div class="col-md-8">
        {{ Form::text('settings[magnati_enabled]', trans('magnati::magnati.settings.enabled'), $errors, ['required' => true]) }}
        {{ Form::text('settings[translatable][magnati_label]', trans('magnati::magnati.settings.label'), $errors, ['required' => true]) }}
        {{ Form::textarea('settings[translatable][magnati_description]', trans('magnati::magnati.settings.description'), $errors, ['required' => true]) }}
        {{ Form::checkbox('settings[magnati_test_mode]', trans('magnati::magnati.settings.test_mode'), trans('magnati::magnati.settings.test_mode'), $errors) }}
    </div>
</div>
