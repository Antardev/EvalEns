<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Questionnaire') — ÉvalENS</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('dashboard/images/favicon.png') }}">
    <link href="{{ asset('dashboard/css/style.css') }}" rel="stylesheet">
    <style>
        body { background: #f4f6fb; min-height: 100vh; }
        .q-header {
            background: #fff;
            border-bottom: 1px solid #e9ecef;
            padding: 16px 0;
            margin-bottom: 32px;
        }
        .q-header img { height: 40px; object-fit: contain; }
        .star-group { display: flex; gap: 6px; flex-wrap: wrap; }
        .star-label {
            display: inline-flex; align-items: center; justify-content: center;
            width: 44px; height: 44px; border-radius: 8px; border: 2px solid #dee2e6;
            font-weight: 700; font-size: 16px; cursor: pointer; transition: all .15s;
            color: #6c757d; background: #fff;
        }
        input[type=radio]:checked + .star-label,
        .star-label:hover { border-color: #2F4CDD; background: #2F4CDD; color: #fff; }
        input[type=radio] { display: none; }
        .score-legend { font-size: 11px; color: #adb5bd; display: flex; justify-content: space-between; }
    </style>
    @stack('styles')
</head>
<body>

<div class="q-header">
    <div class="container">
        <img src="{{ asset('dashboard/images/evalens-logo.svg') }}" alt="ÉvalENS">
    </div>
</div>

<div class="container pb-5">
    @yield('content')
</div>

<script src="{{ asset('dashboard/vendor/global/global.min.js') }}"></script>
@stack('scripts')
</body>
</html>
