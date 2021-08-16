<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ config('app.url') }}</title>
    <link rel="stylesheet" type="text/css" href="{{public_path('pdf/pdf.css')}}" media="all" />
    <style>
        .page{
            page-break-after: auto;
        }
    </style>
</head>
<body>
<header class="clearfix">
    <div>
        <div id="logo">
            <br/><br/><br/><br/><br/><br/><br/>
            <br/><br/><br/><br/><br/><br/>
        </div>
    </div>
</header>

<div id="baner">
    <div class="row titre">
        <span class="h1">@yield("titre")</span>
    </div>
    @yield("titre-complement")
</div>

<br/>
<br/>
<br/>
<br/>
<br/>
<main class="page">
    @yield('content')
</main>
</body>
</html>