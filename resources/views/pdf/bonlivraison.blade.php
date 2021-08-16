@extends("pdf.layout")
@section("titre") BON DE LIVRAISON @endsection
@section("titre-complement")
    <div class="row quatre-cm">Client :<br/><strong>{{ $pieceComptable->partenaire->raisonsociale }}</strong> </div>
    <div class="row quatre-cm">N° C.C. :<br/><strong>{{ $pieceComptable->partenaire->comptecontribuable }}</strong> </div>
    <div class="row quatre-cm">Téléphone :<br/> <strong>{{ $pieceComptable->partenaire->telephone }}</strong> </div>
    <br style="clear: both" />
@endsection

@section("content")
    <div class="">
        <div class="row" style="width: 4cm; margin: 0 0.5cm">

        </div>

        <div class="row" style="width: 5cm; margin: 0 0.5cm">
            <p><strong>N° Bon de Livraison</strong></p>
            <hr/>
            <p class="item">{{ $pieceComptable->referencebl }}</p>
        </div>

        <div class="row quatre-cm">
            <p><strong>Date</strong></p>
            <hr/>
            <p class="item">{{ (new Carbon\Carbon($pieceComptable->creationbl))->format("d/m/Y") }}</p>
        </div>
    </div>
    <br style="clear: both"/>
    <br/>
    <br/>
    <br/>

    <div class="objet"><span><strong>Objet : </strong>{{ $pieceComptable->objet }}</span></div>
    <br/>
    <br/>
    <table>
        <thead>
        <tr class="">
            <th width="20%">Référence</th>
            <th width="60%">Désignation</th>
            <th class="quantity">Quantité</th>
            @if($pieceComptable->type_piece == \App\PieceComptable::TYPE_FACTURE_MISSION)
            <th width="10%">Période</th>
            @endif
        </tr>
        </thead>
        <tbody>
        @foreach($pieceComptable->lignes as $ligne)
            <tr>
                <td class="center">{{ $ligne->reference ? $ligne->reference : "#" }}</td>
                <td>{{ ucfirst($ligne->designation) }}</td>
                <td class="quantity">{{ number_format($ligne->quantite,0,',',' ') }}</td>
                @if($pieceComptable->type_piece == \App\PieceComptable::TYPE_FACTURE_MISSION)
                <td>{{ $ligne->quantite_periode }} {{ $ligne->periode }}</td>
                @endif
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="">
        <br/>
        <br/>
        <br/>
        <br/>

        <div class="row quatre-cm">
            <p><strong>Emetteur</strong></p>
            <hr/>
            <p class="item">{{ $pieceComptable->utilisateur->employe->nom }} {{ $pieceComptable->utilisateur->employe->prenoms }}</p>
        </div>
        <div class="row quatre-cm"></div>
        <div class="row quatre-cm"></div>
        <div class="row quatre-cm">
            <p><u>Signature Client</u></p>
        </div>
    </div>
    <br style="clear: both;"/>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <div class="">
        <h4><u>Remarque éventuelle du client concernant la livraison</u></h4>
        <p></p>
    </div>
@endsection