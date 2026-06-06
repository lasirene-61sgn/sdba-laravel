<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Login</title>
    <style>
        /* ================== Global Reset & Base Styles ================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #fdf6f0, #ffe0c0);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            color: #333;
        }

        /* ================== Login Card ================== */
        .login-card {
            width: 100%;
            max-width: 420px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .login-header {
            background: #ff7a00; /* Vibrant orange — your preference! */
            color: white;
            padding: 24px 20px;
            text-align: center;
        }

        .login-header h1 {
            font-weight: 600;
            font-size: 1.8rem;
        }

        .login-body {
            padding: 30px 24px;
        }

        /* ================== Form & Inputs ================== */
        .error-messages {
            background: #fff5f5;
            border-left: 4px solid #ff6b6b;
            color: #d63031;
            padding: 12px 16px;
            margin-bottom: 24px;
            border-radius: 6px;
        }

        .error-messages ul {
            list-style: none;
            padding-left: 0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #444;
        }

        .form-group input {
            width: 100%;
            padding: 14px;
            font-size: 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #ff7a00;
            box-shadow: 0 0 0 3px rgba(255, 122, 0, 0.15);
        }

        .form-group input::placeholder {
            color: #aaa;
        }

        /* ================== Button ================== */
        .submit-btn {
            width: 100%;
            padding: 14px;
            background: #ff7a00;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
        }

        .submit-btn:hover {
            background: #e66f00;
            transform: translateY(-2px);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        /* ================== Responsive Adjustments ================== */
        @media (max-width: 768px) {
            body {
                padding: 15px;
            }

            .login-header h1 {
                font-size: 1.5rem;
            }

            .login-body {
                padding: 24px 20px;
            }
        }

        @media (max-width: 480px) {
            .login-card {
                border-radius: 10px;
            }

            .login-header {
                padding: 20px 16px;
            }

            .login-body {
                padding: 20px 16px;
            }

            .form-group input,
            .submit-btn {
                padding: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <h1>Super Admin Login</h1>
        </div>

        <div class="login-body">
            @if ($errors->any())
                <div class="error-messages">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('superadmin.login') }}">
                @csrf

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password">
                </div>

                <div>
                    <button type="submit" class="submit-btn">
                        Log In as Super Admin
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>