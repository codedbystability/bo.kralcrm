{{--@extends('layouts.master')--}}


{{--@section('content-title')--}}
    @include('layouts.content-title',['title' =>'Islem Raporlari','first' => '','second' => ''])
@endsection

@section('content')

    <style>
        .table-striped > tbody > tr:nth-child(odd) > td,
        .table-striped > tbody > tr:nth-child(odd) > th {
            background-color: rgba(0, 0, 0, 0.3);
        }
    </style>

    <div class="row">
        <div class="col-12">
            <div class="card card-primary">

                <div class="card-header">
                    <h3 class="card-title">Filtreler</h3>

                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                </div>
                <div class="card-body">
                    <form action="{{route('financier.transactions.reports.filter')}}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="form-group col-12 p-0 d-flex">
                                <div class="form-group col-6 p-0">
                                    <label for="client">Musteriler</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                          <span class="input-group-text @if(isset($client_name)) bg-primary @endif">
                                            <i class="fas fa-people-arrows"></i>
                                          </span>
                                        </div>
                                        <input class="form-control"
                                               list="client"
                                               name="client_name"
                                               id="client-input"
                                               placeholder="Tum Musteriler"
                                               onfocus="this.value = null"
                                               value="{{$client_name}}"
                                               onchange="dataListChanged(this)"
                                        >
                                    </div>
                                    <datalist id="client">
                                        @foreach($clients as $client)
                                            <option value="{{$client->name}}">
                                        @endforeach
                                    </datalist>
                                </div>


                                <div class="form-group col-6 p-0">
                                    <label for="client">Para Birimi</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                          <span class="input-group-text @if(isset($currency_name)) bg-primary @endif">
                                            <i class="fas fa-people-arrows"></i>
                                          </span>
                                        </div>
                                        <input class="form-control"
                                               list="currency"
                                               name="currency_name"
                                               id="currency-input"
                                               placeholder="Tum Para Birimleri"
                                               onfocus="this.value = null"
                                               value="{{$currency_name}}"
                                               onchange="dataListChanged(this)"
                                        >
                                    </div>
                                    <datalist id="currency">
                                        @foreach($currencies as $currency)
                                            <option value="{{$currency->code}}">
                                        @endforeach
                                    </datalist>
                                </div>
                            </div>

                            <div class="form-group col-12 p-0 d-flex">

                                <div class="form-group col-6 p-0" id="website-content">
                                    <label for="website" id="website-label">Siteler</label>

                                    <div class="input-group" id="website-container">
                                        <div class="input-group-prepend">
                                          <span class="input-group-text @if(isset($website_name)) bg-primary @endif">
                                            <i class="fas fa-sitemap"></i>
                                          </span>
                                        </div>
                                        <input class="form-control" list="website"
                                               onfocus="this.value = null"
                                               id="website-input"
                                               name="website_name"
                                               autocomplete="off"
                                               value="{{$website_name}}"
                                               placeholder="Tum Siteler"
                                        >
                                    </div>

                                    <datalist id="website">
                                        @foreach($websites as $website)
                                            <option value="{{$website->domain}}">
                                        @endforeach
                                    </datalist>
                                </div>
                                <div class="form-group col-6 p-0">

                                    <label for="status">Islem Sonucu</label>

                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                          <span class="input-group-text @if(isset($status_name)) bg-primary @endif">
                                            <i class="fas fa-restroom"></i>
                                          </span>
                                        </div>
                                        <input class="form-control"
                                               list="statutes"
                                               name="status_name"
                                               autocomplete="off"
                                               value="{{$status_name}}"
                                               placeholder="Tum Sonuclar"
                                               onfocus="this.value = null"
                                               onchange="dataListChanged(this)"
                                        >
                                    </div>

                                    <datalist id="statutes">
                                        @foreach($statutes as $status)
                                            <option value="{{$status->key}}">
                                                {{\App\Enums\TransactionStatusEnum::get($status->key)}}
                                            </option>
                                        @endforeach
                                    </datalist>

                                </div>
                            </div>

                            <div class="form-group col-12 p-0 d-flex">

                                <div class="form-group col-6 p-0">
                                    <label for="type">Tipi</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                          <span class="input-group-text @if(isset($type_name)) bg-primary @endif">
                                            <i class="fas fa-file"></i>
                                          </span>
                                        </div>
                                        <input class="form-control" list="type"
                                               onfocus="this.value = null"
                                               placeholder="Tum Tipler"
                                               value="{{$type_name}}"
                                               autocomplete="off"
                                               name="type_name">
                                    </div>

                                    <datalist id="type">
                                        @foreach($types as $type)
                                            <option value="{{$type->key}}">
                                                {{\App\Enums\TransactionTypeEnum::get($type->key)}}
                                            </option>
                                        @endforeach
                                    </datalist>
                                </div>
                                <div class="form-group col-6 p-0">
                                    <label for="type">Method</label>


                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                          <span class="input-group-text @if(isset($method_name)) bg-primary @endif">
                                            <i class="fas fa-mountain"></i>
                                          </span>
                                        </div>

                                        <input class="form-control" list="method"
                                               placeholder="Tum Methodlar"
                                               name="method_name"
                                               value="{{$method_name}}"
                                               autocomplete="off"
                                               onfocus="this.value = null">
                                    </div>

                                    <datalist id="method">
                                        @foreach($methods as $method)
                                            <option value="{{$method->key}}">{{$method->name}}</option>
                                        @endforeach
                                    </datalist>
                                </div>
                            </div>

                            <div class="form-group col-12 p-0 d-flex">
                                <div class="form-group col-6 p-0">
                                    <label for="min_amount">Miktar Minimum</label>

                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                          <span class="input-group-text @if(isset($min_amount)) bg-primary @endif">
                                            <i class="fas fa-minus-circle"></i>
                                          </span>
                                        </div>
                                        <input type="number" min="0" name="min_amount" class="form-control"
                                               placeholder="Minimum miktar giriniz .."
                                               value="{{$min_amount}}"
                                               id="min_amount">
                                    </div>
                                </div>
                                <div class="form-group col-6 p-0">

                                    <label for="max_amount">Miktar Maksimum</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                          <span class="input-group-text  @if(isset($max_amount)) bg-primary @endif">
                                            <i class="fas fa-plus-circle"></i>
                                          </span>
                                        </div>
                                        <input type="number" min="1" name="max_amount" class="form-control"
                                               placeholder="Maksimum miktar giriniz .."
                                               value="{{$max_amount}}"
                                               id="max_amount">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-12 p-0 d-flex">

                                <div class="form-group col-6 p-0">
                                    <label>Tarih Baslangic:</label>

                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                          <span
                                              class="input-group-text @if(isset($dateActive) && $dateActive) bg-primary @endif">
                                            <i class="far fa-calendar-alt"></i>
                                          </span>
                                        </div>
                                        <input type="date" class="form-control"
                                               value="{{$dateFrom}}"
                                               name="date_from"
                                               min="1997-01-01" max="2030-12-31"
                                        >
                                    </div>
                                    <!-- /.input group -->
                                </div>
                                <div class="form-group col-6 p-0">
                                    <label>Tarih Bitis:</label>

                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                          <span
                                              class="input-group-text @if(isset($dateActive) && $dateActive) bg-primary @endif">
                                            <i class="far fa-calendar-alt"></i>
                                          </span>
                                        </div>
                                        <input type="date" class="form-control "
                                               value="{{$dateTo}}"
                                               name="date_to"
                                               min="1997-01-01" max="2030-12-31"
                                        >
                                    </div>
                                    <!-- /.input group -->
                                </div>

                            </div>


                            <div class="col-12 d-flex justify-content-around" style="margin-top: 20px">
                                <button class="btn btn-primary col-lg-4 mt-3" type="submit">
                                    <i class="fas fa-check-circle">
                                        FILTRELE
                                    </i>
                                </button>
                                <form action="{{route('financier.transactions.reports.index')}}" method="GET"
                                      id="clear-filter-id"
                                      name="clear-filter">
                                    <button class="btn btn-secondary col-lg-3 mt-3" type="submit"
                                            onclick="handleClearFilter()"
                                    >
                                        <i class="fas fa-trash">
                                            TEMIZLE
                                        </i>
                                    </button>
                                </form>


                            </div>


                        </div>
                    </form>
                </div>
                <!-- /.card-body -->
            </div>

            @if($data && count($data) >= 1)
                <div class="row">
                    @foreach($widgets as $widget)

                        @if($widget)

                            <div class="col-lg-6 ">
                                <!-- small card -->
                                <div class="small-box {{$widget['bg']}}">
                                    <div class="inner">
                                        <span>{{$widget['subValue']}} ₺</span>
                                        <br>
                                        <span>{{$widget['value']}} adet</span>

                                        <span>{{$widget['title']}}</span>
                                    </div>
                                    <div class="icon">
                                        <i class="fas {{$widget['icon']}}" style="font-size: 25px"></i>
                                    </div>

                                </div>
                            </div>
                        @endif

                    @endforeach


                </div>

            @endif

            <div class="card">

                <div class="card-header text-right">


                </div>
            </div>
            <!-- /.card-body -->
        </div>


    </div>
    <!-- /.col -->
    </div>

@endsection
