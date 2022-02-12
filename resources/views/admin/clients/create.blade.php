@extends('layouts.master')


@section('content-title')
    @include('layouts.content-title',['title' =>'Musteri Ekle','first' => 'Agreements','second' => 'Create'])
@endsection

@section('content')


    <form method="POST" action="{{ route('admin.clients.store') }}">

        @csrf

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
                                        >
                                    </div>
                                </div>


                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="password">SIFRE</label>
                                        <input name="password" class="form-control"
                                               type="text"
                                               placeholder="sifre giriniz"
                                               required
                                        >
                                    </div>
                                </div>


                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="password">CEKIM ISLEM ORAN %</label>
                                        <input name="withdraw_percentage" class="form-control"
                                               type="number"
                                               min="1"
                                               max="100"
                                               placeholder="cekim orani giriniz"
                                               required
                                        >
                                    </div>
                                </div>


                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="password">YATIRIM ISLEM ORAN %</label>
                                        <input name="deposit_percentage" class="form-control"
                                               type="number"
                                               min="1"
                                               max="100"
                                               placeholder="yatirim orani giriniz"
                                               required
                                        >
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
