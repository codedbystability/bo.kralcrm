@extends('layouts.master')


@section('content-title')
    @include('layouts.content-title',['title' =>'','first' => '','second' => ''])
@endsection

@section('content')


    <div class="row">
        <!-- left column -->
        <div class="col-md-12">

            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Profil</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form method="POST" action="{{ route('admin.profile.update') }}">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Ad-Soyad</label>
                            <input type="text" class="form-control" name="name"
                                   value="{{\Illuminate\Support\Facades\Auth::user()->name}}"
                                   placeholder="Ad-Soyad giriniz"
                                   required

                            >
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Kullanici Adi</label>
                            <input type="text" class="form-control" name="username"
                                   value="{{\Illuminate\Support\Facades\Auth::user()->username}}"
                                   placeholder="Kullanici Adi giriniz"
                                   required

                            >
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">Password</label>
                            <input type="password" class="form-control" name="password"
                                   placeholder="Parola Giriniz">
                        </div>
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Guncelle</button>
                    </div>
                </form>
            </div>

        </div>

    </div>


@endsection


