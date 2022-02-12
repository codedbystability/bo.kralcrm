<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{url('/')}}" class="brand-link">
        <img src="{{url('img/AdminLTELogo.png')}}" alt="clientLTE Logo" class="brand-image img-circle elevation-3"
             style="opacity: .8">
        <span class="brand-text font-weight-light">Musteri Panel</span>
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
                    <a href="{{route('client.home')}}"
                       class="nav-link {{\Illuminate\Support\Facades\Request::is('client/home') ? 'active' : ''}}">
                        <i class="nav-icon fas fa-th"></i>
                        <p>
                            Ana Sayfa
                        </p>
                    </a>
                </li>


                <li class="nav-item">
                    <a href="{{route('client.websites.index')}}"
                       class="nav-link {{\Illuminate\Support\Facades\Request::is('client/websites') || \Illuminate\Support\Facades\Request::is('client/websites/*')? 'active' : ''}}">
                        <i class="nav-icon fas fa-th"></i>
                        <p>
                            Siteler
                        </p>
                    </a>
                </li>



                <li class="nav-item">
                    <a href="{{route('client.transactions.reports.index')}}"
                       class="nav-link {{\Illuminate\Support\Facades\Request::is('client/transactions/reports') || \Illuminate\Support\Facades\Request::is('client/transactions/reports/*') ? 'active' : ''}}">
                        <i class="nav-icon fas fa-th"></i>
                        <p>
                            Raporlar
                        </p>
                    </a>
                </li>


                <li class="nav-item">
                    <a href="{{route('client.profile')}}"
                       class="nav-link {{\Illuminate\Support\Facades\Request::is('client/profile') ? 'active' : ''}}">
                        <i class="nav-icon fas fa-user-alt"></i>
                        <p>
                            Profil
                        </p>
                    </a>
                </li>


                <li class="nav-item">
                    <a href="{{route('client.logout')}}"
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
