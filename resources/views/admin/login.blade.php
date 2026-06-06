<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            /* Modern sports-themed color palette */
            --primary: #3b82f6; /* Blue-500 */
            --primary-dark: #2563eb; /* Blue-600 */
            --primary-light: #93c5fd; /* Blue-300 */
            --secondary: #10b981; /* Emerald-500 */
            --accent: #8b5cf6; /* Violet-500 */
            --dark: #1e293b; /* Slate-800 */
            --light: #f8fafc; /* Slate-50 */
            --gray: #94a3b8; /* Slate-400 */
            --success: #22c55e; /* Green-500 */
            --warning: #f59e0b; /* Amber-500 */
            --danger: #ef4444; /* Red-500 */
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 1rem;
        }

        .login-container {
            width: 100%;
            max-width: 450px;
            background-color: white;
            border-radius: 1rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .login-header {
            background-color: var(--dark);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .login-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0;
        }

        .login-body {
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark);
        }

        .form-control {
            display: block;
            width: 100%;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            line-height: 1.5;
            color: #334155;
            background-color: #ffffff;
            background-clip: padding-box;
            border: 1px solid #cbd5e1;
            border-radius: 0.5rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .form-control:focus {
            color: #334155;
            background-color: #ffffff;
            border-color: var(--primary-light);
            outline: 0;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
        }

        .btn {
            display: block;
            width: 100%;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            font-weight: 500;
            text-align: center;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
        }

        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .alert-danger {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .footer {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--gray);
            font-size: 0.875rem;
        }

        @media (max-width: 480px) {
            .login-container {
                max-width: 100%;
            }
            
            .login-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Admin Login</h1>
        </div>
        
        <div class="login-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}">
                @csrf

                <div class="form-group">
                    <label for="login" class="form-label">Email or Phone Number</label>
                    <input 
                        id="login" 
                        type="text" 
                        name="login" 
                        value="{{ old('login') }}" 
                        required 
                        autofocus
                        class="form-control"
                        placeholder="Enter your email or phone number"
                    >
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input 
                        id="password" 
                        type="password" 
                        name="password" 
                        required
                        class="form-control"
                        placeholder="Enter your password"
                    >
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        Log In as Admin
                    </button>
                </div>
            </form>
            
            <div class="footer">
                <p>© {{ date('Y') }} Admin Panel. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>