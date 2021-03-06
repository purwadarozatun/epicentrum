@extends('epicentrum::edit', ['tab' => 'password'])

@section('content-user-edit')

    <h4>Manual</h4>
    <p>User akan mendapat email yang berisi link untuk melakukan reset password. User harus mengisi sendiri password barunya.</p>
    <form action="{{ route('epicentrum::password.reset', [$user['id']]) }}" method="POST">
    {{ csrf_field() }}
    <button type="submit" class="ui button" href="">@lang('epicentrum::action.send_reset_password_link')</button>
    </form>

    <div class="ui divider"></div>

    <h4>Otomatis</h4>
    <p>Generate password baru, dan kirim password tersebut via email. User bisa langsung login menggunakan password baru tersebut.</p>
    {!! SemanticForm::open()->post()->action(route('epicentrum::password.generate', $user['id'])) !!}
    {{ csrf_field() }}
    <div class="field">
        <div class="ui checkbox">
            <input type="checkbox" name="must_change_password" {{ request()->old('must_change_password')?'checked':'' }}>
            <label>@lang('epicentrum::users.change_password_on_first_login')</label>
        </div>
    </div>
    <button type="submit" class="ui button" href="">@lang('epicentrum::action.send_new_password')</button>
    {!! SemanticForm::close() !!}
@endsection
