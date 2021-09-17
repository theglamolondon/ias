@extends("layouts.main")

@section("link")
<!-- Bootstrap Select Css -->
<link href="{{ asset('plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet" />
<!-- Bootstrap Material Datetime Picker Css -->
<link href="{{ asset('plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}" rel="stylesheet" />

<!-- Sweetalert Css -->
<link href="{{ asset('plugins/sweetalert/sweetalert.css') }}" rel="stylesheet" />
@endsection

@section("content")
<div class="container-fluid">
    <!-- Basic Table -->
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <div class="col-md-12">
                        <h2> @if($updateUrl != null ) Modification @endif Facture Pro forma  @if(request()->query("from") == \App\Metier\Behavior\Notifications::CREATE_FROM_PROFORMA )(copié de la pro forma n° {{$proforma->referenceproforma}} ) @endif</h2>
                    </div>
                </div>
                <div class="body table-responsive">
                    <div class="row clearfix">
                        <div class="col-lg-1 col-md-1 col-sm-4 col-xs-5 form-control-label">
                            <label for="objet">Objet</label>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-8 col-xs-7">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" required name="objet" id="objet" class="form-control" value="{{ $proforma->objet ?? null }}" maxlength="150" placeholder="Objet de la facture">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-1 col-md-1 col-sm-4 col-xs-5 form-control-label">
                            <label for="client">Client</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-8 col-xs-7">
                            <div class="form-group">
                                <select class="form-control selectpicker" id="client" name="client" data-live-search="true" required>
                                    @foreach($partenaires as $partenaire)
                                        <option @if(($proforma->partenaire_id ?? null) ==  $partenaire->id) selected @endif value="{{ $partenaire->id }}">{{ $partenaire->raisonsociale }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-1 col-md-1 col-sm-4 col-xs-5 form-control-label">
                            <label for="creationbc">Date</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-8 col-xs-7">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" required name="creationbc" id="creationbc" class="datepicker form-control" placeholder="JJ/MM/AAAA" value="{{(new Carbon\Carbon($proforma->creationproforma ?? null))->format('d/m/Y') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row clearfix">
                        <div class="col-lg-1 col-md-1 col-sm-4 col-xs-5 form-control-label">
                            <label for="client">Produits</label>
                        </div>
                        <div class="col-lg-7 col-md-7 col-sm-8 col-xs-7">
                            <div class="form-group">
                                <select class="form-control selectpicker" id="produits" name="produits" data-live-search="true" required>
                                    <option >Veuillez sélectionner votre article SVP</option>
                                    @foreach($commercializables as $commercializable)
                                        <option value="{{ $commercializable->id }}" data-stock="{{ $commercializable->stock ?? 0 }}" data-modele="{{ $commercializable->getRealModele() }}" data-id="{{ $commercializable->getId() }}" data-price="{{ $commercializable->getPrice() }}" data-libelle="{!! $commercializable->detailsForCommande() !!}" data-reference="{{ $commercializable->getReference() }}">{{ $commercializable->getReference() }} {!! $commercializable->detailsForCommande() !!}</option>
                                    @endforeach
                                </select>
                                <button id="addProduct" title="Ajouter un produit" class="btn bg-teal btn-circle waves-effect waves-circle waves-float"><i class="material-icons">add</i> </button>
                                <span id="infoProduct" class="new badge blue"></span>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <input style="position: initial;left: 0;opacity: 1;height: 20px;width: 18px;" type="checkbox" name="activeDispo" id="activeDispo">
                            <span>Activer la disponibilité</span>
                            <div id="divDispo" class="hidden">
                                <div class="form-group">
                                    <b>Disponibilité</b> <br/>
                                    <select class="form-control" id="disponibilite">
                                        <option>Non disponible</option>
                                        <option>Disponible</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <b>Période de disponibilité</b>
                                    <div class="form-line">
                                        <input type="text" name="periodeDispo" class="form-control" value="" id="periodeDispo">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row clearfix">
                        <div class="col-lg-1 col-md-1 col-sm-4 col-xs-5 form-control-label">
                            <label for="price">Prix</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-8 col-xs-7">
                            <div class="input-group">
                                <div class="form-line">
                                    <input name="price" id="price" type="number" class="form-control">
                                </div>
                                <span class="input-group-addon">F CFA</span>
                            </div>
                        </div>

                        <div class="col-lg-1 col-md-1 col-sm-4 col-xs-5 form-control-label">
                            <label for="remise">Remise</label>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-8 col-xs-7">
                            <div class="input-group">
                                <div class="form-line">
                                    <input name="remise" id="remise" type="number" value="0" class="form-control">
                                </div>
                                <span class="input-group-addon">%</span>
                            </div>
                        </div>

                        <div class="col-lg-1 col-md-1 col-sm-4 col-xs-5 form-control-label">
                            <label for="quantity">Quantité</label>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-8 col-xs-7">
                            <div class="form-group">
                                <div class="form-line">
                                    <input name="quantity" id="quantity" type="number" class="form-control" value="1">
                                </div>
                            </div>
                        </div>

                        @if(request()->has("type") && request()->get("type") == \App\PieceComptable::TYPE_FACTURE_MISSION )
                        <div class="col-lg-1 col-md-1 col-sm-4 col-xs-5 form-control-label">
                            <label for="quantity">Période </label>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-8 col-xs-7">
                            <div class="form-group">
                                <div class="form-line">
                                    <input name="quantite_periode" id="quantite_periode" type="number" class="form-control" value="1">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-8 col-xs-7">
                            <div class="form-group">
                                <div class="form-line">
                                    <select name="periode" id="periode" class="form-control">
                                        <option selected>Jours</option>
                                        <option>Mois</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        @endif
                        <button type="button" id="btnajouter" class="btn btn-primary m-t-15 waves-effect">Ajouter</button>
                    </div>

                    <div class="row clearfix">
                        <div class="col-lg-1 col-md-1 col-sm-4 col-xs-5 form-control-label">
                            <label for="client">Motif</label>
                        </div>
                        <div class="col-lg-10 col-md-10 col-sm-8 col-xs-7">
                            <div class="form-group">
                                <div class="form-line">
                                    <textarea required name="complement" id="complement" class="form-control" placeholder="Compléments de la ligne" value="">{{ old('complement') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr/>

                    <table id="piece" class="table table-bordered">
                        <thead>
                        <tr class="bg-teal">
                            <th width="5%">#</th>
                            <th width="10%">Référence</th>
                            <th>Description</th>
                            <th class="text-right" width="8%">Quantité</th>
                            @if(request()->has("type") && request()->get("type") == \App\PieceComptable::TYPE_FACTURE_MISSION || (isset($proforma) && $proforma->type_piece == \App\PieceComptable::TYPE_FACTURE_MISSION ))
                            <th class="text-right" colspan="2" width="10%">Période</th>
                            @endif
                            <th class="text-right" width="10%">Prix Unitaire</th>
                            <th class="text-right" width="6%">Remise %</th>
                            <th class="text-right" width="15%">Montant HT</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($lignes as $ligne)
                        <tr>
                            <th data-modele="{{ $ligne->getRealModele() }}" data-id="{{ $ligne->getId() }}"><a class="delete" href="javascript:void(0);"><i class="material-icons">delete_forever</i> </a></th>
                            <td>{{ $ligne->getReference() }}</td>
                            <td>{{ $ligne->detailsForCommande() }}</td>
                            <td><input type="number" class="form-control quantite" value="{{ $ligne->getQuantity() }}"></td>
                            @if(request()->has("type") && request()->get("type") == \App\PieceComptable::TYPE_FACTURE_MISSION || $proforma->type_piece == \App\PieceComptable::TYPE_FACTURE_MISSION)
                            <td><input type="number" class="form-control periode" value="{{ $ligne->getPeriodeQuantity() }}"></td>
                            <td>{{ $ligne->getPeriode() }}</td>
                            @endif
                            <td class="price text-right">{{ $ligne->getPrice() }}</td>
                            <td class="remise text-right">{{ $ligne->getRemise() * 100 }}</td>
                            <td class="amount text-right">{{ ($ligne->getPrice() * $ligne->getQuantity()) - ceil($ligne->getRemise() * $ligne->getPrice() * $ligne->getQuantity()) }}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>


                    <div class="row clearfix">
                        <div class="col-md-offset-9 col-md-3">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Montant HT</th>
                                    <th id="montantHT">0</th>
                                </tr>
                                <tr>
                                    <td>
                                        <label id="exonerelabel">TVA 18% </label> <br/>
                                        <input type="checkbox" id="isexonere" class="form-control"/>
                                        <label for="isexonere" class="">Exonéré</label>
                                    </td>
                                    <td id="montantTVA">0</td>
                                </tr>
                                <tr>
                                    <th>Montant TTC</th>
                                    <th id="montantTTC">0</th>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr/>

                    <div class="row clearfix">
                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                            <label for="delailivraison">Délai de livraison</label>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-8 col-xs-7">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" required name="delailivraison" id="delailivraison" class="form-control" value="{{ $proforma->delailivraison ?? null }}" maxlength="255" placeholder="Délai de livraison de la commande">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row clearfix">
                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                            <label for="conditions">Conditions de paiement</label>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-8 col-xs-7">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" required name="conditions" id="conditions" class="form-control" value="{{ $proforma->conditions ?? null }}" maxlength="255" placeholder="Conditions de paiement de la commande">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row clearfix">
                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                            <label for="validite">Validité de l'offre</label>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-8 col-xs-7">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" required name="validite" id="validite" class="form-control" value="{{ $proforma->validite ?? null }}" maxlength="255" placeholder="Validite de l'offre">
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr/>

                    <div class="row clearfix">
                        <div class="col-lg-offset-2 col-md-offset-2 col-sm-offset-4 col-xs-offset-5">
                            <input type="hidden" name="IDfacture" id="IDfacture" value="{{ $proforma->id ?? 0 }}">
                            <button type="button"id="validOrder" class="btn btn-primary m-t-15 waves-effect">Enregistrer</button>
                            <button type="reset" class="btn btn-default m-t-15 waves-effect">Retour</button>
                        </div>
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
<!-- <script type="text/javascript"  src="{{ asset('plugins/momentjs/moment-with-locales.min.js') }}"></script> -->

<!-- Bootstrap Material Datetime Picker Plugin Js -->
<script src="{{ asset('plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js' )}}"></script>

<!-- Multi Select Plugin Js -->
<script src="{{ asset('plugins/bootstrap-select/js/bootstrap-select.js') }}"></script>

<!-- SweetAlert Plugin Js -->
<script src="{{ asset('plugins/sweetalert/sweetalert.min.js') }}"></script>

<script type="text/javascript" >

    var TYPE_PROFORMA_LOCATION = $("#quantite_periode")[0] !== undefined;

    var TD = null;
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

    var facture = {
        lines : [],
        isexonere: null,
        montantht: null,
        conditions: null,
        validite: null,
        delailivraison: null,
        objet: null,
        partenaire_id: null,
        id: null,
        _token: '{{ csrf_token() }}'
    };

    $("#validOrder").click(function (e) {
        swal({
            title: "Enregistrement de pro forma",
            text: "Voulez-vous ajouter cette proforma",
            type: "info",
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true
        }, function () {
            saveProForma();
        });
    });

    function saveProForma() {
        var $lines = $("#piece tbody tr");
        var dataString = "[";

        if($lines.length !== 0)
        {
            facture.lines = [];
            $.each($lines, function (key, value) { facture.lines.push(getJsonFromTr(value)); });
        }

        facture.isexonere = ($("#isexonere").is(":checked") ? 1 : 0);
        facture.montantht = $("#montantHT").text();
        facture.conditions = $("#conditions").val();
        facture.validite = $("#validite").val();
        facture.delailivraison = $("#delailivraison").val();
        facture.objet = $("#objet").val();
        facture.partenaire_id = $("#client").val();
        facture.id = $("#IDfacture").val();
        facture.type_piece = TYPE_PROFORMA_LOCATION ? "{{ \App\PieceComptable::TYPE_FACTURE_MISSION }}" : "{{ \App\PieceComptable::TYPE_FACTURE_PIECE }}";
        //console.log(facture);
        sendDataPost(facture);
    }

    function sendDataPost(data){
        $.ajax({
            type:'post',
            url: '{{ $updateUrl ? route("facturation.proforma.modifier") : route("facturation.proforma.nouvelle")  }}',
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            traditional: true,
            success: function (data, status, xhr) {
                console.log(data);
                if(data.code === 0)
                {
                    swal("Echec d'enregistrement !", data.message, "error");
                    console.log(data);
                }else{
                    swal("Pro forma enregistré!",data.message,"success");
                    document.location.href = data.action;
                }
            },
            error : function (data, status, xhr) {
                var $msg = '';
                for(var obj in data.responseJSON ){
                    $msg += data.responseJSON[obj][0]+'<br/>';
                }

                var data ={
                    title: $msg,
                    type: "error",
                    html: $msg
                };
                swal(data);
            }
        });
    }
    
    function getJsonFromTr(tr) {
        var $td = $(tr).children();
        var index = 0;
        return {
            id: 0,
            reference: $($td[++index]).text(),
            designation: $($td[++index]).text(),
            quantite: $($($td[++index]).children("input")).val(),
            quantite_periode: TYPE_PROFORMA_LOCATION ? $($($td[++index]).children("input")).val() : 0,
            periode: TYPE_PROFORMA_LOCATION ? $($td[++index]).text() : "",
            prixunitaire: $($td[++index]).text(),
            remise: parseFloat($($td[++index]).text())/100,
            modele: $($td[0]).data("modele"),
            modele_id: $($td[0]).data("id")
        };
    }
    
    /**
     * FONCTIONS POUR LE TABLEAU
     *
     * */
    $("#produits").change(function (e) {
        $("#price").val($("#produits option:selected").data("price")) ;
        var stock = $("#produits option:selected").data("stock");
        if(stock != 'no'){
            $("#infoProduct").text( stock +" produit(s) en stock");
            if(parseInt(stock) == 0){
                $("#activeDispo").prop("ckecked",true);
            }
        }
    });

    $("#activeDispo").click(function (e) {
        if($(e.target).is(":checked")){
            $("#divDispo").removeClass("hidden");
        }else{
            $("#divDispo").addClass("hidden");
        }
    });

    $("#btnajouter").click(function (e) {
        $q = $("#quantity");
        $p = $("#price");

        if($q.val() === "" || $q.val() <= 0 || $p.val() === "" || $p.val() <= 0 ){
            swal("Erreur","Veuillez saisir la quantité et le prix de l'article SVP","warning");
            return null;
        }

        addLine();
        calculAmount();

        $q.val(1);
        $("#remise").val(0.0);
    });

    $('#isexonere').click(function (e) {
        if($(e.target).is(":checked"))
            $("#exonerelabel").text("TVA 18% (Exonéré de TVA)");
        else
            $("#exonerelabel").text("TVA 18%");

        //MAJ des montants
        calculAmount();
    });

    $(document).on("click", ".delete", function (e) {
        delLine(e.target);
    });

    $(document).on("change", ".quantite", function (e) {
        editQantite(e.target);
    });

    $(document).on("change", ".periode", function (e) {
        editPeriode(e.target);
    });

    function addLine() {
        $product = $("#produits option:selected");
        $quantity = $('#quantity').val();

        var id = $product.data('id') == undefined ? 0 : $product.data('id');
        var modele = $product.data("modele") == undefined ? '{{ \App\Mission::class }}' : $product.data("modele");
        var libelle = getLibelle();
        var qtePeriode = TYPE_PROFORMA_LOCATION ? $("#quantite_periode").val() : 1;
        var prix = $("#price").val();
        var reference = $product.data("reference") == undefined ? '#' : $product.data("reference");
        var remise = parseFloat($("#remise").val() != NaN ? $("#remise").val() : 0);
        var amount = (parseInt(prix) * parseInt(qtePeriode) * parseInt($quantity)) * (1 - (remise/100));

        var complementPeriode = "";

        if(TYPE_PROFORMA_LOCATION){
            complementPeriode = '<td class="periode"><input type="number" class="form-control" value="'+ qtePeriode +'"></td>\n' +
                "<td>"+ $("#periode").val() +"</td>\n"
        }

        $("#piece tbody").fadeIn().append("<tr>\n" +
            "<th data-id=\""+ id +"\" data-modele=\""+ modele +"\"><a class=\"delete\" href=\"javascript:void(0);\"><i class=\"material-icons\">delete_forever</i> </a></th>\n" +
            "<td>"+ reference +"</td>\n" +
            "<td>"+ libelle +"</td>\n" +
            "<td class='quantite'><input type=\"number\" class='form-control' value=\""+ $quantity +"\"></td>\n" +
            complementPeriode +
            "<td class='price text-right'>"+ prix +"</td>\n" +
            "<td class='remise text-right'>"+ remise +"</td>\n" +
            "<td class='amount text-right'>"+ amount +"</td>\n" +
            "</tr>");
    }

    function getLibelle() {
        var text = $product.data("libelle") == undefined ? '' : $product.data("libelle");
        text += " "+ $("#complement").val();

        if($("#activeDispo").is(":checked")){
            text += " - " + $("#disponibilite option:selected").text();

            if($("#periodeDispo").val() != ""){
                text += " (" + $("#periode").val() + ")";
            }
        }
        return text;
    }

    function delLine(arg) {
        swal({
                title: "Suppression de ligne",
                text: "Voulez-vous vraiment supprimer cette ligne ?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Oui",
                cancelButtonText: "Non",
                closeOnConfirm: false
            },
            function(){
                swal.close();
                $parent_tr = $(arg).parent().parent().parent();
                $($parent_tr).fadeOut(500,function () {
                    $($parent_tr).remove();
                    calculAmount();
                });
            });
    }

    function calculAmount() {
        var montantHT = 0;
        $.each($(".amount"),function (key, value) {
            montantHT = montantHT + parseInt($(value).text());
        });

        $("#montantHT").text(montantHT);
        $("#montantTVA").text(Math.ceil(montantHT * 0.18));

        if($("#isexonere").is(":checked")){
            $("#montantTTC").text(montantHT);
        }else{
            $("#montantTTC").text(Math.ceil(montantHT * 1.18));
        }
    }

    function editQantite(arg) {
        var $quantite = parseInt($(arg).val());
        var $td = $(arg).parent();

        var $qtePeriod = TYPE_PROFORMA_LOCATION ? $($td).siblings(".periode").children("input").val() : 1;
        updateOtherLines($quantite, $qtePeriod, $td);
    }

    function editPeriode(arg) {
        var $qtePeriod = parseInt($(arg).val());
        var $td = $(arg).parent();
        var $quantite = $($td).siblings(".quantite").children("input").val();
        updateOtherLines($quantite, $qtePeriod, $td);
    }

    function updateOtherLines($quantite, $qtePeriod , $td) {
        var $price = $($td).siblings(".price");
        var $remise = $($td).siblings(".remise");
        var $amount = (parseInt($($price).text()) * $qtePeriod * $quantite) * (1 - (parseFloat($($remise).text())/100));
        $($($td).siblings(".amount")).text( $amount );

        calculAmount();
    }

    $("#addProduct").click(function () {
       addNewProduit();
    });

    /**
     * Appelé par la fenêtre fille qui ouvre le popup d'ajout de produit
     */
    function addNewProduit() {
        var hauteur = 600;
        var largeur = 500;
        var top=(screen.height-hauteur)/2;
        var left=(screen.width-largeur)/2;
        var addproductWindow = window.open('{{ route("stock.produit.ajouter")."?from=proforma" }}',"","menubar=no, status=no, scrollbars=auto, menubar=no, width="+largeur+", height="+hauteur+", top="+top+", left="+left);
    }

    function refreshFromNewProduct(produit) {
        console.log(produit);
        var option = '<option value="'+produit.id+'" data-stock="0" data-modele="'+produit.modele+'" data-id="'+produit.id+'" data-price="'+produit.price+'" data-libelle="'+produit.libelle+'" data-reference="'+produit.reference+'">'+produit.reference+' '+produit.libelle+'</option>';
        //console.log(option);

        $("#produits").selectpicker('deselectAll');
        $("#produits").append(option);

        $("#produits").selectpicker('refresh');
        $("#produits").selectpicker('val',produit.id);
        $("#price").val(produit.price);

        //window.location.reload(false);
    }

    @if(request()->has('from') && request('from') == \App\Metier\Behavior\Notifications::UPDATE_FROM_PROFORMA)
    //En cas de modification de la pro forma
    calculAmount();
    @endif
</script>
@endsection