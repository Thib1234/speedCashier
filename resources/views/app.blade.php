<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
</head>

<body>
    <div id="app">
        @auth
            <form action="{{ route('auth.logout') }}" method="post">
                @method("delete")
                @csrf
                <button>Se déconnecter</button>
            </form>
        @endauth
        @guest
            <a href="{{ route('auth.login') }}">Se Connecter</a>
        @endguest
        <heading></heading>
        @yield('content')
        
    </div>
</body>

@vite('resources/js/app.js')

</html>
