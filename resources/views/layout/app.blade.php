<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name', 'Laravel') }}</title>

    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Styles --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap-icons.min.css') }}">

    {{-- Scripts --}}
    <script type="text/javascript" src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <nav class="navbar bg-dark navbar-expand-lg bg-body-tertiary" data-bs-theme="dark">
                <div class="container-fluid">
                    <a class="navbar-brand" href="{{ url('/') }}">
                        {{ config('app.name', 'Laravel') }}
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarContent">
                        @guest
                        <ul class="navbar-nav">
                            <li class="nav-item"> <a class="nav-link" href="{{ route('registro') }}">Registro</a> </li>
                            <li class="nav-item"> <a class="nav-link" href="{{ route('login') }}">Inicia Sesión</a> </li>
                        </ul>
                        @else
                        <ul class="navbar-nav">
                            <li class="nav-item"><a class="nav-link" href="#">Tareas</a></li>
                        </ul>
                        <div class="d-flex ms-auto">
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">{{Auth::user()->name}}</button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Cerrar Sesión</a></li>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </ul>
                            </div>
                        </div>
                        @endguest
                    </div>
                </div>
            </nav>
        </div>
        @yield('content')
    </div>
</body>
</html>