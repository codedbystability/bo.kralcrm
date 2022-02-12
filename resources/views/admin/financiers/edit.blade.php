@extends('layouts.master')


@section('content-title')
    @include('layouts.content-title',['title' =>'Finansci Duzenle','first' => '','second' => ''])
@endsection

@section('content')


    <form method="POST" action="{{ route('admin.financiers.update', $financier->id) }}"
          onsubmit="return setPermissionsArray()">
        @method('PUT')
        @csrf

        <input type="hidden" id="permission_list" name="permission_list">

        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">

                    <div class="card-body">


                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="name">ISIM SOYISIM</label>
                                        <input name="name" class="form-control"
                                               type="text"
                                               placeholder="ad-soyad giriniz"
                                               required
                                               value="{{$financier->name}}"
                                        >
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="username">KULLANICI ADI</label>
                                        <input name="username" class="form-control"
                                               type="text"
                                               placeholder="kullanici adi giriniz"
                                               required
                                               value="{{$financier->username}}"

                                        >
                                    </div>
                                </div>


                            </div>
                        </div>


                    </div>

                </div>

                <div class="card card-primary">

                    <div class="card-body">


                        <div class="form-group">
                            <div class="row">

                                <div class="col-12">
                                    <div class="card card-primary card-tabs">
                                        <div class="pt-2 px-3 bg-white">
                                            <h3 class="card-title">Izinler Yonetimi </h3>

                                        </div>

                                        <div class="card-header p-0 pt-1">
                                            <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                                                @foreach($permissions as $key => $permission)

                                                    @if($key >= 3 && $key % 3 === 0) <br> <br>  @endif

                                                    @if($key <= 1) <br><br><br> @endif

                                                    <li class="nav-item {{$key <= 1 ? 'col-lg-6' : 'col-lg-3'}}">
                                                        <a class="nav-link" data-toggle="pill"
                                                           href="#{{$permission['key']}}" role="tab"
                                                           aria-controls="custom-tabs-one-home"
                                                           aria-selected="false">{{$permission['title']}} </a>
                                                    </li>
                                                @endforeach


                                            </ul>
                                        </div>
                                        <div class="card-body">
                                            <div class="tab-content" id="custom-tabs-one-tabContent">

                                                @foreach($permissions as $permission)
                                                    <div class="tab-pane fade" id="{{$permission['key']}}"
                                                         role="tabpanel"
                                                         aria-labelledby="custom-tabs-one-home-tab">
                                                        <div class="form-group d-flex flex-wrap">

                                                            @foreach($permission['values'] as $key =>$value)

                                                                <div class="form-check col-lg-6 mt-2">
                                                                    <input class="form-check-input" type="checkbox"
                                                                           name="permission-{{$value['id']}}"
                                                                           id="permission-{{$value['id']}}"
                                                                           @if(in_array($value['id'],$permissionArray))
                                                                           checked
                                                                        @endif
                                                                    >
                                                                    <label
                                                                        class="form-check-label">{{$value['description']}} </label>
                                                                </div>

                                                                <br>


                                                            @endforeach


                                                            <button class="btn btn-primary col-lg-12 mt-4"
                                                                    type="button"
                                                                    onclick="handlePermissions({{$permission['values']}})">
                                                                Tumunu Sec \ Kaldir
                                                            </button>

                                                        </div>
                                                    </div>
                                                @endforeach

                                            </div>
                                        </div>
                                        <!-- /.card -->
                                    </div>
                                </div>

                            </div>
                        </div>


                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">Kaydet</button>
                    </div>
                </div>

            </div>

        </div>

    </form>




@endsection
