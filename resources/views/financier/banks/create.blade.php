@extends('layouts.master')


@section('content-title')
    @include('layouts.content-title',['title' =>'Banka Hesap Ekle','first' => 'Agreements','second' => 'Create'])
@endsection

@section('content')


    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">

                <!-- form start -->
                <form method="POST" action="{{ route('financier.bank-accounts.store') }}">
                    @csrf
                    <div class="card-body">

                        <div class="form-group">
                            <label for="currency_id">Doviz Tipi</label>
                            <select class="custom-select form-control-border" name="currency_id"
                                    required
                                    id="currency_id">
                                @foreach($currencies as $currency)
                                    <option value="{{$currency->id}}">{{$currency->local_name}}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="form-group">
                            <label for="provider_id">Banka</label>
                            <select class="custom-select form-control-border" name="bank_id"
                                    id="bank_id" required>
                                <option value="">Banka Seciniz</option>
                                @foreach($banks as $providerItem)
                                    <option value="{{$providerItem->id}}">{{$providerItem->name}}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="form-group">
                            <label for="provider_id">Tip</label>
                            <select class="custom-select form-control-border" name="type_id" id="type_id" required
                                    onchange="handleShowClientSelect(this.value)"
                            >
                                <option value="">Tip Seciniz</option>
                                <option value="2">Genel - Global</option>
                                <option value="1">Ozel - Personal</option>
                            </select>
                        </div>

                        <div class="form-group" style="display: none" id="client_id_visible">
                            <label for="client_id">Musteri</label>
                            <select class="custom-select form-control-border" name="client_id"
                                    id="client_id">
                                <option value="">Musteri Seciniz</option>
                                @foreach($clients as $client)
                                    <option value="{{$client->id}}">{{$client->name}}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="owner">HESAP SAHIBI</label>
                                        <input name="owner" class="form-control"
                                               type="text"
                                               placeholder="hesap sahibi"
                                               required
                                        >
                                    </div>

                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="accno">HESAP NO</label>
                                        <input name="accno" class="form-control"
                                               type="text"
                                               placeholder="hesap no"
                                               required
                                        >
                                    </div>

                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="iban">IBAN NO</label>
                                        <input name="iban" class="form-control"
                                               type="text"
                                               placeholder="iban no"
                                               required
                                        >
                                    </div>
                                </div>


                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="iban">SUBE</label>
                                        <input name="branch" class="form-control" type="text" placeholder="sube"
                                               required>
                                    </div>
                                </div>


                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="min_withdraw">MIN CEKIM</label>
                                        <input name="min_withdraw" class="form-control" type="number" step="0.1"
                                               placeholder="min cekim"
                                               value="100.00"
                                               required>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="max_withdraw">MAX CEKIM</label>
                                        <input name="max_withdraw" class="form-control" type="number"
                                               value="5000.00" step="0.1"
                                               placeholder="max cekim" required>
                                    </div>
                                </div>


                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="min_deposit">MIN YATIRIM</label>
                                        <input name="min_deposit" class="form-control" type="number"
                                               value="100.00" step="0.1"
                                               placeholder="min yatirim" required>
                                    </div>
                                </div>


                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="max_deposit">MAX YATIRIM</label>
                                        <input name="max_deposit" class="form-control" type="text"
                                               placeholder="max yatirim"
                                               value="5000.00" step="0.1"
                                               required>
                                    </div>
                                </div>


                            </div>
                        </div>


                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">Kaydet</button>
                    </div>
                </form>
            </div>

        </div>

    </div>



@endsection
