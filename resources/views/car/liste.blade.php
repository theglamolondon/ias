@extends('layouts.main')

@section('content')
<div class="container-fluid">
    <!-- Basic Table -->
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <div class="row clearfix">
                        <div class="col-md-4">
                            <h2>Liste des véhicules {{$titre}}</h2>
                        </div>
                    </div>
                    <div class="row clearfix">
                        <form method="get">
                            <div class="col-md-6">
                                <b>Immatriculation</b>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="material-icons">directions_car</i>
                                        <i class="material-icons">search</i>
                                    </span>
                                    <div class="form-line">
                                        <input name="search" type="text" class="form-control" placeholder="N° d'immatriculation ou marque ou modèle" value="{{ old("search", request()->query('search')) }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <br/>
                                <button class="btn bg-teal waves-button waves-effect" type="submit">Rechercher</button>
                            </div>
                        </form>
                        <div class="col-md-4 col-xs-12">
                            <div class="align-right">
                                <a href="{{ route('vehicule.nouveau') }}" class="btn bg-blue waves-effect">Ajouter un véhicule</a>
                                <a target="_blank" href="{{ route('print.vehicule') }}" class="btn bg-amber waves-effect">Imprimer</a>
                            </div>
                        </div>
                        <br class="clearfix"/>
                    </div>

                </div>
                <div class="body table-responsive">
                    <table class="table table-bordered table-hover ">
                        <thead>
                        <tr class="bg-green">
                            <th width="7.5%"></th>
                            <th width="10%">IMMATRICULATION</th>
                            <th>MARQUE</th>
                            <th>MODELE</th>
                            <th>GENRE</th>
                            <th>EXP VISITE</th>
                            <th>EXP ASSUR</th>
                            <th>Statut</th>
                            <th width="4%">PLACES</th>
                        </tr>
                        </thead>
                        <tbody class="table-hover">
                        @foreach($vehicules as $vehicule)
                        <tr>
                            <td scope="row">
                                <div class="btn-toolbar" role="toolbar">
                                    <div class="btn-group btn-group-xs" role="group">
                                        <a class="btn bg-blue-grey waves-effect" href="{{ route("mission.nouvelle",[ 'vehicule' => $vehicule->immatriculation ]) }}" title="Démarrer une mission"><i class="material-icons">directions_car</i></a>
                                        <a class="btn bg-green waves-effect" href="{{ route('vehicule.modifier',["immatriculation" => $vehicule->immatriculation]) }}" title="Modifier le véhicule"><i class="material-icons">edit</i></a>
                                        <a class="btn bg-orange waves-effect" href="{{ route("vehicule.details", ["immatriculation" => $vehicule->immatriculation]) }}" title="Consulter le rapport du véhicule"><i class="material-icons">insert_drive_file</i></a>
                                        <a class="btn bg-light-blue waves-effect" href="{{ route("reparation.nouvelle", ["vehicule" => $vehicule->immatriculation ]) }}" title="Démarrer une intervention"><i class="material-icons">settings</i></a>
                                    </div>
                                </div>
                            </td>
                            <td valign="center">{{ $vehicule->immatriculation }}</td>
                            <td>{{ $vehicule->marque }}</td>
                            <td>{{ $vehicule->typecommercial }}</td>
                            <td>{{ $vehicule->genre->libelle }}</td>
                            <td>{{ (new \Carbon\Carbon($vehicule->visite))->format('d/m/Y') }}</td>
                            <td>{{ (new \Carbon\Carbon($vehicule->assurance))->format('d/m/Y') }}</td>
                            <td>{{ \App\Statut::getStatut($vehicule->status) }}</td>
                            <td>{{ $vehicule->nbreplace }}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{ $vehicules->appends(request()->except([]))->links('vendor.pagination.default') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection