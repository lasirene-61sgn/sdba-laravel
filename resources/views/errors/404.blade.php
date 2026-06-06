<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            color: #1f2937;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .error-container {
            max-width: 500px;
            text-align: center;
            padding: 2rem;
        }
        
        .error-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            background-color: #dbeafe;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .error-icon svg {
            width: 40px;
            height: 40px;
            color: #3b82f6;
        }
        
        .error-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #374151;
            margin-bottom: 0.5rem;
        }
        
        .error-message {
            font-size: 1rem;
            color: #6b7280;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        
        .btn-primary {
            background-color: #4f46e5;
            border-color: #4f46e5;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            border-radius: 0.5rem;
        }
        
        .btn-primary:hover {
            background-color: #4338ca;
            border-color: #4338ca;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        
        <h1 class="error-title">Page Not Found</h1>
        
        <p class="error-message">
            Sorry, the page you're looking for doesn't exist or has been moved.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ url('/') }}" class="btn btn-primary text-white">
                Go to Homepage
            </a>
            
            @auth('admin')
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary">
                    Admin Dashboard
                </a>
            @else
                <a href="{{ route('admin.login') }}" class="btn btn-outline-primary">
                    Login as Admin
                </a>
            @endauth
        </div>
    </div>
</body>
</html>