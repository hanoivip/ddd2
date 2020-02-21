<html lang="{{ app()->getLocale() }}">
<head>
<title>@yield('title')</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="{{asset('css/app.css')}}" rel="stylesheet" type="text/css">
@if (Auth::check())
<script type="text/javascript">
    	var API_TOKEN = "{{ Auth::user()->api_token }}";
    </script>
@endif
</head>
<body>
	<div class="main">@yield('content')</div>
	<script type="text/javascript" src="{{asset('js/app.js')}}"></script>
</body>
</html>