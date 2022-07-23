<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{url('/')}}" class="brand-link">
        <img src="{{url('img/AdminLTELogo.png')}}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
             style="opacity: .8">
        <span class="brand-text font-weight-light">Finans Panel</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{url('img/user2-160x160.jpg')}}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{\Illuminate\Support\Facades\Auth::user()->username}}</a>
            </div>
        </div>


        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <li class="nav-item">
                    <a href="{{route('financier.home')}}"
                       class="nav-link {{\Illuminate\Support\Facades\Request::is('financier/home') ? 'active' : ''}}">
                        <i class="nav-icon fas fa-th"></i>
                        <p>
                            Ana Sayfa
                        </p>
                    </a>
                </li>


                <li class="nav-item menu-{{\Illuminate\Support\Facades\Request::path() === 'financier/bank-accounts'
|| \Illuminate\Support\Facades\Request::path() === 'financier/papara-accounts'
 || \Illuminate\Support\Facades\Request::is('financier/bank-accounts/*')
 || \Illuminate\Support\Facades\Request::is('financier/banks')
 || \Illuminate\Support\Facades\Request::is('financier/banks/*')
 || \Illuminate\Support\Facades\Request::is('papara-accounts/*')   ? 'open' : ''}}">

                    @can('observe bank account')
                        <a href="#" class="nav-link ">
                            <i class="nav-icon fas fa-handshake"></i>
                            <p>
                                Hesaplar
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{route('financier.banks.index')}}"
                                   class="nav-link {{\Illuminate\Support\Facades\Request::path() === 'financier/banks' ?'active' : ''}}">
                                    <i class="fas fa-list nav-icon"></i>
                                    <p>Bankalar</p>
                                </a>
                            </li>
                        </ul>


                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{route('financier.bank-accounts.index')}}"
                                   class="nav-link {{\Illuminate\Support\Facades\Request::path() === 'financier/bank-accounts' ?'active' : ''}}">
                                    <i class="fas fa-list nav-icon"></i>
                                    <p>Banka Hesaplari</p>
                                </a>
                            </li>
                        </ul>
                    @endcan
                    @can('observe papara account')
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{route('financier.papara-accounts.index')}}"
                                   class="nav-link {{\Illuminate\Support\Facades\Request::path() === 'financier/papara-accounts' ?'active' : ''}}">
                                    <i class="fas fa-list nav-icon"></i>
                                    <p>Papara Hesaplari</p>
                                </a>
                            </li>
                        </ul>
                    @endcan


                </li>

                <li class="nav-item menu-{{\Illuminate\Support\Facades\Request::is('financier/transactions/papara/*') || \Illuminate\Support\Facades\Request::is('financier/transactions/havale/*') ? 'open' : ''}}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-book-open"></i>
                        <p>
                            Islemler
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview"
                        style="display:  {{\Illuminate\Support\Facades\Request::is('financier/transactions/papara/*') || \Illuminate\Support\Facades\Request::is('financier/transactions/havale/*')  ?'block' : 'none'}};">

                        <li class="nav-item menu-is-opening menu-{{\Illuminate\Support\Facades\Request::is('financier/transactions/papara/*')  ?'open' : ''}}">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>
                                    Papara Islemler
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            @can('observe papara withdraw waiting')
                                <ul class="nav nav-treeview"
                                    style="display: {{\Illuminate\Support\Facades\Request::is('financier/transactions/papara/*')  ?'block' : 'none'}};">
                                    <li class="nav-item">
                                        <a href="{{route('financier.transactions.papara.waiting-withdraws')}}"
                                           class="nav-link {{\Illuminate\Support\Facades\Request::path() === 'financier/transactions/papara/waiting-withdraws' ?'active' : ''}}">
                                            <i class="fas fa-building nav-icon"></i>
                                            <p>Bekleyen Cekimler</p>
                                        </a>
                                    </li>
                                </ul>
                            @endcan

                            @can('observe papara deposit waiting')

                                <ul class="nav nav-treeview"
                                    style="display: {{\Illuminate\Support\Facades\Request::is('financier/transactions/papara/*')  ?'block' : 'none'}};">
                                    <li class="nav-item ">
                                        <a href="{{route('financier.transactions.papara.waiting-deposits-approve-checked')}}"
                                           class="nav-link {{\Illuminate\Support\Facades\Request::path() === 'financier/transactions/papara/waiting-deposits-approve-checked' ?'active' : ''}}">
                                            <i class="fas fa-home nav-icon"></i>
                                            <p>Bekleyen Musteri Onayli</p>
                                        </a>
                                    </li>
                                </ul>

                                <ul class="nav nav-treeview"
                                    style="display: {{\Illuminate\Support\Facades\Request::is('financier/transactions/papara/*')  ?'block' : 'none'}};">
                                    <li class="nav-item ">
                                        <a href="{{route('financier.transactions.papara.waiting-deposits')}}"
                                           class="nav-link {{\Illuminate\Support\Facades\Request::path() === 'financier/transactions/papara/waiting-deposits' ?'active' : ''}}">
                                            <i class="fas fa-home nav-icon"></i>
                                            <p>Bekleyen Yatirimlar</p>
                                        </a>
                                    </li>
                                </ul>
                            @endcan


                            @can('observe papara deposit approved')
                                <ul class="nav nav-treeview"
                                    style="display: {{\Illuminate\Support\Facades\Request::is('financier/transactions/papara/*')  ?'block' : 'none'}};">
                                    <li class="nav-item">
                                        <a href="{{route('financier.transactions.papara.approved-deposits')}}"
                                           class="nav-link {{\Illuminate\Support\Facades\Request::path() === 'financier/transactions/papara/approved-deposits' ?'active' : ''}}">
                                            <i class="fas fa-check-circle nav-icon"></i>
                                            <p>Onaylanan Yatirimlar</p>
                                        </a>
                                    </li>
                                </ul>
                            @endcan


                            @can('observe papara withdraw completed')
                                <ul class="nav nav-treeview"
                                    style="display: {{\Illuminate\Support\Facades\Request::is('financier/transactions/papara/*')  ?'block' : 'none'}};">
                                    <li class="nav-item">
                                        <a href="{{route('financier.transactions.papara.completed-withdraws')}}"
                                           class="nav-link {{\Illuminate\Support\Facades\Request::path() === 'financier/transactions/papara/completed-withdraws' ?'active' : ''}}">
                                            <i class="fas fa-plus nav-icon"></i>
                                            <p>Tamamlanan Cekimler</p>
                                        </a>
                                    </li>
                                </ul>
                            @endcan

                            @can('observe papara deposit completed')
                                <ul class="nav nav-treeview"
                                    style="display: {{\Illuminate\Support\Facades\Request::is('financier/transactions/papara/*')  ?'block' : 'none'}};">
                                    <li class="nav-item">
                                        <a href="{{route('financier.transactions.papara.completed-deposits')}}"
                                           class="nav-link {{\Illuminate\Support\Facades\Request::path() === 'financier/transactions/papara/completed-deposits' ?'active' : ''}}">
                                            <i class="fas fa-plus-circle nav-icon"></i>
                                            <p>Tamamlanan Yatirimlar</p>
                                        </a>
                                    </li>
                                </ul>
                            @endcan


                            @can('observe papara withdraw cancelled')
                                <ul class="nav nav-treeview"
                                    style="display: {{\Illuminate\Support\Facades\Request::is('financier/transactions/papara/*')  ?'block' : 'none'}};">
                                    <li class="nav-item">
                                        <a href="{{route('financier.transactions.papara.cancelled-withdraws')}}"
                                           class="nav-link {{\Illuminate\Support\Facades\Request::path() === 'financier/transactions/papara/cancelled-withdraws' ?'active' : ''}}">
                                            <i class="fas fa-minus nav-icon"></i>
                                            <p>Iptal Edilen Cekimler</p>
                                        </a>
                                    </li>
                                </ul>
                            @endcan

                            @can('observe papara deposit cancelled')
                                <ul class="nav nav-treeview"
                                    style="display: {{\Illuminate\Support\Facades\Request::is('financier/transactions/papara/*')  ?'block' : 'none'}};">
                                    <li class="nav-item">
                                        <a href="{{route('financier.transactions.papara.cancelled-deposits')}}"
                                           class="nav-link {{\Illuminate\Support\Facades\Request::path() === 'financier/transactions/papara/cancelled-deposits' ?'active' : ''}}">
                                            <i class="fas fa-minus-square nav-icon"></i>
                                            <p>Iptal Edilen Yatirimlar</p>
                                        </a>
                                    </li>
                                </ul>
                            @endcan


                        </li>

                        <li class="nav-item menu-is-opening menu-{{\Illuminate\Support\Facades\Request::is('financier/transactions/havale/*')  ?'open' : ''}}">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>
                                    Havale Islemler
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            @can('observe havale withdraw waiting')
                                <ul class="nav nav-treeview"
                                    style="display: {{\Illuminate\Support\Facades\Request::is('financier/transactions/havale/*')  ?'block' : 'none'}};">
                                    <li class="nav-item">
                                        <a href="{{route('financier.transactions.havale.waiting-withdraws')}}"
                                           class="nav-link {{\Illuminate\Support\Facades\Request::path() === 'financier/transactions/havale/waiting-withdraws' ?'active' : ''}}">
                                            <i class="fas fa-building nav-icon"></i>
                                            <p>Bekleyen Cekimler</p>
                                        </a>
                                    </li>
                                </ul>
                            @endcan

                            @can('observe havale deposit waiting')
                                <ul class="nav nav-treeview"
                                    style="display: {{\Illuminate\Support\Facades\Request::is('financier/transactions/havale/*')  ?'block' : 'none'}};">
                                    <li class="nav-item ">
                                        <a href="{{route('financier.transactions.havale.waiting-deposits-approve-checked')}}"
                                           class="nav-link {{\Illuminate\Support\Facades\Request::path() === 'financier/transactions/havale/waiting-deposits-approve-checked' ?'active' : ''}}">
                                            <i class="fas fa-home nav-icon"></i>
                                            <p>Bekleyen Musteri Onayli</p>
                                        </a>
                                    </li>
                                </ul>

                                <ul class="nav nav-treeview"
                                    style="display: {{\Illuminate\Support\Facades\Request::is('financier/transactions/havale/*')  ?'block' : 'none'}};">
                                    <li class="nav-item ">
                                        <a href="{{route('financier.transactions.havale.waiting-deposits')}}"
                                           class="nav-link {{\Illuminate\Support\Facades\Request::path() === 'financier/transactions/havale/waiting-deposits' ?'active' : ''}}">
                                            <i class="fas fa-home nav-icon"></i>
                                            <p>Bekleyen Yatirimlar</p>
                                        </a>
                                    </li>
                                </ul>


                                <ul class="nav nav-treeview"
                                    style="display: {{\Illuminate\Support\Facades\Request::is('financier/transactions/havale/*')  ?'block' : 'none'}};">
                                    <li class="nav-item ">
                                        <a href="{{route('financier.transactions.havale.waiting-deposits-account')}}"
                                           class="nav-link {{\Illuminate\Support\Facades\Request::path() === 'financier/transactions/havale/waiting-deposits-account' ?'active' : ''}}">
                                            <i class="fas fa-home nav-icon"></i>
                                            <p>Hesap Bekleyen Yatirimlar</p>
                                        </a>
                                    </li>
                                </ul>
                            @endcan


                            @can('observe havale deposit approved')
                                <ul class="nav nav-treeview"
                                    style="display: {{\Illuminate\Support\Facades\Request::is('financier/transactions/havale/*')  ?'block' : 'none'}};">
                                    <li class="nav-item">
                                        <a href="{{route('financier.transactions.havale.approved-deposits')}}"
                                           class="nav-link {{\Illuminate\Support\Facades\Request::path() === 'financier/transactions/havale/approved-deposits' ?'active' : ''}}">
                                            <i class="fas fa-check-circle nav-icon"></i>
                                            <p>Onaylanan Yatirimlar</p>
                                        </a>
                                    </li>
                                </ul>
                            @endcan

                            @can('observe havale withdraw approved')
                                <ul class="nav nav-treeview"
                                    style="display: {{\Illuminate\Support\Facades\Request::is('financier/transactions/havale/*')  ?'block' : 'none'}};">
                                    <li class="nav-item">
                                        <a href="{{route('financier.transactions.havale.completed-withdraws')}}"
                                           class="nav-link {{\Illuminate\Support\Facades\Request::path() === 'financier/transactions/havale/completed-withdraws' ?'active' : ''}}">
                                            <i class="fas fa-plus nav-icon"></i>
                                            <p>Tamamlanan Cekimler</p>
                                        </a>
                                    </li>
                                </ul>
                            @endcan

                            @can('observe havale deposit completed')
                                <ul class="nav nav-treeview"
                                    style="display: {{\Illuminate\Support\Facades\Request::is('financier/transactions/havale/*')  ?'block' : 'none'}};">
                                    <li class="nav-item">
                                        <a href="{{route('financier.transactions.havale.completed-deposits')}}"
                                           class="nav-link {{\Illuminate\Support\Facades\Request::path() === 'financier/transactions/havale/completed-deposits' ?'active' : ''}}">
                                            <i class="fas fa-plus-circle nav-icon"></i>
                                            <p>Tamamlanan Yatirimlar</p>
                                        </a>
                                    </li>
                                </ul>
                            @endcan


                            @can('observe havale withdraw cancelled')
                                <ul class="nav nav-treeview"
                                    style="display: {{\Illuminate\Support\Facades\Request::is('financier/transactions/havale/*')  ?'block' : 'none'}};">
                                    <li class="nav-item">
                                        <a href="{{route('financier.transactions.havale.cancelled-withdraws')}}"
                                           class="nav-link {{\Illuminate\Support\Facades\Request::path() === 'financier/transactions/havale/cancelled-withdraws' ?'active' : ''}}">
                                            <i class="fas fa-minus nav-icon"></i>
                                            <p>Iptal Edilen Cekimler</p>
                                        </a>
                                    </li>
                                </ul>
                            @endcan


                            @can('observe havale deposit cancelled')
                                <ul class="nav nav-treeview"
                                    style="display: {{\Illuminate\Support\Facades\Request::is('financier/transactions/havale/*')  ?'block' : 'none'}};">
                                    <li class="nav-item">
                                        <a href="{{route('financier.transactions.havale.cancelled-deposits')}}"
                                           class="nav-link {{\Illuminate\Support\Facades\Request::path() === 'financier/transactions/havale/cancelled-deposits' ?'active' : ''}}">
                                            <i class="fas fa-minus-square nav-icon"></i>
                                            <p>Iptal Edilen Yatirimlar</p>
                                        </a>
                                    </li>
                                </ul>
                            @endcan

                        </li>


                    </ul>
                </li>

                @can('observe reports')

                    <li class="nav-item menu-{{\Illuminate\Support\Facades\Request::is('financier/transactions/papara/*')
 || \Illuminate\Support\Facades\Request::is('financier/transactions/reports/accounts-filter/*')
 || \Illuminate\Support\Facades\Request::is('financier/transactions/reports/*')
 || \Illuminate\Support\Facades\Request::is('financier/transactions/havale/*') ? 'open' : ''}}">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-book-open"></i>
                            <p>
                                Rapor Yonetimi
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview"
                            style="display:  {{\Illuminate\Support\Facades\Request::is('financier/transactions/papara/*') || \Illuminate\Support\Facades\Request::is('financier/transactions/havale/*')  ?'block' : 'none'}};">

                            <li class="nav-item">
                                <a href="{{route('financier.transactions.reports.index')}}"
                                   class="nav-link {{\Illuminate\Support\Facades\Request::is('financier/transactions/reports') || \Illuminate\Support\Facades\Request::is('financier/transactions/reports') ? 'active' : ''}}">
                                    <i class="nav-icon fas fa-th"></i>
                                    <p>
                                        İşlem Raporları
                                    </p>
                                </a>
                            </li>


                            {{--                @can('observe accountreports')--}}
                            <li class="nav-item">
                                <a href="{{route('financier.transactions.reports.accounts')}}"
                                   class="nav-link {{\Illuminate\Support\Facades\Request::is('financier/transactions/reports/accounts') || \Illuminate\Support\Facades\Request::is('financier/transactions/reports/accounts-filter') ? 'active' : ''}}">
                                    <i class="nav-icon fas fa-th"></i>
                                    <p>
                                        Hesap Raporlar
                                    </p>
                                </a>
                            </li>
                            {{--                @endcan--}}

                        </ul>
                    </li>


                @endcan




                <li class="nav-item">
                    <a href="{{route('financier.profile')}}"
                       class="nav-link {{\Illuminate\Support\Facades\Request::is('financier/profile') ? 'active' : ''}}">
                        <i class="nav-icon fas fa-user-alt"></i>
                        <p>
                            Profil
                        </p>
                    </a>
                </li>


                <li class="nav-item">
                    <a href="{{route('financier.logout')}}"
                       class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>
                            Cikis Yap
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
