<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Room Password | ekiliConvo</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="images/icons/favicon.jpeg" rel="icon">
    <link href="images/icons/favicon.jpeg" rel="apple-touch-icon">
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='{{asset("assets/styles/main.css")}}'>
</head>
<body>
    <header id="nav">
        <div class="nav--list">
            <a href="{{route('home')}}">>
                <h3 id="logo">
                    <span>ekiliConvo</span>
                </h3>
            </a>
        </div>
    </header>

    <main id="room-password-container">
        <div id="form__container">
            <div id="form__container__header">
                <p>🔒 This room is password protected</p>
            </div>

            <div id="form__content__wrapper">
                <h2>Enter Room Password</h2>
                <p>Room: <strong>{{ $roomName }}</strong></p>
                
                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form id="password-form" method="POST" action="{{ route('room.validate-password', $roomUuid) }}">
                    @csrf
                    <div class="form__field__wrapper">
                        <label>Password</label>
                        <input type="password" name="password" placeholder="Enter room password" required/>
                    </div>

                    <div class="form__field__wrapper">
                        <input type="submit" value="Join Room" />
                    </div>
                </form>

                <div class="form__field__wrapper">
                    <a href="{{ route('home') }}" class="secondary-link">← Back to Lobby</a>
                </div>
            </div>
        </div>
    </main>

    <style>
        #room-password-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
        }

        #form__container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            max-width: 400px;
            width: 90%;
        }

        #form__container__header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        #form__container__header p {
            font-size: 1.2rem;
            color: #666;
            margin: 0;
        }

        #form__content__wrapper h2 {
            text-align: center;
            margin-bottom: 1rem;
            color: #333;
        }

        #form__content__wrapper p {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #666;
        }

        .alert {
            padding: 10px;
            margin-bottom: 1rem;
            border-radius: 4px;
        }

        .alert-danger {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .form__field__wrapper {
            margin-bottom: 1rem;
        }

        .form__field__wrapper label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
        }

        .form__field__wrapper input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .form__field__wrapper input[type="password"]:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
        }

        .form__field__wrapper input[type="submit"] {
            width: 100%;
            padding: 12px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .form__field__wrapper input[type="submit"]:hover {
            background: #0056b3;
        }

        .secondary-link {
            color: #666;
            text-decoration: none;
            font-size: 14px;
        }

        .secondary-link:hover {
            color: #007bff;
        }
    </style>
</body>
</html>