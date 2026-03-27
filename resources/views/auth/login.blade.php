<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Atsiri Tour</title>

    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link href="{{ asset('css/login.css') }}" rel="stylesheet">
</head>

<body>

    <div class="card">

        <!-- LEFT PANEL -->
        <div class="left">
            <div class="logo-area">
                <img src="{{ asset('images/Login-Logo.png') }}" class="login-logo"
                    style="width:300px; height:auto; object-fit:cover; filter: brightness(0) invert(1);">
            </div>
            <div class="left-footer">
                <div class="admin-badge">Manajemen Tour</div>
                <div class="tagline">
                    Internal Management System<br>
                    Rumah Atsiri Indonesia.<br>
                    Limited access for official staff only.
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL -->
        <div class="right">
            <div class="welcome">Welcome Back!</div>
            <div class="sub-welcome">Login to your account</div>

            @if ($errors->any())
                <div class="alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="user" method="POST" action="{{ route('login') }}">
                @csrf

                <div class="field">
                    <label for="login">Username or Email</label>
                    <input type="text" id="login" name="login" placeholder="Enter Username or Email Address..."
                        value="{{ old('login') }}" required>
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Password" required>
                </div>

                <div class="row-check">
                    <div class="check-wrap">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Remember Me</label>
                    </div>
                </div>

                <button type="submit" class="btn-login">Login</button>
                <hr>
            </form>
        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/js/sb-admin-2.min.js"></script>

</body>

</html>