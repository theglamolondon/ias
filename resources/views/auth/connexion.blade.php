<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>Connexion | IAS Manager</title>
    <!-- Favicon-->
    <link rel="icon" href="{{ config("app.url") }}favicon.ico" type="image/x-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

    <!-- Bootstrap Core Css -->
    <link href="{{ asset("plugins/bootstrap/css/bootstrap.css")}}" rel="stylesheet">

    <!-- Waves Effect Css -->
    <link href="{{ asset("plugins/node-waves/waves.css") }}" rel="stylesheet" />

    <!-- Animation Css -->
    <link href="{{ asset("plugins/animate-css/animate.css") }}" rel="stylesheet" />

    <!-- Custom Css -->
    <link href="{{ asset("css/style.css") }}" rel="stylesheet">
    <style type="text/css">
        .login-page{
            background: url("{{ asset('images/ias/background.png') }}") no-repeat center right #8a90ff !important;
            /*url("http://localhost/ias/public/images/ias/background.png") no-repeat 127% -90px #8a90ff !important*/
        }
    </style>
</head>

<body class="login-page">
<div class="login-box">
    <div class="logo">
        <a href="javascript:void(0);">{{ config("app.name") }}</a>
        <small>Licence accordée à IAS</small>
    </div>
    <div class="card">
        <div class="body">
            <form id="sign_in" method="post" action="{{ route('login') }}">
                {{ csrf_field() }}
                <div class="msg">Veuillez vous connecter pour démarrer votre session</div>

                @foreach($errors->all() as $e)
                    <div class="alert bg-red alert-dismissible ">
                        {{ $e }}
                    </div>
                @endforeach

                <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">person</i>
                        </span>
                    <div class="form-line">
                        <input type="text" class="form-control" name="login" placeholder="Nom d'utilisateur" required value="{{ old('login',\Illuminate\Support\Facades\Cookie::get('login')) }}">
                    </div>
                </div>
                <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">lock</i>
                        </span>
                    <div class="form-line">
                        <input type="password" class="form-control" name="password" placeholder="Password"  autofocus required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-8 p-t-5">
                        <input type="checkbox" name="remember" id="remember" class="filled-in chk-col-pink" checked>
                        <label for="remember">Se souvenir de moi</label>
                    </div>
                    <div class="col-xs-4">
                        <button class="btn btn-block bg-pink waves-effect" type="submit">Connexion</button>
                    </div>
                </div>
                <div class="row m-t-15 m-b--20">
                    <div class="col-xs-6">
                        <!--<a href="sign-up.html">Register Now!</a> -->
                    </div>
                    <div class="col-xs-6 align-right">
                        <a href="forgot-password.html">Mot de passe oublié?</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Jquery Core Js -->
<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>

<!-- Bootstrap Core Js -->
<script src="{{ asset('plugins/bootstrap/js/bootstrap.js') }}"></script>

<!-- Waves Effect Plugin Js -->
<script src="{{ asset('plugins/node-waves/waves.js') }}"></script>

<!-- Validation Plugin Js -->
<script src="{{ asset('plugins/jquery-validation/jquery.validate.js') }}"></script>

<!-- Custom Js -->
<script src="{{ asset('js/admin.js') }}"></script>
<script src="{{ asset('js/pages/examples/sign-in.js') }}"></script>
</body>

</html>