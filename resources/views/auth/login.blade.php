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

    <style>
        /* @font-face {
            font-family: 'SuisseIntlMono';
            src: url('\public\Fonts\SuisseIntlMono-Regular.otf') format('truetype');
            font-weight: normal;
            font-style: normal;
        } */

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Nunito', sans-serif;
            background: #f5f0ed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .card {
            display: grid;
            grid-template-columns: 1fr 1fr;
            max-width: 860px;
            width: 100%;
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 40px rgba(178, 62, 39, 0.12);
        }

        /* LEFT PANEL */
        .left {
            background: #b23e27;
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .left::before {
            content: '';
            position: absolute;
            top: -70px;
            right: -70px;
            width: 230px;
            height: 230px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.06);
        }

        .left::after {
            content: '';
            position: absolute;
            bottom: -50px;
            left: -50px;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
        }

        .logo-area {
            position: relative;
            z-index: 1;
        }

        .logo-area img {
            height: 180px;
            width: auto;
            object-fit: contain;
        }

        .left-footer {
            position: relative;
            z-index: 1;
        }

        .admin-badge {
            display: inline-block;
            border: 1px solid rgba(255, 255, 255, 0.35);
            border-radius: 6px;
            padding: 0.35rem 0.75rem;
            font-size: 0.68rem;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.75);
            margin-bottom: 1rem;
        }

        .tagline {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.6);
            line-height: 1.8;
            letter-spacing: 0.02em;
        }

        /* RIGHT PANEL */
        .right {
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .welcome {
            font-size: 2rem;
            font-weight: bold;
            color: #1a1a1a;
            letter-spacing: 0.06em;
            margin-bottom: 0.3rem;
            text-transform: uppercase;
        }

        .sub-welcome {
            font-size: 1rem;
            color: #aaa;
            margin-bottom: 2rem;
            letter-spacing: 0.08em;
        }

        /* Error alert */
        .alert-danger {
            background: #fdf2f0;
            border: 1px solid #f0c4bb;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            margin-bottom: 1.25rem;
            font-size: 0.72rem;
            color: #b23e27;
            letter-spacing: 0.03em;
            line-height: 1.7;
        }

        .alert-danger ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .field {
            margin-bottom: 1.1rem;
        }

        .field label {
            display: block;
            font-size: 0.65rem;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: #b23e27;
            margin-bottom: 0.45rem;
        }

        .field input[type="text"],
        .field input[type="password"] {
            width: 100%;
            padding: 0.72rem 1rem;
            border: 1.5px solid #e8ddd9;
            border-radius: 10px;
            font-family: 'Nunito', sans-serif;
            font-size: 0.85rem;
            color: #1a1a1a;
            background: #fdf9f8;
            outline: none;
            transition: border-color 0.2s, background 0.2s;
            letter-spacing: 0.04em;
        }

        .field input[type="text"]:focus,
        .field input[type="password"]:focus {
            border-color: #b23e27;
            background: #fff;
        }

        .row-check {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 0.2rem 0 1.5rem;
        }

        .check-wrap {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            cursor: pointer;
        }

        .check-wrap input[type="checkbox"] {
            accent-color: #b23e27;
            width: 14px;
            height: 14px;
            cursor: pointer;
        }

        .check-wrap label {
            font-size: 0.72rem;
            color: #999;
            letter-spacing: 0.06em;
            cursor: pointer;
        }

        .btn-login {
            width: 100%;
            padding: 0.82rem;
            background: #b23e27;
            border: none;
            border-radius: 10px;
            color: #fff;
            font-family: 'Nunito', sans-serif;
            font-size: 0.82rem;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
        }

        .btn-login:hover {
            background: #9a3421;
        }

        .btn-login:active {
            transform: scale(0.99);
        }

        hr {
            border: none;
            border-top: 1px solid #f0e8e5;
            margin: 1.25rem 0 0;
        }

        /* RESPONSIVE */
        @media (max-width: 600px) {
            .card {
                grid-template-columns: 1fr;
            }

            .left {
                padding: 2rem 1.75rem 2.5rem;
                min-height: 200px;
            }
        }
    </style>
</head>

<body>

    <div class="card">

        <!-- LEFT PANEL -->
        <div class="left">
            <div class="logo-area">
                <img src="{{ asset('images/Login-Logo.png') }}"
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