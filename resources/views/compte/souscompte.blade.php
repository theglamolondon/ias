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
                <h3>Sous compte : {{ $souscompte->libelle }} | solde : <b class="bg-green">{{ number_format($solde, 0,',',' ') }} FCFA </b></h3>
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
                                    <input name="debut" type="text" class="form-control datepicker" placeholder="Ex: {{ date('d/m/Y') }}" value="{{ old("debut",(\Carbon\Carbon::now()->firstOfMonth()->format("d/m/Y"))) }}">
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
                                    <input name="fin" type="text" class="form-control datepicker" placeholder="Ex: {{ date('d/m/Y') }}" value="{{ old("fin",Carbon\Carbon::now()->format("d/m/Y")) }}">
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
                    <div class="col-md-3 col-sm-6">
                        <button class="btn bg-blue waves-button waves-effect" data-toggle="modal" data-target="#defaultModal">Nouvelle opération</button>
                    </div>
                    @if(\Illuminate\Support\Facades\Auth::user()->authorizes(\App\Service::DG, \App\Service::INFORMATIQUE))
                    <div class="col-md-3 col-sm-6">
                            <a href="{{ route('compte.reset', ["slug" => $souscompte->slug]) }}" class="btn bg-red waves-button waves-effect">Remettre à zéro</a>
                    </div>
                    @endif
                    <div class="col-md-offset-3 col-md-3 col-sm-6">
                        <a target="_blank" href="{{ route("print.souscompte", ["slug" => $souscompte->slug]) }}" class="btn bg-light-green waves-button waves-effect">Imprimer</a>
                    </div>
                </div>
                <div class="row clearfix">
                    <div class="body table-responsive">
                        <table class="table table-bordered table-hover ">
                            <thead>
                            <tr class="bg-green">
                                <th width="3%"></th>
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
                                    <td>
                                        <div class="btn-toolbar" role="toolbar">
                                            <div class="btn-group btn-group-xs" role="group">
                                                <a class="btn bg-orange waves-effect" href="javascript:void(0);" onclick="edit('{{ $ligne->id }}','{{ (new \Carbon\Carbon($ligne->dateecriture))->format('d/m/Y') }}','{{ $ligne->objet }}');" title="Modifier"><i class="material-icons">edit</i></a>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ (new \Carbon\Carbon($ligne->dateaction))->format('d/m/Y H:i:s') }}</td>
                                    <td>{{ (new \Carbon\Carbon($ligne->dateoperation))->format('d/m/Y') }}</td>
                                    <td>{{ $ligne->objet }}</td>
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

<div class="modal fade" id="defaultModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form class="form-line" action="{{ "" }}" method="post">
            <div class="modal-content">
                <div class="modal-header bg-light-green">
                    <h4 class="modal-title" id="defaultModalLabel">Nouveau mouvement de caisse</h4>
                </div>
                <div class="modal-body">
                    {{ csrf_field() }}
                    <div class="row clearfix">
                        <div class="col-md-3 col-xs-12 form-control-label">
                            <label for="dateecriture">Date</label>
                        </div>
                        <div class="col-md-8 col-xs-12 ">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" required name="dateoperation" id="dateoperation" class="datepicker form-control" placeholder="JJ/MM/AAAA" value="{{ old('dateoperation',\Carbon\Carbon::now()->format("d/m/Y")) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row clearfix">
                        <div class="col-md-3 col-xs-12 form-control-label">
                            <label for="sens">Opération</label>
                        </div>
                        <div class="col-md-8 col-xs-12 ">
                            <div class="form-group">
                                <select class="form-control selectpicker" id="sens" name="sens" required>
                                    <option @if(old('sens') == '-1') selected @endif value="-1" >Sortie caisse</option>
                                    @if(\Illuminate\Support\Facades\Auth::user()->authorizes(\App\Service::INFORMATIQUE,
                                        \App\Service::DG, \App\Service::COMPTABILITE) || $souscompte->can_appro)
                                    <option @if(old('sens') == '1') selected @endif value="1" >Approvisionnement</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row clearfix">
                        <div class="col-md-3 col-xs-12 form-control-label">
                            <label for="objet">Objet du mouvement</label>
                        </div>
                        <div class="col-md-8 col-xs-12">
                            <div class="form-group">
                                <input type="text" required name="objet" id="objet" class="form-control" placeholder="Objet du mouvement" value="{{ old('objet') }}">
                            </div>
                        </div>
                    </div>

                    <div class="row clearfix">
                        <div class="col-md-3 col-xs-12 form-control-label">
                            <label for="montant">Montant</label>
                        </div>
                        <div class="col-md-8 col-xs-12">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="number" required name="montant" id="montant" class="form-control" placeholder="Montant du mouvement" value="{{ old('montant') }}">
                                </div>
                                <span class="input-group-addon">F CFA</span>
                            </div>
                        </div>
                    </div>

                    <div class="row clearfix">
                        <div class="col-md-3 col-xs-12 form-control-label">
                            <label for="observation">Observations</label>
                        </div>
                        <div class="col-md-8 col-xs-12">
                            <div class="form-group">
                                <textarea maxlength="255" id="observation" name="observation" class="form-control" placeholder="Note sur le mouvement">{{ old('observation') }}</textarea>
                            </div>
                        </div>
                        <div class="">
                            <input type="hidden" name="compte_id" id="compte_id" value="{{ $souscompte->id }}">
                        </div>
                    </div>

                    <hr/>

                    <div class="row clearfix">
                        <div class="col-lg-offset-2 col-md-offset-2 col-sm-offset-4 col-xs-offset-5">
                            <button type="submit" class="btn btn-primary m-t-15 waves-effect">Ajouter</button>
                            <button type="reset" data-dismiss="modal" class="btn btn-default m-t-15 waves-effect">Annuler</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form class="form-line" action="{{ route("compte.modifier", ["slug" => $souscompte->slug]) }}" method="post">
            <div class="modal-content">
                <div class="modal-header bg-light-green">
                    <h4 class="modal-title" id="editModalLabel">Modification mouvement de caisse</h4>
                </div>
                <div class="modal-body">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="md-id">
                    <div class="row clearfix">
                        <div class="col-md-3 col-xs-12 form-control-label">
                            <label for="md-dateecriture">Date</label>
                        </div>
                        <div class="col-md-8 col-xs-12 ">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" required name="dateoperation" id="md-dateoperation" class="datepicker form-control" placeholder="JJ/MM/AAAA" value="{{ old('dateecriture',\Carbon\Carbon::now()->format("d/m/Y")) }}">
                                </div>
                            </div>
                        </div>

                        <div class="row clearfix">
                            <div class="col-md-3 col-xs-12 form-control-label">
                                <label for="md-objet">Objet du mouvement</label>
                            </div>
                            <div class="col-md-8 col-xs-12">
                                <div class="form-group">
                                    <input type="text" required name="objet" id="md-objet" class="form-control" placeholder="Objet du mouvement" value="{{ old('objet') }}">
                                </div>
                            </div>
                        </div>

                        <hr/>

                        <div class="row clearfix">
                            <div class="col-lg-offset-2 col-md-offset-2 col-sm-offset-4 col-xs-offset-5">
                                <button type="submit" class="btn btn-primary m-t-15 waves-effect">Modifier</button>
                                <button type="reset" data-dismiss="modal" class="btn btn-default m-t-15 waves-effect">Annuler</button>
                            </div>
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

        function edit(id, dateEcriture, objet) {
            $('#md-id').val(id);
            $('#md-dateoperation').val(dateEcriture);
            $('#md-objet').val(objet);
            $('#editModal').modal('show');
        }

    </script>
@endsection