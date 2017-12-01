@extends("layouts.main")
@section("content")
    <div class="container-fluid">
        <!-- Basic Table -->
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header">
                        <div class="col-md-4">
                            <h2>
                                Liste des utilisateurs
                            </h2>
                        </div>
                        <div class="col-md-4">
                        </div>
                        <div class="col-md-4 col-xs-12">
                            <div class="align-right">
                                <div class="btn-toolbar">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.utilisateur.ajouter') }}" class="btn bg-blue waves-effect"><i class="material-icons">person_add</i> Ajouter un utilisateur </a>
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
                                <th>Employé</th>
                                <th>Service</th>
                                <th>Login</th>
                                <th>Statut</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($utilisateurs as $utilisateur)
                                <tr>
                                    <td></td>
                                    <td><a href="{{ route('admin.employe.fiche', ["matricule" => $utilisateur->employe->matricule]) }}" >{{ $utilisateur->employe->nom }} {{ $utilisateur->employe->prenoms }}</a></td>
                                    <td>{{ $utilisateur->employe->service->libelle }}</td>
                                    <td>{{ $utilisateur->login }}</td>
                                    <td>{{ \Illuminate\Support\Facades\Lang::get("message.statut.".$utilisateur->satut) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection