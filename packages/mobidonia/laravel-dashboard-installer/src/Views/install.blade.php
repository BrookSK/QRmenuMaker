<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Installation - {{ config('app.name', 'Laravel') }}</title>
    <link rel="shortcut icon" href="/images/default/icon.png">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@1/dist/tailwind.min.css" rel="stylesheet">
</head>
<body  class="pt-10  bg-gradient-to-r from-purple-500 to-pink-500">
<div>
    <div class="flex h-screen py-6" style="height: 100vh">
        <div class="container m-auto">
            <div class="mb-10 bg-white shadow overflow-hidden sm:rounded-lg" style="opacity:0.9">
                <div class="px-4 py-8 border-b border-gray-200 sm:px-6">
                    <div class="flex justify-center items-center">
                        <h2 class="pl-6 uppercase font-medium text-2xl text-gray-800">{{ config('app.name', 'Laravel') }} Installation</h2>
                    </div>
                </div>
                <div class="px-4 py-5 sm:px-6">
                    @yield('step')
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
