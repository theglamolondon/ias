@extends("layouts.main")
@section("content")
<div class="container-fluid">
    <!-- Basic Table -->
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <div class="row clearfix">
                        <div class="col-md-12">
                            <h2><b>Liste des produits</b></h2>
                            <br/>
                        </div>
                    </div>
                    <div class="row clearfix">
                        <div class="col-md-8">
                            <form method="get">
                                <div class="row clearfix">
                                    <div class="col-md-4 col-sm-6">
                                        <b>Familles</b>
                                        <select class="form-control selectpicker" id="famille" name="famille" data-live-search="true" required>
                                            <option value="all">Toutes les familles</option>
                                            @foreach($familles as $famille)
                                                <option @if($famille->id == request()->query("famille")) selected @endif value="{{ $famille->id }}">{{ $famille->libelle }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <b>Référence ou nom de produit</b>
                                        <div class="input-group">
                                            <div class="form-line">
                                                <input name="keyword" type="text" class="form-control" placeholder="Reference ou produit" value="{{ request()->query("keyword") }}">
                                            </div>
                                            <span class="input-group-addon">
                                            <i class="material-icons">search</i>
                                        </span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <br/>
                                        <button class="btn bg-teal waves-button waves-effect" type="submit">Rechercher</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-2 col-xs-12">
                            <div class="align-right">
                                <a href="{{ route('stock.produit.ajouter') }}" class="btn bg-blue waves-effect">Ajouter un produit</a>
                            </div>
                        </div>
                        <div class="col-md-2 col-xs-12">
                            <div class="align-right">
                                <a href="{{ route('print.produits') }}" target="_blank" class="btn bg-grey waves-effect">Imprimer</a>
                            </div>
                        </div>
                    </div>

                    <br class="clearfix"/>
                </div>
                <div class="body table-responsive">
                    <table class="table table-bordered table-hover ">
                        <thead>
                        <tr class="bg-green">
                            <th width="7%"></th>
                            <th>REFERENCE</th>
                            <th>FAMILLE</th>
                            <th>LIBELLE</th>
                            <th>STOCK</th>
                            <th>PRIX UNITAIRE</th>
                        </tr>
                        </thead>
                        <tbody class="table-hover">
                        @foreach($produits as $produit)
                        <tr>
                            <td>
                                <div class="btn-toolbar" role="toolbar">
                                    <div class="btn-group btn-group-xs" role="group">
                                        <a class="btn bg-blue-grey waves-effect" href="{{ route("stock.produit.modifier", ["reference" => $produit->reference]) }}" title="Modifier le produit"><i class="material-icons">edit</i></a>
                                        <a class="btn bg-red waves-effect" onclick="return confirm('Voulez-vous vraiment supprimer leprroduit ?')" href="{{ route("stock.produit.supprimer", ["reference" => $produit->reference]) }}" title="Supprimer le produit"><i class="material-icons">delete</i></a>
                                    </div>
                                </div>
                            </td>
                            <td>{!! str_replace(strtoupper(request()->query("keyword")),'<span class="bg-teal">'.request()->query("keyword").'</span>' ,strtoupper($produit->reference) ) !!}</td>
                            <td>{{ $produit->famille->libelle }}</td>
                            <td>{!! str_replace(strtoupper(request()->query("keyword")),'<span class="bg-teal">'.request()->query("keyword").'</span>' ,strtoupper($produit->libelle) ) !!}</td>
                            <td class="text-right">{{ number_format($produit->stock,0,","," ") }}</td>
                            <td class="text-right">{{ number_format($produit->prixunitaire,0,","," ") }} F CFA</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @if(request()->query("keyword"))
                        {{ $produits->appends(["keyword" => request()->query("keyword")])->links('vendor.pagination.default') }}
                    @else
                        {{ $produits->appends(request()->except([]))->links('vendor.pagination.default') }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection