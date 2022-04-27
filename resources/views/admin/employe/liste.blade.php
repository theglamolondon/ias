@extends('layouts.main')

@section('content')
    <div class="container-fluid">
        <!-- Basic Table -->
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header">
                        <div class="col-md-4">
                            <h2>
                                Liste des employés
                            </h2>
                        </div>
                        <div class="col-md-4 col-xs-12">
                            <div class="align-right">
                                <div class="btn-toolbar">
                                    <div class="btn-group">
                                        <a href="" class="btn bg-light-green waves-effect"><i class="material-icons">person_add</i> Bulletins</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-xs-12">
                            <div class="align-right">
                                <div class="btn-toolbar">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.employe.ajouter') }}" class="btn bg-blue waves-effect"><i class="material-icons">person_add</i> Ajouter un employé </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br class="clearfix"/>
                    </div>
                    <div class="body table-responsive">
                        <table class="table table-bordered table-hover ">
                            <thead>
                            <tr class="bg-green">
                                <th width="7.5%"></th>
                                <th>Matricule</th>
                                <th>Nom & prénoms</th>
                                <th>Date de naissance</th>
                                <th>Date d'embauche</th>
                                <th>Pièce d'identité</th>
                                <th>Service</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($employes as $employe)
                            <tr>
                                <td>
                                    <div class="btn-toolbar" role="toolbar">
                                        <div class="btn-group btn-group-xs" role="group">
                                            <a class="btn bg-blue-grey waves-effect" href="{{ route("admin.employe.fiche", ["matricule" => $employe->matricule]) }}" title="Fiche d'employé"><i class="material-icons">person_outline</i></a>
                                            <a class="btn bg-orange waves-effect" href="{{ route("admin.employe.modifier", ["matricule" => $employe->matricule]) }}" title="Modifier"><i class="material-icons">mode_edit</i></a>
                                            <!-- <a class="btn bg-light-green waves-effect" href="{{ route("rh.paie", ["annee" => date('Y'), "mois" => date('m')]) }}" title="Salaire"><i class="material-icons">assignment</i></a> -->
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $employe->matricule }}</td>
                                <td>{{ $employe->nom }} {{ $employe->prenoms }}</td>
                                <td>{{ (new \Carbon\Carbon($employe->datenaissance))->format('d/m/Y') }}</td>
                                <td>{{ (new \Carbon\Carbon($employe->dateembauche))->format('d/m/Y') }}</td>
                                <td>{{ $employe->pieceidentite }}</td>
                                <td>{{ $employe->service->libelle }}</td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>

                        {{ $employes->appends(request()->except([]))->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection