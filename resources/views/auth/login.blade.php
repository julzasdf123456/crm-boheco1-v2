<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ config('app.name') }}</title>

    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <!-- Font Awesome -->
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css"
          integrity="sha512-0S+nbAYis87iX26mmj/+fWt1MmaKCv80H+Mbo+Ne7ES4I6rxswpfnC6PxmLiw33Ywj2ghbtTw0FkLbMWqh4F7Q=="
          crossorigin="anonymous"/> --}}
    <link rel="stylesheet" href="{{ URL::asset('css/all.css'); }} ">

    <!-- AdminLTE -->
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.0.5/css/adminlte.min.css"
          integrity="sha512-rVZC4rf0Piwtw/LsgwXxKXzWq3L0P6atiQKBNuXYRbg2FoRbSTIY0k2DxuJcs7dk4e/ShtMzglHKBOJxW8EQyQ=="
          crossorigin="anonymous"/> --}}
    <link rel="stylesheet" href="{{ URL::asset('css/adminlte.min.css'); }} ">

    <!-- iCheck -->
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/icheck-bootstrap/3.0.1/icheck-bootstrap.min.css"
          integrity="sha512-8vq2g5nHE062j3xor4XxPeZiPjmRDh6wlufQlfC6pdQ/9urJkU07NM0tEREeymP++NczacJ/Q59ul+/K2eYvcg=="
          crossorigin="anonymous"/> --}}

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <link rel="stylesheet" href="{{ URL::asset('css/style.css'); }}">

</head>
<style>
    body {
        background-image: 
            linear-gradient(to right, rgba(0, 0, 0, 0.8), rgba(255, 255, 255, 0)),
            url("{{ URL::asset('imgs/login-img.JPG') }}");
        background-size: cover;      /* Ensures the image covers the entire background */
        background-position: center; /* Centers the image */
        background-repeat: no-repeat;
        padding: 0px 30px 30px 100px;;
        display: flex;
        height: 100dvh;
        align-items: center;
    }

    .login-card {
        width: 24%;
    }

    @media screen and (max-width: 1710px) {
        /* Select <p> elements and set display to none */
        body {
            padding-left: 5%;
        }

        .login-card {
            width: 30%;
        }
    }

    @media screen and (max-width: 1440px) {
        /* Select <p> elements and set display to none */
        .login-card {
            width: 40%;
        }
    }

    @media screen and (max-width: 1020px) {
        /* Select <p> elements and set display to none */
        .login-card {
            width: 50%;
        }
    }

    @media screen and (max-width: 720px) {
        /* Select <p> elements and set display to none */
        .login-card {
            width: 100% !important;
        }
    }
</style>
<body>
<div class="login-card">
    <div class="login-logo">
        <a class=" text-white" href="{{ url('/home') }}"><b>{{ config('app.name') }}</b></a>
    </div>
    
    <!-- /.login-logo -->
    
    <!-- /.login-box-body -->
    <div class="card shadow-soft">
        <div class="card-body">
            <p class="login-box-msg">Sign in to start your session</p>
    
            <form method="post" action="{{ url('/login') }}">
                @csrf
    
                <div class="input-group mb-3">
                    <input type="text"
                            name="username"
                            value="{{ old('username') }}"
                            placeholder="Username"
                            class="form-control @error('username') is-invalid @enderror" autofocus>
                    @error('username')
                    <span class="error invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
    
                <div class="input-group mb-3">
                    <input type="password"
                            name="password"
                            placeholder="Password"
                            class="form-control @error('password') is-invalid @enderror">
                    @error('password')
                    <span class="error invalid-feedback">{{ $message }}</span>
                    @enderror
    
                </div>
    
                <button type="submit" class="btn btn-primary btn-block">Sign In</button>
            </form>
    
            <div class="divider mt-3"></div>
    
            <p class="mb-0">
                <a class="btn btn-link-muted" href="{{ route('register') }}" class="text-center">Register a new membership</a>
            </p>
    
            {{-- <p class="mb-1">
                <a href="{{ route('password.request') }}">I forgot my password</a>
            </p> --}}
        </div>
        <!-- /.login-card-body -->
    </div>
</div>
<!-- /.login-box -->

<!-- AdminLTE App -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.0.5/js/adminlte.min.js"
        integrity="sha512-++c7zGcm18AhH83pOIETVReg0dr1Yn8XTRw+0bWSIWAVCAwz1s2PwnSj4z/OOyKlwSXc4RLg3nnjR22q0dhEyA=="
        crossorigin="anonymous"></script>

</body>
</html>
