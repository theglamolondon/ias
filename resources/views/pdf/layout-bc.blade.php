<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ config('app.url') }}</title>
    <link rel="stylesheet" type="text/css" href="{{ public_path('pdf/pdf.css') }}" media="all" />

    <style>
        .page{page-break-after: auto;}
    </style>
</head>
<body class="bc">
<header class="clearfix">
    <div>
        <div id="logo" class="row douze-cm">
            <a href="{{ config('app.url') }}"><img src="{{ public_path('images/logo-ias.png') }}"/></a>
        </div>
        <div class="row" style="border: 1px solid #0258c3;height: 1.7cm;margin-left:0.5cm; margin-right: 0.6cm;"></div>
        <div class="row six-cm location">
            <p><strong><small>IVOIRE AUTO SERVICES</small></strong></p>
            <p><small>Cocody, Angré 8ème tranche <br/>13 BP 1715 Abidjan 13</small></p>
            <p><small>N° CC : 0526299 H </small></p>
            <p><small>N° RC : CI-ABJ-2008-A-1483</small></p>
        </div>
    </div>
</header>
<br />

<div id="baner" class="clearfix">
    <span class="h1">@yield("titre")</span>
</div>
<br />
<div>
    <div class="row two-col reference">
        @yield("reference")
    </div>
    <div class="row" style="margin-left:1cm; margin-right: 0.4cm;"></div>
    <div class="row two-col client">
        @yield("client")
    </div>
</div>
<br style="clear: both;"/>

<hr style="border: 1px solid #eeeeee"/>
<div style="padding: 2px 5px;">
    <p style="font-size: 6pt;">Le N° de cette commande doit impérativement figurer sur votre facture. A défaut, cette dernière vous sera retournée. <br/>
        L’accusée de réception de la présente commande est à retourner dans les (03) jours de la réception de la commande, daté, signé et revêtu de votre cachet commercial à l’adresse email indiqué ci-dessus. Passé ce délai, la commande sera considérée comme acceptée dans tous ses termes et conditions par le fournisseur ou le sous-traitant. <br/>
        La présente commande est soumise prioritairement : </p>
    <ol style="margin: 0;">
        <li style="font-size: 6pt;">Aux conditions particulières, figurant sur la présente commande</li>
        <li style="font-size: 6pt;">Aux conditions du contrat de prestation et/ou service et/ou de sous-traitance lorsque celui-ci existe</li>
        <li style="font-size: 6pt;">Aux conditions du contrat cadre lorsque celui-ci existe</li>
        <li style="font-size: 6pt;">A nos conditions générales d’achat (CGA)</li>
    </ol>
    <p style="font-size: 6pt;">Pour les fournitures de services, les dispositions du contrat de sécurité, sont également applicables.<br/>
        L’acceptation expresse ou tacite de la commande implique l’acceptation sans réserve, de l’ensemble de ses conditions.</p>
</div>
<hr style="border: 1px solid #eeeeee"/>
<br/>
<main class="page">
    @yield('content')
</main>

<footer>
    <p>Situé à Angré 8ème tranche non loin du carrefour de la prière.</p>
    <p>Tel : +225 : 07 07 93 97 12 / 07 07 94 08 08 /05 06 72 68 83 13 BP 1715 Abidjan 13</p>
    <p>N°CC : 1912797 L Réel Simplifié d’Imposition Centre des Impôts de la Djibi N° RC : CI-ABJ-2019-B- 02715 NSIA N° 035361963575</p>
    <p>IBAN : CI93 CI04 2012 - Compte Bancaire BIAO N° 03536196357524  - SWIFT Code : BIAOCIABXXX</p>
    <p>Email : commercial@ivoireautoservices.net</p>
</footer>
</body>
</html>