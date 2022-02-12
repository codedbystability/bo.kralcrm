@include('layouts.header')


@include('layouts.navbar')


@if(\Illuminate\Support\Facades\Auth::guard('web')->check())
    @include('admin.layouts.sidebar')
@elseif(\Illuminate\Support\Facades\Auth::guard('client')->check())
    @include('client.layouts.sidebar')
@elseif(\Illuminate\Support\Facades\Auth::guard('financier')->check())
    @include('financier.layouts.sidebar')
@endif


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->

@yield('content-title')
<!-- /.content-header -->


    <button type="button" class="btn btn-success swalDefaultSuccess" style="display: none" id="successToast">
        Launch Success Toast
    </button>

    <div class="content">
        <div class="container-fluid">
            @yield('content')
        </div>
    </div>

</div>
<!-- /.content-wrapper -->

<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
    <div class="p-3">
        <h5>Title</h5>
        <p>Sidebar content</p>
    </div>
</aside>
<!-- /.control-sidebar -->

@include('layouts.footer')


@if(\Illuminate\Support\Facades\Session::has('type') && \Illuminate\Support\Facades\Session::has('message'))
    <script>
        showToast(`{{\Illuminate\Support\Facades\Session::get('type')}}`, `{{\Illuminate\Support\Facades\Session::get('message')}}`)
    </script>
@endif



@yield('additional-js')
