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
            <a href="{{ config('app.url') }}"><img src="{{ public_path('images/logo-ias.png') }}"/></a>
        </div>
    </div>
</header>
<hr/>
<div id="baner">
    <div class="row titre">
        <span class="h1">@yield("titre")</span>
    </div>
    @yield("titre-complement")
</div>
<hr/>

<br/>
<br/>
<br/>
<main class="page">
    @yield('content')
</main>

<footer>
    <p>Situé à Angré 8ème tranche non loin du carrefour de la prière.</p>
    <p>Tel : +225 : 07 07 93 97 12 / 07 07 94 08 08 /05 06 72 68 83 13 BP 1715 Abidjan 13</p>
    <p>N°CC : 1912797 L Réel Normal d’Imposition Centre des Impôts de la Djibi N° RC : CI-ABJ-2019-B- 02715 NSIA N° 035361963575</p>
    <p>IBAN : CI93 CI04 2012 - Compte Bancaire BIAO N° 03536196357524  - SWIFT Code : BIAOCIABXXX</p>
    <p>Email : commercial@ivoireautoservices.net</p>
</footer>
</body>
</html>