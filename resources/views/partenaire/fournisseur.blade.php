@extends("layouts.main")

@section('link')
    <!-- Bootstrap Material Datetime Picker Css -->
    <link href="{{ asset('plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}" rel="stylesheet" />

    <!-- Bootstrap Select Css -->
    <link href="{{ asset('plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet" />
@endsection

@section("content")
@php
$rap = 0;
@endphp
<div class="container-fluid">
    <!-- Basic Table -->
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <div class="row clearfix">
                        <div class="col-md-12 col-xs-12">
                            <h3><small>Point fournisseur</small> <br/>{{ $partenaire->raisonsociale }}</h3>
                            <hr/>
                        </div>
                    </div>
                    <div class="row clearfix">
                        <form method="get">
                            <div class="col-md-2 col-sm-6">
                                <b>Début</b>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="material-icons">date_range</i>
                                    </span>
                                    <div class="form-line">
                                        <input name="debut" type="text" class="form-control datepicker" placeholder="Ex: 30/07/2016" value="{{ request()->query("debut") ?? \Carbon\Carbon::now()->firstOfMonth()->format("d/m/Y") }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <b>Fin</b>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="material-icons">date_range</i>
                                    </span>
                                    <div class="form-line">
                                        <input name="fin" type="text" class="form-control datepicker" placeholder="Ex: 30/07/2016" value="{{ request()->query("fin") ?? Carbon\Carbon::now()->format("d/m/Y") }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <br/>
                                <button class="btn bg-light-green waves-button waves-effect" type="submit">Rechercher</button>
                            </div>
                        </form>
                        <div class="col-md-2 col-xs-12 align-right">
                            <br>
                            <button class="btn waves-li waves-effect bg-brown" data-toggle="modal" data-target="#defaultModal"><i class="material-icons left">attach_money</i>Reglement facture</button>
                        </div>
                        <div class="col-md-2 col-xs-12 align-right">
                            <br>
                            <a href="{{ route("partenaire.fournisseur.new", ["from" => $partenaire->id]) }}" class="btn btn-flat waves-effect bg-teal"><i class="material-icons">insert_drive_file</i> Nouvelle facture fournisseur</a>
                        </div>
                    </div>
                </div>
                <div class="body table-responsive">
                    <table class="table table-bordered table-hover ">
                        <thead>
                        <tr class="bg-green">
                            <th>Date</th>
                            <th>Référence</th>
                            <th>Objet</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Moyen Paiement</th>
                            <th>Date de paiement</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if($pieces)
                        @foreach($pieces as $piece)
                        <tr>
                            <td>{{ (new \Carbon\Carbon($piece->datepiece))->format('d/m/Y') }}</td>
                            <td><a href="{{ route("partenaire.fournisseur.factures.details",["id"=>$piece->id]) }}" title="Consulter la facture">{{ $piece->reference ?? $piece->numerobc }}</a></td>
                            <td><a href="{{ route("partenaire.fournisseur.factures.details",["id"=>$piece->id]) }}" title="Consulter la facture">{{ $piece->objet }}</a></td>
                            <td>{{ number_format($piece->montantht + $piece->montanttva, 0, '', ' ') }}</td>
                            <td>{{ \App\Statut::getStatut($piece->statut) }}</td>
                            <td><span title="{{ $piece->remarquepaiement }}">{{ $piece->moyenPaiement ? $piece->moyenPaiement->libelle : null }}</span></td>
                            <td>{{ $piece->datereglement ? (new \Carbon\Carbon($piece->datereglement))->format('d/m/Y') : null }}</td>
                        </tr>
                        @php
                            if(!in_array($piece->statut, [\App\Statut::PIECE_COMPTABLE_FACTURE_PAYEE,
                            \App\Statut::PIECE_COMPTABLE_BON_COMMANDE ], false)){
                                $rap += $piece->montantht + $piece->montanttva;
                            }
                        @endphp
                        @endforeach
                        <tr>
                            <td colspan="10"><h5>RAP/Total : {{ number_format($rap, 0, '', ' ') }} F CFA / {{ number_format($CA->somme, 0, '', ' ') }} F CFA</h5></td>
                        </tr>
                        @else
                            <td colspan="10">
                                <h3>Aucune pièce comptable n'a été passée avec ce fournisseur !</h3>
                            </td>
                        @endif
                        </tbody>
                    </table>
                    {{ $pieces->appends(request()->except([]))->links('vendor.pagination.default') }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="defaultModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <form class="form-line" action="{{ route("versement.facture.fournisseur") }}" method="post">
            <div class="modal-content">
                <div class="modal-header bg-light-green">
                    <h4 class="modal-title" id="defaultModalLabel">Règlement de facture fournisseur</h4>
                </div>
                <div class="modal-body">
                    {{ csrf_field() }}
                    <div class="row clearfix">
                        <div class="col-md-3 col-xs-12 form-control-label">
                            <label for="datereglement">Date</label>
                        </div>
                        <div class="col-md-8 col-xs-12 ">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" required name="datereglement" id="datereglement" class="datepicker form-control" placeholder="JJ/MM/AAAA" value="{{ old('datereglement',\Carbon\Carbon::now()->format("d/m/Y")) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row clearfix">
                        <div class="col-md-3 col-xs-12 form-control-label">
                            <label>Liste des factures</label>
                        </div>
                        <div class="col-md-8 col-xs-12">
                            <div class="form-group">
                            @foreach($pieces as $piece)
                                @if($piece->statut != \App\Statut::PIECE_COMPTABLE_FACTURE_PAYEE)
                                    <input type="checkbox" id="md_checkbox_{{ $piece->id }}" name="facture[]" class="chk-col-teal" checked  value="{{ $piece->id }}"/>
                                    <label for="md_checkbox_{{ $piece->id }}">{{ $piece->reference }} {{ $piece->objet }} : </label> {{ number_format($piece->montantht+$piece->montanttva, 0, '', ' ') }} F CFA
                                @endif
                            @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="row clearfix">
                        <div class="col-md-3 col-xs-12 form-control-label">
                            <label for="moyenpaiment_id">Moyen de paiement</label>
                        </div>
                        <div class="col-md-8 col-xs-12">
                            <div class="form-group">
                                <select class="form-control selectpicker" id="moyenpaiement_id" name="moyenpaiement_id" required>
                                    @foreach($moyenReglements as $moyenReglement)
                                        <option value="{{ $moyenReglement->id }}">{{ $moyenReglement->libelle }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row clearfix">
                        <div class="col-md-3 col-xs-12 form-control-label">
                            <label for="remarquepaiement">Observations</label>
                        </div>
                        <div class="col-md-8 col-xs-12">
                            <div class="form-group">
                                <textarea maxlength="220" id="remarquepaiement" name="remarquepaiement" class="form-control" placeholder="Note sur le paiement"></textarea>
                            </div>
                        </div>
                    </div>

                    <hr/>

                    <div class="row clearfix">
                        <div class="col-lg-offset-2 col-md-offset-2 col-sm-offset-4 col-xs-offset-5">
                            <button type="submit" class="btn btn-primary m-t-15 waves-effect">Ajouter</button>
                            <button type="reset" class="modal-close btn btn-default m-t-15 waves-effect">Annuler</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
    <!-- Bootstrap Material Datetime Picker Plugin Js -->
    <script src="{{ asset('plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js' )}}"></script>
    <!-- Multi Select Plugin Js -->
    <script src="{{ asset('plugins/bootstrap-select/js/bootstrap-select.js') }}"></script>
    <script type="text/javascript" >
        $('.datepicker').bootstrapMaterialDatePicker({
            format: 'DD/MM/YYYY',
            clearButton: false,
            nowButton: true,
            weekStart: 1,
            time: false,
            lang: 'fr',
            cancelText : 'ANNULER',
            nowText : 'AUJOURD\'HUI'
        });
    </script>
@endsection