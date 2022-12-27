@extends("layouts.main")
@php
$totalPeriode = 0;
@endphp
@section("link")
<!-- Bootstrap Material Datetime Picker Css -->
<link href="{{ asset('plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}" rel="stylesheet" />
@endsection

@section("content")
<div class="container-fluid">
    <div class="col-md-12 col-sm-12">
        <div class="card">
            <div class="body">
                <form action="" method="get">
                    <div class="row clearfix">
                        <div class="col-md-2 col-sm-6">
                            <b>Début</b>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">date_range</i>
                                </span>
                                <div class="form-line">
                                    <input name="debut" type="text" class="form-control datepicker" placeholder="Ex: 30/07/2016" value="{{ old("debut",(\Carbon\Carbon::now()->firstOfMonth()->format("d/m/Y"))) }}">
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
                                    <input name="fin" type="text" class="form-control datepicker" placeholder="Ex: 30/07/2016" value="{{ old("fin",Carbon\Carbon::now()->format("d/m/Y")) }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2 col-sm-6">
                            <b>Référence</b>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">credit_card</i>
                                </span>
                                <div class="form-line">
                                    <input name="reference" type="text" class="form-control" placeholder="N° de facture" value="{{ old("reference", request()->query('reference')) }}">
                                </div>
                            </div>
                        </div>

                        @if($statuts)
                        <div class="col-md-3 col-sm-6">
                            <b>Statut</b>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">dvr</i>
                                </span>
                                <select class="form-control selectpicker" id="status" name="status" data-live-search="true" required>
                                    <option value="all">Tous les statuts</option>
                                    @foreach($statuts as $statut)
                                        <option @if(request()->input("status") == $statut) selected @endif>{{ \App\Statut::getStatut($statut) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif

                        <div class="col-md-1">
                            <br/>
                            <button class="btn bg-teal waves-button waves-effect" type="submit">Rechercher</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="header">
                <h2>Liste des pièces comptables</h2>
                <div class="align-right">
                    <a href="{{ route("facturation.proforma.nouvelle", ['type' => \App\PieceComptable::TYPE_FACTURE_PIECE]) }}" class="btn btn-flat waves-effect bg-teal"><i class="material-icons">insert_drive_file</i> Nouvelle pro forma</a>
                    <a href="{{ route("facturation.proforma.nouvelle",['from'=> \App\Metier\Behavior\Notifications::MISSION_OBJECT, 'type' => \App\PieceComptable::TYPE_FACTURE_MISSION]) }}" class="btn btn-flat waves-effect bg-teal"><i class="material-icons">insert_drive_file</i> Nouvelle pro forma de location</a>
                </div>
            </div>
            <div class="body table-responsive">
                <table class="table table-hover table-bordered">
                    <thead>
                    <tr class="bg-teal">
                        <th width="10%">Ref. Pro forma</th>
                        <th width="10%">Ref. Facture</th>
                        <th width="8%">Date</th>
                        <th width="30%">Objet</th>
                        <th>Client</th>
                        <th width="10%">Statut</th>
                        <th width="12%" class="amount">Montant</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($pieces as $piece)
                    <tr>
                        <td><a href="{{ route("facturation.details", ["reference" => urlencode($piece->referenceproforma) ]) }}">{{\App\Application::getprefixOrder()}}{{ $piece->referenceproforma }}</a></td>
                        <td>{{ $piece->referencefacture }}</td>
                        <td>{{ (new \Carbon\Carbon($piece->creationproforma))->format("d/m/Y") }}</td>
                        <td>{{ $piece->objet }}</td>
                        <td>{{ $piece->partenaire->raisonsociale }}</td>
                        <td>{{ \App\Statut::getStatut($piece->etat) }}</td>
                        <td class="amount">{{ number_format($piece->montantht * ($piece->isexonere ? 1 : $piece->tva+1), 0,",", " ") }}</td>
                    </tr>
                        @php
                            $totalPeriode += $piece->montantht * ($piece->isexonere ? 1 : $piece->tva+1);
                        @endphp
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr class="bg-teal">
                        <td></td>
                        <td colspan="3"></td>
                        <td colspan="2" class="text-right">Total</td>
                        <td class="amount">{{ number_format($totalPeriode, 0,",", " ") }}</td>
                    </tr>
                    </tfoot>
                </table>
                {{ $pieces->appends(request()->except([]))->links('vendor.pagination.default') }}
            </div>
        </div>
    </div>
</div>
@endsection
@section("script")
<!-- Moment Plugin Js -->
<script src="{{ asset('plugins/momentjs/moment.js') }}"></script>
<script type="text/javascript"  src="{{ asset('plugins/momentjs/moment-with-locales.min.js') }}"></script>

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
</script>
@endsection