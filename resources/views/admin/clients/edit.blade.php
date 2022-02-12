@extends('layouts.master')


@section('content-title')
    @include('layouts.content-title',['title' =>'Musteri Duzenle','first' => 'Agreements','second' => 'Create'])
@endsection

@section('content')


    <form method="POST" action="{{ route('admin.clients.update', $client->id) }}">

        @csrf
        @method('PUT')

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
                                               value="{{$client->name}}"
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
                                               value="{{$client->username}}"

                                        >
                                    </div>
                                </div>


                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="password">SIFRE</label>
                                        <input name="password" class="form-control"
                                               type="text"
                                               placeholder="sifre giriniz"
                                        >
                                    </div>
                                </div>


                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="password">CEKIM ISLEM ORAN</label>
                                        <input name="withdraw_percentage" class="form-control"
                                               type="number"
                                               placeholder="cekim orani giriniz"
                                               required
                                               min="1"
                                               max="100"
                                               value="{{$client->withdraw_percentage}}"

                                        >
                                    </div>
                                </div>


                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="password">YATIRIM ISLEM ORAN</label>
                                        <input name="deposit_percentage" class="form-control"
                                               type="number"
                                               placeholder="cekim orani giriniz"
                                               required
                                               min="1"
                                               max="100"
                                               value="{{$client->deposit_percentage}}"

                                        >
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>

                    <div class="card-body d-flex justify-content-around">
                        @foreach($client->clientAccountAgreements as $agreement)
                            <div class="row col-lg-5">
                                <label for="papara_agreement" style="margin-left: 10px"> {{$agreement->method->name}}
                                    Anlasmasi </label>
                                <div class="form-group d-flex col-lg-12 justify-content-between mt-3">
                                    @foreach($accountTypes as $accountType)
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio"
                                                   name="{{$agreement->method->key}}-agreement"
                                                   value="{{$accountType->key}}"
                                                   @if($agreement->account_type_id === $accountType->id )
                                                   checked
                                                @endif>

                                            <label
                                                class="form-check-label">{{\App\Enums\AccountTypeEnum::get($accountType->key)}}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach


                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">Kaydet</button>
                    </div>

                </div>
            </div>


        </div>

    </form>




@endsection
