<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Scripts -->
     <script src="{{asset('js/bootstrap.min.js')}}" defer></script>
     <!-- Styles -->
     <link href="{{asset('css/bootstrap.min.css')}}" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h1 class="form-group">{{asset('css/bootstrap.min.css')}}</h1>
            <label for="my-input">Text</label>
            <input id="my-input" class="form-control" type="text" name="">
        </div>
    </div>

</body>

</html>
