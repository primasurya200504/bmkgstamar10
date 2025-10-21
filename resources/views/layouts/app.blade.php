<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin</title>
    <link rel="icon" type="image/png" sizes="30x35" href="/img/favicon-32x32.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #1f2937;
        }

        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .main-content {
            background-color: #f8fafc;
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            font-size: 0.875rem;
            line-height: 1.25rem;
            font-weight: 500;
            border-radius: 9999px;
        }

        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            transition: all 0.3s ease;
        }

        .modal-overlay {
            backdrop-filter: blur(8px);
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                z-index: 50;
                transition: left 0.3s ease;
            }

            .sidebar.open {
                left: 0;
            }

            .main-content {
                width: 100%;
            }

            .hamburger {
                display: block;
            }
        }

        @media (min-width: 769px) {
            .hamburger {
                display: none;
            }
        }
    </style>
</head>

<body class="flex min-h-screen">
    <!-- Hamburger Menu for Mobile -->
    <button class="hamburger fixed top-4 left-4 z-40 bg-blue-600 text-white p-2 rounded-md md:hidden"
        onclick="toggleSidebar()">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>

    @include('layouts.navigation')

    <!-- Main Content -->
    <div class="flex-1 main-content">
        @yield('content')
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('open');
        }

        // Function to highlight active navigation item
        function highlightActiveNav() {
            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('nav a');

            navLinks.forEach(link => {
                link.classList.remove('bg-white', 'bg-opacity-20');
                link.classList.add('hover:bg-white', 'hover:bg-opacity-20');
            });

            navLinks.forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('bg-white', 'bg-opacity-20');
                    link.classList.remove('hover:bg-white', 'hover:bg-opacity-20');
                }
            });
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            highlightActiveNav();
        });
    </script>
</body>

</html>
