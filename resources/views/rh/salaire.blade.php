@extends('layouts.main')
@section('content')
<div class="container-fluid">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>Salaire mensuel</h2>
            </div>
            <div class="body">
                <form class="form-horizontal" method="post" >
                    {{ csrf_field() }}
                    <div class="row clearfix">
                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                            <label for="annee">Année</label>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-8 col-xs-7">
                            <div class="form-group">
                                <select name="annee" id="annee" class="form-control input-field">
                                    @foreach($annees as $annee)
                                        <option value="{{ $annee }}" @if(old('annee', \Carbon\Carbon::now()->year) == $annee) selected @endif> {{ $annee }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                            <label for="mois">Mois</label>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-8 col-xs-7">
                            <div class="form-group">
                                <select name="mois" id="mois" class="form-control input-field">
                                    @foreach($months as $mois)
                                        <option value="{{ $mois->id }}" @if(old('mois', \Carbon\Carbon::now()->month) == $mois->id) selected @endif> {{ $mois->libelle }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr />

                    <div class="row clearfix">
                        <div class="col-lg-offset-2 col-md-offset-2 col-sm-offset-4 col-xs-offset-5">
                            <button type="submit" class="btn btn-lg bg-green m-t-15 waves-effect">Démarrer</button>
                            <button type="reset" class="btn btn-lg btn-default m-t-15 waves-effect">Annuler</button>

                            @if(request()->has('state') && request()->query('state') == \App\Salaire::ETAT_DEMARRE)
                            <a type="reset" href="{{ route('rh.paie',["annee" => old('annee'), "mois"=>old('mois') ,"page" => request()->query("target")]) }}" class="btn btn-lg btn-warning m-t-15 waves-effect">Continuer</a>
                            @endif

                            @if(request()->has('state') && (request()->query('state') == \App\Salaire::ETAT_DEMARRE || request()->query('state') == \App\Salaire::ETAT_VALIDE))
                            <a type="reset" href="{{ route('rh.salaire.reset', ["annee" => old('annee'), "mois"=>old('mois')]) }}" class="btn btn-lg btn-danger m-t-15 waves-effect" onclick="return confirm('Voulez-vous vraiment recommencer les salaires ?')">Recommencer</a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="header">
                <h2>Récapitulatifs</h2>
            </div>
            <div class="body">
                <div class="body table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr class="bg-blue-grey">
                            <th width="20%">Actions</th>
                            <th width="20%">Annéé</th>
                            <th width="20%">Mois</th>
                            <th width="40%">Statut</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($salaires as $salaire)
                        <tr>
                            <td>
                                <div class="btn-toolbar" role="toolbar">
                                    <div class="btn-group btn-group-xs" role="group">
                                        <a href="#" class="btn bg-blue waves-effect" title="Consulter les salaires du mois"><i class="material-icons">description</i></a>
                                        @if($salaire->statut == \App\Salaire::ETAT_VALIDE)
                                        <a href="{{ route("rh.salaire.confirm",["annee"=> $salaire->annee, "mois"=>$salaire->mois]) }}" class="btn bg-green waves-effect" title="Clôturer les salaires du mois"><i class="material-icons">check</i></a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $salaire->annee }}</td>
                            <td>{{ App\Http\Controllers\Web\RH\SalaireController::getMonthsById($salaire->mois) }}</td>
                            <td>{{ \App\Salaire::getStateToString($salaire->statut) }}</td>
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