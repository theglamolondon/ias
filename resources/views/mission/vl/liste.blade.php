@extends("layouts.main")
@section("link")
    <!-- Bootstrap Material Datetime Picker Css -->
    <link href="{{ asset('plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}" rel="stylesheet" />
@endsection
@section("content")
<div class="container-fluid">
    <!-- Basic Table -->
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
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
                                        <input name="debut" type="text" class="form-control datepicker" placeholder="Ex: 30/07/2016" value="{{ $debut }}">
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
                                        <input name="fin" type="text" class="form-control datepicker" placeholder="Ex: 30/07/2016" value="{{ $fin }}">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2 col-sm-6">
                                <b>Chauffeurs</b>
                                <div class="input-group">
                                    <div class="form-line">
                                        <select class="form-control selectpicker" id="chauffeur" name="chauffeur" data-live-search="true" required>
                                            <option value="#">Tous les chauffeurs</option>
                                            @foreach($chauffeurs as $chauffeur)
                                                <option value="{{ $chauffeur->employe_id }}">{{ $chauffeur->employe->nom }} {{ $chauffeur->employe->prenoms }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2 col-sm-6">
                                <b>Etat</b>
                                <div class="input-group">
                                    <div class="form-line">
                                        <select class="form-control selectpicker" id="etat" name="etat" required>
                                            <option value="#">Tous les états</option>
                                            @foreach($status as $k => $v)
                                                <option value="{{ $k }}">{{ $v }}</option>
                                            @endforeach
                                        </select>
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
            </div>
            <div class="card">
                <div class="header">
                    <div class="col-md-4">
                        <h2>Mission VL</h2>
                    </div>
                    <div class="col-md-4">

                    </div>
                    <div class="col-md-4 col-xs-12">
                        <div class="align-right">
                            <a href="{{ route("mission.nouvelle") }}" class="btn bg-blue waves-effect">Nouvelle mission VL</a>
                        </div>
                    </div>
                    <br class="clearfix"/>
                </div>
                <div class="body table-responsive">
                    <table class="table table-bordered table-hover ">
                        <thead>
                        <tr class="bg-green">
                            <th width=""></th>
                            <th width="7%">Début effectif (programmé)</th>
                            <th width="7%">Fin effective (programée)</th>
                            <th width="4%">Durée</th>
                            <th width="12%">Client</th>
                            <th width="13%">Destination</th>
                            <th width="8%">Etat</th>
                            <th width="12%">Chauffeur</th>
                            <th width="6%">Véhicule</th>
                            <th width="10%">Montant total</th>
                            <th width="10%">Ref facture</th>
                        </tr>
                        </thead>
                        <tbody class="table-hover">
                        @foreach($missions as $mission)
                            <tr>
                                <td scope="row">
                                    <div class="btn-toolbar" role="toolbar">
                                        <div class="btn-group btn-group-xs" role="group">
                                            @if($mission->status == \App\Statut::MISSION_COMMANDEE && empty($mission->piececomptable_id) )
                                                <a class="btn bg-green waves-effect" href="{{ route("mission.modifier", ["reference" => $mission->code]) }}" title="Modifier la mission"><i class="material-icons">edit</i></a>
                                            @endif
                                            <a class="btn bg-orange waves-effect" href="{{ route("mission.details", ["reference" => $mission->code]) }}" title="Fiche de mission"><i class="material-icons">insert_drive_file</i></a>
                                            @if($mission->status == \App\Statut::MISSION_COMMANDEE && empty($mission->piececomptable_id) )
                                                <a class="btn bg-red waves-effect" href="#" title="Supprimer la mission"><i class="material-icons">delete</i></a>
                                            @endif
                                            @if($mission->status != \App\Statut::MISSION_TERMINEE_SOLDEE)
                                                <a class="btn bg-teal waves-effect" href="{{ route("versement.mission.ajouter", ["code"=>$mission->code]) }}" title="Effectuer un versement"><i class="material-icons">monetization_on</i></a>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td valign="center">
                                    {{ (new Carbon\Carbon($mission->debuteffectif))->format("d/m/Y") }}
                                    ({{ (new Carbon\Carbon($mission->debutprogramme))->format("d/m/Y") }})
                                </td>
                                <td>
                                    {{ (new Carbon\Carbon($mission->fineffective))->format("d/m/Y") }}
                                    ({{ (new Carbon\Carbon($mission->finprogramme))->format("d/m/Y") }})
                                </td>
                                <td>{{ $mission->getDuree() }} j</td>
                                <td>{{ $mission->clientPartenaire->raisonsociale }}</td>
                                <td>{{ $mission->destination }}</td>
                                <td>{{ \App\Statut::getStatut($mission->status) }}</td>
                                <td>{{ $mission->chauffeur ? $mission->chauffeur->employe->nom : 'Chauffeur de sous traitance'}} {{ $mission->chauffeur ? $mission->chauffeur->employe->prenoms : ""}}</td>
                                <td>{{ $mission->soustraite ? $mission->immat_soustraitance : $mission->vehicule->immatriculation }}</td>
                                <td class="amount">{{ number_format($mission->montantjour * $mission->getDuree(),0,","," ") }}</td>
                                <td><a href="{{ $mission->pieceComptable ? route("facturation.details", ["reference" => $mission->pieceComptable->getReference() ]) : "javascript:void(0);" }}" >{{ $mission->pieceComptable ? $mission->pieceComptable->getReference() : ""}}</a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{ $missions->appends(request()->except([]))->links('vendor.pagination.default') }}
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
</script>
@endsection