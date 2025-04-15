<div class="row">
    <div class="col-md-8">
        <div class="box-content">
            <h4 class="section-title">Test Mode Credentials</h4>
            
            {{ Form::text('settings[magnati_test_username]', trans('magnati::magnati.settings.test_username'), $errors) }}
            {{ Form::password('settings[magnati_test_password]', trans('magnati::magnati.settings.test_password'), $errors) }}
            {{ Form::text('settings[magnati_test_customer]', trans('magnati::magnati.settings.test_customer'), $errors) }}
            {{ Form::text('settings[magnati_test_store]', trans('magnati::magnati.settings.test_store'), $errors) }}
            {{ Form::text('settings[magnati_test_terminal]', trans('magnati::magnati.settings.test_terminal'), $errors) }}
            {{ Form::text('settings[magnati_test_transaction_hint]', trans('magnati::magnati.settings.test_transaction_hint'), $errors) }}
            
            <h4 class="section-title">Production Credentials</h4>
            
            {{ Form::text('settings[magnati_username]', trans('magnati::magnati.settings.username'), $errors) }}
            {{ Form::password('settings[magnati_password]', trans('magnati::magnati.settings.password'), $errors) }}
            {{ Form::text('settings[magnati_customer]', trans('magnati::magnati.settings.customer'), $errors) }}
            {{ Form::text('settings[magnati_store]', trans('magnati::magnati.settings.store'), $errors) }}
            {{ Form::text('settings[magnati_terminal]', trans('magnati::magnati.settings.terminal'), $errors) }}
            {{ Form::text('settings[magnati_transaction_hint]', trans('magnati::magnati.settings.transaction_hint'), $errors) }}
        </div>
    </div>
</div>
