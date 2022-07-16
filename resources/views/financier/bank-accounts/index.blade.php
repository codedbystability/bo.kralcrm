@extends('layouts.master')


@section('content-title')
    @include('layouts.content-title',['title' =>'Banka Hesaplari','first' => '','second' => ''])
@endsection

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">

                <div class="card-header text-right">
                    @can('add bank account')
                        <a href="{{route('financier.bank-accounts.create')}}" type="button" class="btn btn-primary">
                            <i class="fa fa-plus-circle"></i> <strong>Yeni Kayit</strong>
                        </a>
                    @endcan
                </div>

                <div class="card-body">

                    <form method="POST" action="{{ route('financier.bank-accounts.filter') }}">
                        @csrf
                        <div class="card-body">

                            <div class="form-group">
                                <label for="client_id">Musteri</label>
                                <select class="custom-select form-control-border" name="client_id" id="client_id">
                                    <option value="">Musteri Seciniz</option>
                                    @foreach($clients as $client)
                                        <option
                                            value="{{$client->id}}"
                                            @if($clientID === $client->id)selected="selected"@endif>
                                            {{$client->username}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="form-group">
                                <label for="currency_id">Para Birimi</label>
                                <select class="custom-select form-control-border" name="currency_id" id="currency_id">
                                    <option value="" selected>Para Birimi Seciniz</option>
                                    @foreach($currencies as $currency)
                                        <option value="{{$currency->id}}"
                                                @if($currencyID === $currency->id)
                                                    selected="selected"
                                            @endif
                                        >{{$currency->symbol}}</option>
                                    @endforeach
                                </select>
                            </div>


                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-success">Kaydet</button>
                        </div>
                    </form>


                </div>
                <!-- /.card-body -->
            </div>

        </div>
        <!-- /.col -->
    </div>


    <div class="row">
        <div class="col-12">
            <div class="card">

                {{--                <div class="card-header text-right">--}}
                {{--                    @can('add bank account')--}}
                {{--                        <a href="{{route('financier.bank-accounts.create')}}" type="button" class="btn btn-primary">--}}
                {{--                            <i class="fa fa-plus-circle"></i> <strong>Yeni Kayit</strong>--}}
                {{--                        </a>--}}
                {{--                    @endcan--}}

                {{--                </div>--}}

                <div class="card-body">
                    <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="table-responsive">
                                    <table id="example1" class="table table-bordered table-striped dataTable dtr-inline"
                                           role="grid" aria-describedby="example1_info">
                                        <thead>
                                        <tr role="row">
                                            <th style="width: 10px">#</th>
                                            <th style="width: 10px">DOVIZ</th>
                                            <th class="sorting sorting_asc" tabindex="0" aria-controls="example1"
                                                rowspan="1" colspan="1" aria-sort="ascending"
                                                aria-label="Rendering engine: activate to sort column descending">
                                                BANKA
                                            </th>

                                            <th class="sorting sorting_asc" tabindex="0" aria-controls="example1"
                                                rowspan="1" colspan="1" aria-sort="ascending"
                                                aria-label="Rendering engine: activate to sort column descending">
                                                TIP
                                            </th>

                                            {{--                                        <th class="sorting sorting_asc" tabindex="0" aria-controls="example1"--}}
                                            {{--                                            rowspan="1" colspan="1" aria-sort="ascending"--}}
                                            {{--                                            aria-label="Rendering engine: activate to sort column descending">--}}
                                            {{--                                            HESAP NO--}}
                                            {{--                                        </th>--}}

                                            <th class="sorting sorting_asc" tabindex="0" aria-controls="example1"
                                                rowspan="1" colspan="1" aria-sort="ascending"
                                                aria-label="Rendering engine: activate to sort column descending">
                                                IBAN
                                            </th>


                                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1"
                                                colspan="1"
                                                aria-label="Engine version: activate to sort column ascending">
                                                SAHIBI
                                            </th>
                                            {{--                                        <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1"--}}
                                            {{--                                            colspan="1" aria-label="Engine version: activate to sort column ascending">--}}
                                            {{--                                            SUBE--}}
                                            {{--                                        </th>--}}

                                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1"
                                                colspan="1"
                                                aria-label="Engine version: activate to sort column ascending">
                                                GUNLUK ISLEM
                                            </th>

                                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1"
                                                colspan="1"
                                                aria-label="Engine version: activate to sort column ascending">
                                                GUNLUK YATIRIM
                                            </th>

                                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1"
                                                colspan="1"
                                                aria-label="Engine version: activate to sort column ascending">
                                                GUNLUK CEKIM
                                            </th>

                                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1"
                                                colspan="1"
                                                aria-label="Engine version: activate to sort column ascending">
                                                GUNLUK NET
                                            </th>


                                            {{--                                        <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1"--}}
                                            {{--                                            colspan="1" aria-label="Engine version: activate to sort column ascending">--}}
                                            {{--                                            AKTIF MI?--}}
                                            {{--                                        </th>--}}


                                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1"
                                                colspan="1" aria-label="CSS grade: activate to sort column ascending">
                                                ISLEMLER
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        @foreach($data as $key=> $agreement)
                                            <tr class="{{$key % 2 === 0 ? 'even':'odd'}}">
                                                <td>{{$agreement->id}}</td>

                                                <td class="sorting_1 dtr-control">{{$agreement->accountable->currency->symbol}}</td>
                                                <td class="sorting_1 dtr-control">{{$agreement->accountable->bank->name}}</td>
                                                <td class="sorting_1 dtr-control">{{\App\Enums\AccountTypeEnum::get($agreement->type->key)}}</td>
                                                {{--                                            <td>{{$agreement->accountable->accno}}</td>--}}
                                                <td>{{$agreement->accountable->iban}}</td>
                                                <td>{{$agreement->accountable->owner}}</td>
                                                {{--                                            <td>{{$agreement->accountable->branch}}</td>--}}
                                                <td>{{$agreement->todayCount}}</td>
                                                <td>{{$agreement->todayDeposit}}</td>
                                                <td>{{$agreement->todayWithdraw}}</td>
                                                <td>{{$agreement->todayNet}}</td>

                                                <td class="project-actions d-flex justify-content-around">

                                                    @can('update bank account')
                                                        <form
                                                            action="{{ route('financier.bank-accounts.edit', $agreement->accountable->id) }}"
                                                            method="GET">
                                                            <button type="submit" class="btn btn-primary btn-sm ">
                                                                <i class="fas fa-pencil-alt">
                                                                </i>
                                                                Duzenle
                                                            </button>
                                                        </form>


                                                        <form
                                                            action="{{ route('financier.bank-accounts.activate', $agreement->id) }}"
                                                            method="GET">
                                                            <button type="submit" class="btn btn-secondary btn-sm ">
                                                                <i class="fas fa-pencil-alt">
                                                                </i>
                                                                @if($agreement->is_active)
                                                                    Pasif Et
                                                                @else
                                                                    Aktif Et
                                                                @endif
                                                            </button>
                                                        </form>
                                                    @endcan



                                                    @can('delete bank account')
                                                        <form
                                                            action="{{ route('financier.bank-accounts.destroy', $agreement->accountable->id) }}"
                                                            method="POST">
                                                            <button type="submit" class="btn btn-danger btn-sm">
                                                                {{ method_field('DELETE') }}
                                                                {{ csrf_field() }}
                                                                <i class="fas fa-trash">
                                                                </i>
                                                                Sil
                                                            </button>
                                                        </form>
                                                    @endcan

                                                </td>


                                            </tr>
                                        @endforeach

                                        </tbody>

                                    </table>
                                </div>
                            </div>
                        </div>


                        <div class="row">

                            <div class="col-12 table-responsive">
                                {{$data->links("pagination::bootstrap-4")}}
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>

        </div>
        <!-- /.col -->
    </div>

@endsection
