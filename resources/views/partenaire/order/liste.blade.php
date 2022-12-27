@extends("layouts.main")
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
                    <h2>Liste des factures fournisseur</h2>
                    <div class="align-right">
                        <a href="{{ route("partenaire.fournisseur.new") }}" class="btn btn-flat waves-effect bg-teal"><i class="material-icons">insert_drive_file</i> Nouvelle facture fournisseur</a>
                    </div>
                </div>
                <div class="body table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead>
                        <tr class="bg-teal">
                            <th width="5%">#</th>
                            <th width="10%">Ref. Facture</th>
                            <th width="8%">Date</th>
                            <th width="30%">Objet</th>
                            <th>Fournisseur</th>
                            <th width="15%">Statut</th>
                            <th width="12%" class="amount">Montant TTC</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($pieces as $piece)
                            <tr>
                                <td>
                                    <a class="btn bg-deep-purple waves-effect" href="{{ route('partenaire.fournisseur.factures.details', ['id' => $piece->id]) }}" title="Détails facture"><i class="material-icons">insert_drive_file</i></a>
                                </td>
                                <td>{{ $piece->reference ?? $piece->numerobc }}</td>
                                <td>{{ (new \Carbon\Carbon($piece->datepiece))->format("d/m/Y") }}</td>
                                <td>{{ $piece->objet }}</td>
                                <td>{{ $piece->partenaire->raisonsociale }}</td>
                                <td>{{ \App\Statut::getStatut($piece->statut) }}</td>
                                <td class="amount">{{ number_format($piece->montantht + $piece->montanttva, 0,",", " ") }}</td>
                            </tr>
                        @endforeach
                        </tbody>
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