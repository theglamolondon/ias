@extends("layouts.main")

@section("link")
@endsection

@section("content")
<div class="container-fluid">
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>{{ ucfirst(\App\Statut::getStatut($piece->etat)) }} {{ $piece->getReference() }}</h2>
                </div>
                <div class="body">
                    <div class="row clearfix">
                    @if($piece->etat != \App\Statut::PIECE_COMPTABLE_FACTURE_ANNULEE)
                        @if($piece->etat == \App\Statut::PIECE_COMPTABLE_PRO_FORMA)
                            <div class="col-lg-2 col-md-3 col-sm-6 col-xs-6">
                                <a class="btn btn-flat waves-effect bg-deep-orange" href="{{ route("facturation.proforma.nouvelle",["from" => App\Metier\Behavior\Notifications::UPDATE_FROM_PROFORMA, "ID" => $piece->referenceproforma]) }}">
                                    <i class="material-icons">edit</i> Modifier
                                </a>
                            </div>
                        @endif
                        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-6">
                            <a href="{{ route("facturation.proforma.nouvelle",["from" => App\Metier\Behavior\Notifications::CREATE_FROM_PROFORMA, "ID" => $piece->referenceproforma]) }}" class="btn btn-flat waves-effect bg-teal">
                                <i class="material-icons">refresh</i> Créer à partir
                            </a>
                        </div>
                        @if($piece->etat == \App\Statut::PIECE_COMPTABLE_PRO_FORMA)
                        <div class="col-lg-2 col-md-3 col-sm-6 col-xs-6">
                            <button class="btn btn-flat waves-effect bg-teal" data-toggle="modal" data-target="#defaultModal"><i class="material-icons">loop</i> Transformer en facture</button>
                        </div>
                        @endif

                        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-6">
                            <a href="{{ route("facturation.switch.livraison", ['id' => $piece->id]) }}" target="_blank" class="btn btn-flat waves-effect bg-teal"><i class="material-icons">local_shipping</i> Bon de livraison</a>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-6">
                            <a href="{{ $piece->printing() }}" target="_blank" class="btn btn-flat waves-effect bg-teal"><i class="material-icons">print</i> Imprimer</a>
                        </div>

                        @if($piece->etat == \App\Statut::PIECE_COMPTABLE_PRO_FORMA)
                        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-6">
                            <a href="{{ route('facturation.envoie.emailchoice',['reference' =>$piece->referenceproforma]) }}" class="btn btn-flat waves-effect bg-light-green"><i class="material-icons">email</i> Envoyer la facture</a>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-6">
                            <a href="{{ route('facturation.switch.annuler',['reference' =>$piece->referenceproforma, '_token' => csrf_token() ]) }}" onclick="return confirm('Voulez-vous vraiment annuler cette facture ?')" class="btn btn-flat waves-effect bg-red"><i class="material-icons">cancel</i> Annuler la facture</a>
                        </div>
                        @endif
                    @endif
                    </div>

                    <hr/>

                    <div class="p-r-10 p-l-10">
                        <div class="row clearfix">
                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                <p class="font-15">Date Pro forma : {{ (new \Carbon\Carbon($piece->creationproforma))->format("d/m/Y") }}</p>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                @if($piece->creationfacture)
                                <p class="font-15">Date facture : {{ (new \Carbon\Carbon($piece->creationfacture))->format("d/m/Y") }}</p>
                                @endif
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                @if($piece->creationbl)
                                <p class="font-15">Date bon de livraison : {{ (new \Carbon\Carbon($piece->creationbl))->format("d/m/Y") }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="row clearfix">
                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                <p class="font-15">Référence Pro forma : {{ \App\Application::getprefixOrder() }}{{ $piece->referenceproforma }}</p>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                @if($piece->referencefacture)
                                    <p class="font-15">Référence facture : {{ $piece->referencefacture }}</p>
                                @endif
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                @if($piece->referencebl)
                                    <p class="font-15">Référence bon de livraison : {{ $piece->referencebl }}</p>
                                @endif
                            </div>
                        </div>

                        <hr/>

                        <div class="row clearfix">
                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6">
                                <p class="h4">Client : <a href="{{ route("partenaire.client", [ "id" => $piece->partenaire->id ]) }}">{{ $piece->partenaire->raisonsociale }}</a></p>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6">
                                <p class="h4">Contact : <a href="callto:{{ $piece->partenaire->telephone }}">{{ $piece->partenaire->telephone }}</a></p>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6">
                                <p class="h4">N°compte contibuable : {{ $piece->partenaire->comptecontribuable }}</p>
                            </div>
                        </div>

                        <div class="objet"><span><strong>Objet : </strong>{{ $piece->objet }}</span></div>
                        <br/>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr class="bg-light-green">
                                        <th width="10%">Référence</th>
                                        <th width="45%">Désignation</th>
                                        <th width="12%" class="amount">P.U HT</th>
                                        <th class="quantity text-center">Quantité</th>
                                        @if($piece->type_piece == \App\PieceComptable::TYPE_FACTURE_MISSION)
                                            <th width="10%">Période</th>
                                        @endif
                                        <th width="10%" class="text-right" width="6%">Remise %</th>
                                        <th width="15%" class="amount">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="">
                                @foreach($piece->lignes as $ligne)
                                    <tr>
                                        <td class="text-center">{{ $ligne->reference ? $ligne->reference : "#"  }}</td>
                                        <td class="">{{ $ligne->designation }}</td>
                                        <td class="amount">{{ number_format($ligne->prixunitaire,0,',',' ') }}</td>
                                        <td class="quantity text-center">{{ number_format($ligne->quantite,0,',',' ') }}</td>
                                        @if($piece->type_piece == \App\PieceComptable::TYPE_FACTURE_MISSION)
                                            <td class="quantity">{{ $ligne->quantite_periode }} {{ $ligne->periode }}</td>
                                        @endif
                                        <td class="remise text-right">{{ $ligne->remise * 100 }} %</td>
                                        <td class="amount">{{ number_format(($ligne->prixunitaire * $ligne->quantite) - ceil($ligne->prixunitaire * $ligne->quantite * $ligne->remise),0,',',' ') }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="2"></td>
                                    @if($piece->type_piece == \App\PieceComptable::TYPE_FACTURE_MISSION)
                                        <td colspan="1"></td>
                                    @endif
                                    <td colspan="2" class="amount font-bold">Montant HT</td>
                                    <td colspan="2" class="amount font-bold">{{ number_format($piece->montantht,0,','," ") }} FCFA</td>
                                </tr>
                                <tr>
                                    <td colspan="2"></td>
                                    @if($piece->type_piece == \App\PieceComptable::TYPE_FACTURE_MISSION)
                                        <td colspan="1"></td>
                                    @endif
                                    <td colspan="2" class="amount font-bold">TVA 18% @if($piece->isexonere)<small>(Exonéré de TVA)</small> @endif</td>
                                    <td colspan="2" class="amount font-bold">{{ number_format(($piece->montantht * $piece->tva),0,','," ") }} FCFA</td>
                                </tr>
                                <tr>
                                    <td colspan="2"></td>
                                    @if($piece->type_piece == \App\PieceComptable::TYPE_FACTURE_MISSION)
                                        <td colspan="1"></td>
                                    @endif
                                    <td colspan="2" class="amount font-bold">Montant TTC</td>
                                    <td colspan="2" class="amount font-bold">{{ number_format(($piece->montantht * ($piece->isexonere ? 1 : (1 + $piece->tva) )),0,','," ") }} FCFA</td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="row clearfix">
                        <div class="col-md-3 col-sm-4 col-xs-12">
                            <blockquote class="blockquote-reverse">
                                <p class="font-underline">Options de paiement</p>
                                <footer>Facture arrêté à la somme de {{ \App\Metier\Finance\NombreToLettre::getLetter($piece->montantht * ($piece->isexonere ? 1 : (1 + $piece->tva) )) }} francs CFA</footer>
                            </blockquote>
                        </div>
                        <div class="col-md-8 col-sm-7 col-xs-12">
                            <p class="m-t-5"><span>Conditions de  paiement </span><span class="font-bold">{{ $piece->conditions }}</span></p>
                            <p class="m-t-5"><span>Validité de l'offre </span><span class="font-bold">{{ $piece->validite }}</span></p>
                            <p class="m-t-5"><span>Délai de validité </span><span class="font-bold">{{ $piece->delailivraison }}</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="defaultModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form class="form-line" action="{{ route("facturation.switch.normal") }}" method="post">
            <div class="modal-content">
                <div class="modal-header bg-light-green">
                    <h4 class="modal-title" id="defaultModalLabel">Passage de Pro forma à la facture</h4>
                </div>
                <div class="modal-body row">
                    {{ csrf_field() }}
                    <input type="hidden" name="referenceproforma" value="{{ $piece->referenceproforma }}">
                    <div class="col-md-4">
                        <label class="form-label">N° bon de commande</label>
                    </div>
                    <div class="col-md-8">
                        <div class="input-group">
                            <div class="form-line">
                                <input type="text" class="form-control date" placeholder="Référence du bon de commande" name="referencebc" id="referencebc">
                            </div>
                            <span class="input-group-addon">
                                <i class="material-icons">receipt</i>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-4">
                            <label class="form-label">N° facture pré-imprimé</label>
                        </div>
                        <div class="col-md-8">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" required class="form-control date" placeholder="N° facture pré-imprimé" name="referencefacture" id="referencefacture">
                                </div>
                                <span class="input-group-addon">
                                    <i class="material-icons">send</i>
                                </span>
                            </div>
                        </div>
                    </div>
                <hr/>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-link waves-effect">Valider</button>
                    <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">Annuler</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section("script")
<script type="application/javascript">
$(function () {
    $('.js-modal-buttons .btn').on('click', function () {
        $('#defaultModal').modal('show');
    });
});
</script>
@endsection