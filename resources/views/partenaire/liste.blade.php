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
                            <h2>Liste des partenaires</h2>
                        </div>
                    </div>
                    <div class="row clearfix">
                        <form method="get">
                            <div class="col-md-5 col-xs-12">
                                <b>Raison sociale</b>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="material-icons">domain</i>
                                        <i class="material-icons">search</i>
                                    </span>
                                    <div class="form-line">
                                        <input name="raisonsociale" type="text" class="form-control" placeholder="Raison sociale" value="{{ old("raisonsociale", request()->query('raisonsociale')) }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <br/>
                                <button class="btn bg-teal waves-button waves-effect" type="submit">Rechercher</button>
                            </div>
                        </form>
                        <div class="col-md-offset-2 col-md-4 col-sm-6 col-xs-12">
                            <div class="align-right">
                                <a href="{{ route('partenaire.fournisseur.new') }}" class="btn bg-blue-grey waves-effect">Facture fournisseur</a>

                                <a href="{{ route('partenaire.nouveau') }}" class="btn bg-blue waves-effect">Ajouter un partenaire</a>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="body table-responsive">
                    <table class="table table-bordered table-hover ">
                        <thead>
                        <tr class="bg-green">
                            <th></th>
                            <th>RAISON SOCIALE</th>
                            <th>COMPTE CONTRIBUABLE</th>
                            <th>CONTACT</th>
                        </tr>
                        </thead>
                        <tbody class="table-hover">
                        @foreach($partenaires as $partenaire)
                            <tr>
                                <th scope="row">
                                    <div class="btn-toolbar" role="toolbar">
                                        <div class="btn-group btn-group-xs" role="group">
                                            <!--
                                            <a class="btn bg-blue-grey waves-effect" href="{{ '#' }}" title="Démarrer une mission"><i class="material-icons">directions_car</i></a>
                                            -->
                                            <a class="btn bg-green waves-effect" href="{{ route("partenaire.modifier", ["id" => $partenaire->id]) }}" title="Modifier"><i class="material-icons">edit</i></a>
                                            @if($partenaire->isclient)
                                                <a class="btn bg-orange waves-effect" href="{{ route('partenaire.client', ['id' => $partenaire->id]) }}" title="Consulter le point client"><i class="material-icons">insert_drive_file</i></a>
                                            @endif
                                            @if($partenaire->isfournisseur)
                                                <a class="btn bg-indigo waves-effect" href="{{ route('partenaire.fournisseur', ['id' => $partenaire->id]) }}" title="Consulter le point fournisseur"><i class="material-icons">insert_drive_file</i></a>
                                            @endif
                                        </div>
                                    </div>
                                </th>
                                <th valign="center">{{ $partenaire->raisonsociale }}</th>
                                <td>{{ $partenaire->comptecontribuable }}</td>
                                <td>{{ $partenaire->contactString() }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    {{ $partenaires->appends(request()->except([]))->links('vendor.pagination.default') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection