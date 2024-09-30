<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Task Management System</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset("image/avatar_1.png") }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#162131]">
    <div id="app">
        @section("body")
                            
        @show
    </div>
</body>
</html>