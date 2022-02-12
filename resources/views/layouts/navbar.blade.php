<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        {{--        <li class="nav-item d-none d-sm-inline-block">--}}
        {{--            <a href="index3.html" class="nav-link">Home</a>--}}
        {{--        </li>--}}
        {{--        <li class="nav-item d-none d-sm-inline-block">--}}
        {{--            <a href="#" class="nav-link">Contact</a>--}}
        {{--        </li>--}}
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto d-flex align-items-center">
        <!-- Navbar Search -->


{{--        <!-- Messages Dropdown Menu -->--}}
{{--        @if(isset($notes) && count($notes) >= 1)--}}

{{--            <li class="nav-item dropdown">--}}
{{--                <a class="nav-link" data-toggle="dropdown" href="#">--}}
{{--                    <i class="far fa-comments"></i>--}}
{{--                    <span class="badge badge-danger navbar-badge">{{count($notes)}}</span>--}}
{{--                </a>--}}


{{--                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="width: 100vw">--}}
{{--                    @foreach($notes as $key=>$note)--}}
{{--                        <a href="#" class="dropdown-item">--}}
{{--                            <!-- Message Start -->--}}
{{--                            <div class="media">--}}
{{--                                <div class="media-body w-100" style="width: 100%;flex-wrap: wrap">--}}
{{--                                    <h3 class="dropdown-item-title">--}}
{{--                                        {{$note->user->name}}--}}
{{--                                        <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>--}}
{{--                                    </h3>--}}
{{--                                    <p class="text-sm"--}}
{{--                                       style="    word-break: break-all;">{{substr($note->message,0,75)}}</p>--}}
{{--                                    <p class="text-sm text-muted"><i--}}
{{--                                            class="far fa-clock mr-1"></i> {{$note->created_at}}</p>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <!-- Message End -->--}}
{{--                        </a>--}}
{{--                        <div class="dropdown-divider"></div>--}}
{{--                    @endforeach--}}

{{--                </div>--}}
{{--            </li>--}}

{{--    @endif--}}

    <!-- Notifications Dropdown Menu -->
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
                <i class="fas fa-th-large"></i>
            </a>
        </li>
    </ul>
</nav>
<!-- /.navbar -->
