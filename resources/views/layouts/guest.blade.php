<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Bootstrap CSS -->
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        html, body {
            height: 100%;
        }
        img {
            max-width: 100%;
            object-fit: cover;
            height: 100vh;
        }
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="container-fluid px-0">
        <div class="row g-0">
            <!-- Left image -->
            <div class="col-md-6 d-none d-md-block">
                <img src="{{ asset('images/background/1.jpg') }}" alt="Background">
            </div>

            <!-- Right form -->
            <div class="col-12 col-md-6 auth-container">
                <div class="w-100" style="max-width: 480px;">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            @yield('content')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
</body>
</html>
