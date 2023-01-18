<ul class="list">
    <li class="header">Menu</li>
    <li class="active">
        <a href="{{ route('home') }}">
            <i class="material-icons">home</i>
            <span>Tableau de bord</span>
        </a>
    </li>
    @if(\Illuminate\Support\Facades\Auth::user()->authorizes(\App\Service::DG, \App\Service::INFORMATIQUE,
        \App\Service::GESTIONNAIRE_PL, \App\Service::GESTIONNAIRE_VL, \App\Service::ADMINISTRATION)
    )
    <li>
        <a href="javascript:void(0);" class="menu-toggle">
            <i class="material-icons">business_center</i>
            <span>Missions</span>
        </a>
        <ul class="ml-menu">

            @if(\Illuminate\Support\Facades\Auth::user()->authorizes(\App\Service::DG, \App\Service::INFORMATIQUE,
                \App\Service::GESTIONNAIRE_VL, \App\Service::ADMINISTRATION)
            )
            <li>
                <a href="{{ route("mission.liste") }}">
                    <i class="material-icons">directions_car</i>
                    <span>Véhicule Léger (VL)</span>
                </a>
            </li>
            @endif

            @if(\Illuminate\Support\Facades\Auth::user()->authorizes(\App\Service::DG, \App\Service::INFORMATIQUE,
                \App\Service::GESTIONNAIRE_PL, \App\Service::ADMINISTRATION)
            )
            <li>
                <a href="{{ route("mission.liste-pl") }}">
                    <i class="material-icons">directions_bus</i>
                    <span>Poids Lourds (PL)</span>
                </a>
            </li>
            @endif
        </ul>
    </li>
    <li>
        <a href="javascript:void(0);" class="menu-toggle">
            <i class="material-icons">drive_eta</i>
            <span>Véhicules</span>
        </a>
        <ul class="ml-menu">
            <li>
                <a href="{{ route("vehicule.liste") }}">
                    <span>Liste</span>
                </a>
            </li>
            <li>
                <a href="{{ route("vehicule.liste.reformes") }}">
                    <span>Reformés</span>
                </a>
            </li>
            <li>
                <a href="{{ route("reparation.liste") }}">
                    <span>Réparations</span>
                </a>
            </li>
        </ul>
    </li>
    @endif
    @if(\Illuminate\Support\Facades\Auth::user()->authorizes(\App\Service::INFORMATIQUE, \App\Service::DG,
    \App\Service::ADMINISTRATION, \App\Service::COMPTABILITE))
    <li>
        <a href="javascript:void(0);" class="menu-toggle">
            <i class="material-icons">people</i>
            <span>Clients et fournisseurs</span>
        </a>
        <ul class="ml-menu">
            <li>
                <a href="{{ route("partenaire.liste",['type' => \App\Partenaire::CLIENT]) }}">
                    <span>Clients</span>
                </a>
            </li>
            <li>
                <a href="{{ route("partenaire.liste", ['type' => \App\Partenaire::FOURNISSEUR]) }}">
                    <span>Fournisseurs</span>
                </a>
            </li>
        </ul>
    </li>
    @endif
    @if(\Illuminate\Support\Facades\Auth::user()->authorizes(\App\Service::INFORMATIQUE, \App\Service::DG,
    \App\Service::ADMINISTRATION, \App\Service::COMPTABILITE, \App\Service::LOGISTIQUE))
    <li>
        <a href="javascript:void(0);" class="menu-toggle">
            <i class="material-icons">monetization_on</i>
            <span>Factures</span>
        </a>
        <ul class="ml-menu">
            <li>
                <a href="{{ route("facturation.liste.proforma") }}">
                    <span>Pro forma</span>
                </a>
            </li>
            <li>
                <a href="{{ route("facturation.liste.facture") }}">
                    <span>Factures</span>
                </a>
            </li>
            <li>
                <a href="{{ route("facturation.liste.all") }}">
                    <span>Toutes les factures</span>
                </a>
            </li>
            <li>
                <a href="{{ route("partenaire.fournisseur.factures") }}">
                    <span>Factures fournisseurs</span>
                </a>
            </li>
        </ul>
    </li>
    @endif
    <li>
        <a href="{{ route('compte.registre') }}" class="">
            <i class="material-icons">local_atm </i>
            <span>Trésorerie</span>
        </a>
    </li>
    @if(\Illuminate\Support\Facades\Auth::user()->authorizes(\App\Service::INFORMATIQUE, \App\Service::DG,
    \App\Service::ADMINISTRATION, \App\Service::LOGISTIQUE, \App\Service::COMPTABILITE))
    <li>
        <a href="javascript:void(0);" class="menu-toggle">
            <i class="material-icons">redeem</i>
            <span>Stock</span>
        </a>
        <ul class="ml-menu">
            <li>
                <a href="{{ route("stock.produit.mouvement") }}">Mouvements</a>
            </li>
            <li>
                <a href="{{ route("stock.produit.liste") }}">Inventaire</a>
            </li>
            <li>
                <a href="{{ route("stock.produit.famille") }}">Famille</a>
            </li>
            <li>
                <a href="{{ route("stock.produit.ratio") }}">Ratio</a>
            </li>
        </ul>
    </li>
    @endif
    <li>
        <a href="javascript:void(0);" class="menu-toggle">
            <i class="material-icons">assignment_ind</i>
            <span>Personnel</span>
        </a>
        <ul class="ml-menu">
            <li>
                <a href="{{ route('admin.employe.liste') }}">Employes</a>
            </li>
            <li>
                <a href="{{ route('admin.chauffeur.liste') }}">Chauffeurs</a>
            </li>
            @if(\Illuminate\Support\Facades\Auth::user()->authorizes(\App\Service::INFORMATIQUE, \App\Service::DG,
            \App\Service::ADMINISTRATION, \App\Service::COMPTABILITE))
            <li>
                <a href="{{ route('rh.salaire') }}">Salaire</a>
            </li>
            @endif
        </ul>
    </li>
    @if(\Illuminate\Support\Facades\Auth::user()->authorize(\App\Service::INFORMATIQUE, \App\Service::DG))
    <li>
        <a href="javascript:void(0);" class="menu-toggle">
            <i class="material-icons">settings</i>
            <span>Administration</span>
        </a>
        <ul class="ml-menu">
            <li>
                <a href="{{ route("admin.utilisateur.liste") }}">Utilisateurs</a>
            </li>
        </ul>
    </li>
    @endif
</ul>