@props(['dir'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{$dir ? 'rtl' : 'ltr'}}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
<!-- PWA Meta Tags -->
<meta name="theme-color" content="#ffffff">
<link rel="manifest" href="{{ asset('manifest.json') }}">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="Your App Name">
<link rel="apple-touch-icon" href="{{ asset('images/icons/icon-192x192.png') }}">
<script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register("{{ asset('service-worker.js') }}");
    }
</script>

    <title>{{env('APP_NAME')}} | Responsive Bootstrap 5 Admin Dashboard Template</title>

    @include('partials.dashboard._head')
</head>
<body class="" >
@include('partials.dashboard._body')
</body>

</html>
