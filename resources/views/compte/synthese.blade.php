@extends('layouts.main')
@section("link")
    <!-- Bootstrap Material Datetime Picker Css -->
    <link href="{{ asset('plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}" rel="stylesheet" />
@endsection

@section('content')
    <div class="container-fluid">
        <div class="col-md-12 col-sm-12">
            <div class="card">
                <div class="header">
                    <div class="row">
                        <div class="col-md-8">
                            <h3>Synthèse des sous comptes</h3>
                        </div>
                        <div class="col-md-3">
                            <div class="col-md-offset-3 col-md-3 col-sm-6">
                                <a target="_blank" href="{{ route("print.compte.synthese", request()->query()) }}" class="btn bg-light-green waves-button waves-effect">Imprimer</a>
                            </div>
                        </div>
                    </div>

                    <hr/>
                    <form action="" method="get">
                        <div class="row clearfix">
                            <div class="col-md-2 col-sm-6">
                                <b>Début</b>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="material-icons">date_range</i>
                                    </span>
                                    <div class="form-line">
                                        <input name="debut" type="text" class="form-control datepicker" placeholder="Ex: {{ date('d/m/Y') }}" value="{{ $debut->format("d/m/Y") }}">
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
                                        <input name="fin" type="text" class="form-control datepicker" placeholder="Ex: {{ date('d/m/Y') }}" value="{{ $fin->format("d/m/Y") }}">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-6">
                                <b>Objet</b>
                                <div class="input-group">
                            <span class="input-group-addon">
                                <i class="material-icons">message </i>
                            </span>
                                    <div class="form-line">
                                        <input name="objet" type="text" class="form-control" placeholder="Justificatif" value="{{ old("objet", request()->query('objet')) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-1">
                                <br/>
                                <button class="btn bg-teal waves-button waves-effect" type="submit">Rechercher</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="body">
                    <div class="row clearfix">
                        <div class="body table-responsive">
                            <table class="table table-bordered table-hover ">
                                <thead>
                                <tr class="bg-green">
                                    <th>Date écriture</th>
                                    <th>Date action</th>
                                    <th>Objet</th>
                                    <th>Montant</th>
                                    <th>Balance</th>
                                    <th>Opérateur</th>
                                </tr>
                                </thead>
                                <tbody class="table-hover">
                                @foreach($lignes as $ligne)
                                    <tr>
                                        <td>{{ (new \Carbon\Carbon($ligne->dateaction))->format('d/m/Y H:i:s') }}</td>
                                        <td>{{ (new \Carbon\Carbon($ligne->dateoperation))->format('d/m/Y') }}</td>
                                        <td>{{ $ligne->objet }} <b>[Compte {{ $ligne->compte->libelle }}] </b></td>
                                        <td><a title="{{ $ligne->observation }}">{{ number_format($ligne->montant, 0, ',',' ') }}</a></td>
                                        <td>{{ number_format($ligne->balance, 0, ',',' ') }}</td>
                                        <td>{{ $ligne->utilisateur->employe->nom }} {{ $ligne->utilisateur->employe->prenoms }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            {{ $lignes->links('vendor.pagination.default') }}
                        </div>
                    </div>
                </div>
            </div>
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

        function edit(id, dateEcriture, objet) {
            $('#md-id').val(id);
            $('#md-dateoperation').val(dateEcriture);
            $('#md-objet').val(objet);
            $('#editModal').modal('show');
        }

    </script>
@endsection