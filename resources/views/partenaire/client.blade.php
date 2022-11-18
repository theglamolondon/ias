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
                        <div class="col-md-6 col-xs-12">
                            <h3><small>Point client</small> <br/>{{ $partenaire->raisonsociale }}</h3>
                        </div>
                        <div class="col-md-6 col-xs-12 right">
                            <a target="_blank" class="btn bg-blue btn-flat waves-effect" href="{{ route("print.pointclient", ["id" => request()->route('id')]) }}">Imprimer</a>
                        </div>
                    </div>
                    <hr/>
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
                            <div class="col-md-3 col-sm-6">
                                <b>Statut</b>
                                <select class="form-control" id="status" name="status">
                                    <option value="*">Tous les statuts</option>
                                    @foreach($status as $k => $v)
                                        <option value="{{ $k }}" @if(old("status", request()->query("status")) == $k) selected @endif>{{ $v }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <br/>
                                <button class="btn bg-teal waves-button waves-effect" type="submit">Rechercher</button>
                            </div>
                        </form>
                        <div class="col-md-3 col-xs-12 align-right">
                            <button class="btn btn-flat waves-effect bg-purple" data-toggle="modal" data-target="#defaultModal"><i class="material-icons">money</i>Reglement facture</button>
                        </div>
                    </div>
                </div>
                <div class="body table-responsive">
                    <table class="table table-bordered table-hover ">
                        <thead>
                        <tr class="bg-green">
                            <th>
                                <input style="position: initial;left: 0;opacity: 1;height: 20px;width: 18px;" id="md_checkbox_0" type="checkbox" class="checkbox" data-switch="0">
                            </th>
                            <th>Date</th>
                            <th>N° Facture</th>
                            <th>N° Pro forma</th>
                            <th>N° BL</th>
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
                            <td>
                                @if($piece->etat != \App\Statut::PIECE_COMPTABLE_FACTURE_PAYEE)
                                <input style="position: initial;left: 0;opacity: 1;height: 20px;width: 18px;"  id="md_checkbox_{{ $piece->id }}" type="checkbox" class="checkbox facture" data-id="{{ $piece->id }}" data-montant="{{round($piece->montantht * 1+ ($piece->isexonere ? 0 : $piece->tva))}}" data-facture="{{ $piece->referencefacture }}">
                                @endif
                            </td>
                            <td>{{ (new \Carbon\Carbon($piece->creationfacture ?? $piece->creationproforma))->format('d/m/Y') }}</td>
                            <td><a href="{{ route('facturation.details',['reference' => $piece->referenceproforma]) }}">{{ $piece->referencefacture }}</a></td>
                            <td><a href="{{ route('facturation.details',['reference' => $piece->referenceproforma]) }}">{{ $piece->referenceproforma }}</a></td>
                            <td>{{ $piece->referencebl }}</td>
                            <td>{{ $piece->objet }}</td>
                            <td>{{ number_format(round($piece->montantht * 1+ ($piece->isexonere ? 0 : $piece->tva)), 0, '', ' ') }}</td>
                            <td>{{ \App\Statut::getStatut($piece->etat) }}</td>
                            <td><span title="{{ $piece->remarquepaiement }}">{{ $piece->moyenPaiement ? $piece->moyenPaiement->libelle : null }}</span></td>
                            <td>{{ $piece->datereglement ? (new \Carbon\Carbon($piece->datereglement))->format('d/m/Y') : null }}</td>
                        </tr>
                        @php
                            if(!in_array($piece->etat ,[\App\Statut::PIECE_COMPTABLE_FACTURE_PAYEE, \App\Statut::PIECE_COMPTABLE_PRO_FORMA, \App\Statut::PIECE_COMPTABLE_FACTURE_ANNULEE])){
                                $rap += $piece->montantht * 1+ ($piece->isexonere ? 0 : $piece->tva);
                            }
                        @endphp
                        @endforeach
                        <tr>
                            <td colspan="10"><h5>Total : {{ number_format($rap, 0, '', ' ') }} F CFA</h5></td>
                        </tr>
                        @else
                            <td colspan="10">
                                <h3>Aucune pièce comptable n'a été passée avec ce client !</h3>
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
        <form class="form-line" action="{{ route("versement.facture.client") }}" method="post">
            <div class="modal-content">
                <div class="modal-header bg-light-green">
                    <h4 class="modal-title" id="defaultModalLabel">Règlement de facture client</h4>
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
                            <label>Factures</label>
                        </div>
                        <div class="col-md-8 col-xs-12" >
                            <div class="form-group" id="liste"></div>
                            <h5 class="text-danger">Total <b id="total">0</b> F CFA</h5>
                        </div>
                    </div>

                    <div class="row clearfix">
                        <div class="col-md-3 col-xs-12 form-control-label">
                            <label for="moyenpaiement_id">Moyens de paiement</label>
                        </div>
                        <div class="col-md-8 col-xs-12">
                            <div class="form-group">
                                <select class="form-control" id="moyenpaiement_id" name="moyenpaiement_id" required>
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
                            <a data-dismiss="modal" class="modal-close btn btn-default m-t-15 waves-effect">Annuler</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
@section("script")
    <!-- Moment Plugin Js -->
    <script src="{{ asset('plugins/momentjs/moment.js') }}"></script>

    <!-- Bootstrap Material Datetime Picker Plugin Js -->
    <script src="{{ asset('plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js' )}}"></script>
    <script type="text/javascript">
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

        var somme = 0;
        $("#defaultModal").on('shown.bs.modal', function () {
            $('input[type="checkbox"]:checked.facture').each(function (i) {
                $("#liste").append("<input type=\"hidden\" name=\"facture[]\" value=\""+$(this).data("id")+"\"/><label> n°"+$(this).data("facture")+"</label> | ");
                //console.log((this));
                somme += parseInt($(this).data("montant"))
            });
            $("#total").text(somme);
        });

        $("#md_checkbox_0").click(function () {
            console.log($("#md_checkbox_0").is(":checked"));
            $('input[type="checkbox"].facture').each(function (i) {
                  $(this).prop("checked",$("#md_checkbox_0").is(":checked"));
            });
        });
    </script>
@endsection