@extends('layouts.master')


@section('content-title')
    @include('layouts.content-title',['title' =>'Islem Detay','first' => '','second' => ''])
@endsection

@section('content')


    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-body">
                    <div class="form-group">
                        <div class="row">
                            @foreach ($fields as $key => $node)
                                @if($node === null) @continue @endif
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="owner">{{$key}}</label>
                                        <input name="owner" class="form-control"
                                               type="text"
                                               value="{{$node}}"
                                               disabled
                                        >
                                    </div>
                                </div>
                            @endforeach

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="owner">ISLEM MIKTARI</label>
                                    <input name="owner" class="form-control"
                                           type="text"
                                           value="{{$fields['amount']}}"
                                           disabled
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(isset($transaction->transactionable->bank))
                        <input type="hidden" id="theBankId" value="{{$transaction->transactionable->bank}}">
                    @else
                        <input type="hidden" id="theBankId" value="null">
                    @endif

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-primary">

                                <!-- form start -->
                                <form method="POST" action="" id="filter-form">
                                    @csrf
                                    <div class="card-body row">


                                        <div class="form-group col-lg-4">
                                            <label for="provider_id">Banka</label>
                                            <select class="custom-select form-control-border" name="bank_id"
                                                    id="bank_id" required>
                                                <option value="">Banka Seciniz</option>
                                                @foreach($banks as $providerItem)
                                                    <option
                                                        @if($providerItem->name === $bankName)
                                                        selected="selected"
                                                        @endif
                                                        value="{{$providerItem->id}}">{{$providerItem->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group col-lg-4">
                                            <label for="provider_id">Hesap Sahibi</label>
                                            <input class="custom-select form-control-border" name="owner"
                                                   value="{{$formFilter['owner']}}"
                                                   id="owner"/>
                                        </div>


                                        <div class="form-group col-lg-4">
                                            <label for="provider_id">Hesap No</label>
                                            <input class="custom-select form-control-border" name="accno"
                                                   value="{{$formFilter['accno']}}"
                                                   id="accno"/>
                                        </div>


                                        <div class="form-group col-lg-4">
                                            <label for="provider_id">Iban</label>
                                            <input class="custom-select form-control-border" name="iban"
                                                   value="{{$formFilter['iban']}}"
                                                   id="iban"/>
                                        </div>


                                        <div class="form-group col-lg-4">
                                            <label for="provider_id">Sube</label>
                                            <input class="custom-select form-control-border" name="branch"
                                                   value="{{$formFilter['branch']}}"
                                                   id="branch"/>
                                        </div>

                                        <input type="hidden" name="type_id" value="2">


                                    </div>

                                    <div class="card-footer row col-lg-12 justify-content-around">
                                        <button type="button" class="btn btn-secondary col-lg-4"
                                                onclick="handleClear()">Temizle
                                        </button>
                                        <button type="submit" class="btn btn-primary col-lg-4">Filtrele</button>
                                    </div>
                                </form>
                            </div>

                        </div>

                    </div>


                    @if($accounts && count($accounts)>=1)
                        <div class="form-group">
                            <div class="row">
                                <table class="table table-striped projects">
                                    <thead>
                                    <th>#</th>
                                    <th>BANKA</th>
                                    <th>DOVIZ</th>
                                    <th>HESAP SAHIBI</th>
                                    <th>HESAP NO</th>
                                    <th>GUNLUK ATAMA</th>
                                    <th>GUNLUK YATIRIM</th>
                                    <th>GUNLUK CEKIM</th>
                                    <th>GUNLUK NET</th>
                                    <th>ISLEMLER</th>
                                    </thead>
                                    <tbody>

                                    @foreach($accounts as $account)

                                        @if(($transaction->method->key === 'papara') || (isset($fields) && array_key_exists('bank',$fields) && strtolower($fields['bank']) === strtolower($account->accountable->bank->name)))
                                            <tr style="background-color: rgba(0, 230, 64, .3)">
                                        @else
                                            <tr style="">
                                                @endif
                                                <td>
                                                    {{$account->accountable->id}}
                                                </td>

                                                <td>
                                                    @if($transaction->method->key === 'papara')
                                                        {{$bank = 'PAPARA'}}
                                                    @elseif($account->accountable->owner === 'ANA KASA')
                                                        {{$bank = 'ANA KASA'}}
                                                    @else
                                                        {{$bank = $account->accountable->bank->name}}
                                                    @endif
                                                </td>
                                                <td>
                                                    {{$account->accountable->currency->code}}
                                                </td>
                                                <td>
                                                    {{$account->accountable->owner}}
                                                </td>

                                                <td>
                                                    {{$account->accountable->accno}}
                                                </td>

                                                <td>{{$account->todayCount}}</td>
                                                <td>{{$account->todayDeposit}}</td>
                                                <td>{{$account->todayWithdraw}}</td>
                                                <td>{{$account->todayNet}}</td>

                                                <td>
                                                    <form
                                                        action="{{ route('financier.transactions.assign.complete', $account->id) }}"
                                                        method="POST">
                                                        <input type="hidden" value="{{$transaction->id}}"
                                                               name="transaction_id">


                                                        @if(($bank === 'PAPARA') || (isset($fields) && array_key_exists('bank',$fields) && strtolower($fields['bank']) === strtolower($account->accountable->bank->name)))

                                                            <button type="submit" class="btn btn-primary btn-sm mt-1">
                                                                {{ csrf_field() }}
                                                                <i class="fas fa-check-circle">
                                                                </i>
                                                                {{$transaction->type->key === 'deposit' ? ' HESABI YOLLA' : 'PARA GONDERILDI'}}

                                                                <br>
                                                            </button>
                                                        @else

                                                            <button type="submit" class="btn btn-warning btn-sm mt-1">
                                                                {{ csrf_field() }}
                                                                <i class="fas fa-running">
                                                                </i>
                                                                {{$transaction->type->key === 'deposit' ? ' HESABI YOLLA' : 'PARA GONDERILDI'}}


                                                            </button>
                                                        @endif

                                                    </form>
                                                </td>

                                            </tr>
                                            @endforeach


                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="card-body">
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <h5><i class="icon fas fa-ban"></i> UYARI!</h5>
                                Uygun Banka Bulunamadi . Filtrelerinizi degistirerek yeniden deneyebilirsiniz !
                            </div>

                        </div>
                    @endif


                    {{--                    <div class="row">--}}
                    {{--                        <div class="col-12 table-responsive">--}}
                    {{--                            {{$accounts->links("pagination::bootstrap-4")}}--}}
                    {{--                        </div>--}}
                    {{--                    </div>--}}

                </div>

                <div class="d-flex justify-content-around bg-primary">

                    @if($transaction->status->key === 'waiting')

                        {{--                        <form action="{{ route('transactions.cancel', $transaction->id) }}" method="POST"--}}
                        {{--                              onsubmit="return confirmDelete('Islem Iptal Edilecek. Emin misiniz ?')">--}}
                        {{--                            @csrf--}}
                        {{--                            <div class="card-footer">--}}
                        {{--                                <button type="submit" class="btn btn-danger">Iptal Et</button>--}}
                        {{--                            </div>--}}
                        {{--                        </form>--}}


                        <div class="card-footer">

                            <a class="btn btn-danger"
                               data-toggle="modal"
                               onclick="showCancelModal({{$transaction->id}})"
                               data-target="#modal-lg">
                                {{ csrf_field() }}
                                <i class="fas fa-trash">
                                </i>
                                Iptal Et
                            </a>
                        </div>

                    @endif


                    <div class="card-footer">
                        <a href="{{url()->previous()}}" class="btn btn-info"
                           onclick="{{\Illuminate\Support\Facades\Redirect::back()}}">Geri Don</a>
                    </div>
                </div>


            </div>

        </div>

    </div>

    <div class="modal fade" id="modal-lg" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">ISLEM IPTAL !</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form
                    action="{{ route('financier.transactions.cancel') }}"
                    method="POST"
                    onsubmit="return confirmDelete('Islem Silinecek. Emin misiniz ?')">
                    @csrf
                    <div class="modal-body">

                        <input type="hidden" id="transaction_to_cancel" name="transaction_to_cancel">

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="owner">Detay Giriniz</label>
                                <textarea class="form-control" rows="3" placeholder=""
                                          name="message"
                                          required
                                ></textarea>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                        <button type="submit" class="btn btn-primary">Islemi Iptal Et</button>
                    </div>
                </form>

            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>


@endsection


@section('additional-js')
    <script>
        function handleClear() {
            const form = document.getElementById('filter-form');
            const bankId = document.getElementById('bank_id');
            const owner = document.getElementById('owner');
            const accNo = document.getElementById('accno');
            const iban = document.getElementById('iban');
            const branch = document.getElementById('branch');

            const theBankId = document.getElementById('theBankId');

            owner.value = '';
            accNo.value = '';
            iban.value = '';
            branch.value = '';
            bankId.value = theBankId
            form.submit();
        }
    </script>
@endsection


