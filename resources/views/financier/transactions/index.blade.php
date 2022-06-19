@extends('layouts.master')


@section('content-title')
    @include('layouts.content-title',['title' => $title,'first' => '','second' => ''])
@endsection

@section('content')

    <div class="alert alert-danger alert-dismissible" style="display: none" id="alert-container">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h5>
            <i class="icon fas fa-info"></i>
            <span id="alert-title"> Islem bekleyen odemeler mevcut !!!</span>

        </h5>
        <span id="alert-message">Info alert preview. This alert is dismissable.</span>
    </div>


    <div class="row">
        <div class="col-12">
            <div class="card">


                <div class="card-body">
                    <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="table-responsive">
                                    <table id="example1" class="table table-bordered table-striped dataTable dtr-inline"
                                           role="grid" aria-describedby="example1_info">
                                        <thead>
                                        <tr role="row">
                                            <th class="sorting sorting_asc" tabindex="0" aria-controls="example1"
                                                rowspan="1" colspan="1" aria-sort="ascending"
                                                aria-label="Rendering engine: activate to sort column descending">
                                                #
                                            </th>

                                            <th class="sorting sorting_asc" tabindex="0" aria-controls="example1"
                                                rowspan="1" colspan="1" aria-sort="ascending"
                                                aria-label="Rendering engine: activate to sort column descending">
                                                METHOD
                                            </th>

                                            <th class="sorting sorting_asc" tabindex="0" aria-controls="example1"
                                                rowspan="1" colspan="1" aria-sort="ascending"
                                                aria-label="Rendering engine: activate to sort column descending">
                                                BANKA
                                            </th>

                                            <th class="sorting sorting_asc" tabindex="0" aria-controls="example1"
                                                rowspan="1" colspan="1" aria-sort="ascending"
                                                aria-label="Rendering engine: activate to sort column descending">
                                                AD-SOYAD
                                            </th>

                                            <th class="sorting sorting_asc" tabindex="0" aria-controls="example1"
                                                rowspan="1" colspan="1" aria-sort="ascending"
                                                aria-label="Rendering engine: activate to sort column descending">
                                                MIKTAR
                                            </th>

                                            <th class="sorting sorting_asc" tabindex="0" aria-controls="example1"
                                                rowspan="1" colspan="1" aria-sort="ascending"
                                                aria-label="Rendering engine: activate to sort column descending">
                                                TIP
                                            </th>

                                            <th class="sorting sorting_asc" tabindex="0" aria-controls="example1"
                                                rowspan="1" colspan="1" aria-sort="ascending"
                                                aria-label="Rendering engine: activate to sort column descending">
                                                DURUM
                                            </th>


                                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1"
                                                colspan="1" aria-label="CSS grade: activate to sort column ascending">
                                                ISLEMLER
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        @foreach($transactions as $key=> $transaction)
                                            <tr class="{{$key % 2 === 0 ? 'even':'odd'}}"
                                                id="waiting-deposits-{{$transaction->id}}">

                                                <td class="sorting_1 dtr-control">
                                                    <h5>#{{$transaction->id}}</h5>
                                                </td>
                                                <td class="sorting_1 dtr-control">
                                                    <h5>{{$transaction->method->name}}</h5>
                                                </td>
                                                <td>{{$transaction->transactionable && $transaction->transactionable->bank?($transaction->transactionable->bank):'PAPARA'}}</td>
                                                <td>{{$transaction->transactionable && $transaction->transactionable->fullname?($transaction->transactionable->fullname):'PAPARA'}}</td>
                                                <td>{{$transaction->amount}} {{$transaction->currency_code}}</td>
                                                <td>{{\App\Enums\TransactionTypeEnum::get($transaction->type->key)}}</td>
                                                <td>{{\App\Enums\TransactionStatusEnum::get($transaction->status->key)}}</td>


                                                <td class="project-actions flex-row flex-lg-wrap">


                                                    @if($transaction->status->key === 'waiting')
                                                        @can('approve ' . $permissionKey)
                                                            <form
                                                                action="{{ route('financier.transactions.assign', $transaction->id) }} "
                                                                method="POST">
                                                                <button type="submit"
                                                                        class="btn btn-success btn-sm mt-1">
                                                                    {{ csrf_field() }}
                                                                    <i class="fas fa-check-circle">
                                                                    </i>
                                                                    {{$transaction->type->key==='deposit' ? 'Hesap Yolla' : 'Islemi Tamamla'}}

                                                                </button>
                                                            </form>

                                                            {{--                                                            @if($transaction->type->key === 'deposit')--}}
                                                            {{--                                                                <form--}}
                                                            {{--                                                                    action="{{ route('financier.transactions.oto-assign', $transaction->id) }}"--}}
                                                            {{--                                                                    method="POST"--}}
                                                            {{--                                                                    onsubmit="return confirmDelete('Islem Onaylanacak. Emin misiniz ?')">--}}
                                                            {{--                                                                    <button type="submit"--}}
                                                            {{--                                                                            class="btn btn-info btn-sm mt-1">--}}
                                                            {{--                                                                        {{ csrf_field() }}--}}
                                                            {{--                                                                        <i class="fas fa-dolly">--}}
                                                            {{--                                                                        </i>--}}
                                                            {{--                                                                        Oto Hesap At !--}}
                                                            {{--                                                                    </button>--}}
                                                            {{--                                                                </form>--}}
                                                            {{--                                                            @endif--}}

                                                        @endcan


                                                        @can('cancel ' . $permissionKey)
                                                            <button type="submit" class="btn btn-danger btn-sm mt-1"
                                                                    data-toggle="modal"
                                                                    onclick="showCancelModal({{$transaction->id}})"
                                                                    data-target="#modal-lg">
                                                                {{ csrf_field() }}
                                                                <i class="fas fa-trash">
                                                                </i>
                                                                Iptal Et
                                                            </button>
                                                        @endcan

                                                    @elseif($transaction->status->key === 'approved')

                                                        @can('cancel ' . $permissionKey)

                                                            {{--                                                        && \Carbon\Carbon::now()->diffInSeconds($transaction->edit_time) < 120--}}
                                                            @if( !$transaction->direct_approve && $transaction->status->key === 'completed' && $transaction->edit_time )
                                                                <button type="submit" class="btn btn-danger btn-sm mt-1"
                                                                        data-toggle="modal"
                                                                        onclick="showCancelModal({{$transaction->id}})"
                                                                        data-target="#modal-lg">
                                                                    {{ csrf_field() }}
                                                                    <i class="fas fa-trash">
                                                                    </i>
                                                                    Iptal Et
                                                                </button>

                                                                @if(!$transaction->direct_approve)

                                                                    <a href="{{route('financier.transactions.direct-approve',['id' => $transaction->id])}}"
                                                                       class="btn btn-outline-danger btn-sm mt-1">
                                                                        <i class="fas fa-bomb">
                                                                        </i>
                                                                        Beklemeden Onayla !
                                                                    </a>
                                                                @endif
                                                            @endif



                                                            <form
                                                                action="{{ route('financier.transactions.bank-info', $transaction->id) }}"
                                                                method="POST">
                                                                <button type="submit"
                                                                        class="btn btn-success btn-sm mt-1">
                                                                    {{ csrf_field() }}
                                                                    <i class="fas fa-eye">
                                                                    </i>
                                                                    Banka Bilgileri
                                                                </button>
                                                            </form>

                                                        @endcan

                                                    @endif



                                                    @can('observe ' . $permissionKey)
                                                        <form
                                                            action="{{ route('financier.transactions.detail', $transaction->id) }}"
                                                            method="GET">
                                                            <button type="submit" class="btn btn-info btn-sm mt-1">
                                                                <i class="fas fa-eye">
                                                                </i>
                                                                Talep Bilgileri

                                                            </button>
                                                        </form>
                                                    @endcan

                                                    @if($transaction->status->key ==='completed')
                                                        <form
                                                            action="{{ route('financier.transactions.letclient', $transaction->id) }} "
                                                            method="GET">
                                                            <button type="submit"
                                                                    class="btn btn-warning btn-sm mt-1">
                                                                <i class="fas fa-check-circle">
                                                                </i>
                                                                Yeniden Bilgilendir

                                                            </button>
                                                        </form>
                                                    @endif

                                                    @if($transaction->status->key === 'approved')

                                                        @can('approve ' . $permissionKey)
                                                            <button type="submit"
                                                                    class="btn btn-outline-danger btn-sm mt-1"
                                                                    onclick="
                                                                        document.getElementById('complete_transaction_id').value = {{$transaction->id}};
                                                                        document.getElementById('approved_amount').value = {{$transaction->amount}};"
                                                                    data-toggle="modal"
                                                                    data-target="#modal-lg-approve-payment">
                                                                <i class="fas fa-wallet">
                                                                </i>
                                                                PARA GELDI !
                                                            </button>
                                                        @endcan

                                                        @can('cancel ' . $permissionKey)
                                                            <button type="submit" class=" btn btn-danger btn-sm mt-1"
                                                                    data-toggle="modal"
                                                                    onclick="showCancelModal({{$transaction->id}})"
                                                                    data-target="#modal-lg">
                                                                {{ csrf_field() }}
                                                                <i class="fas fa-trash">
                                                                </i>
                                                                Iptal Et
                                                            </button>
                                                        @endcan

                                                    @endif

                                                    @if($transaction->status->key === 'completed')

                                                        @can('cancel ' . $permissionKey)
                                                            <button type="submit" class=" btn btn-danger btn-sm mt-1"
                                                                    data-toggle="modal"
                                                                    onclick="showCancelModal({{$transaction->id}})"
                                                                    data-target="#modal-lg">
                                                                {{ csrf_field() }}
                                                                <i class="fas fa-trash">
                                                                </i>
                                                                Iptal Et
                                                            </button>
                                                        @endcan

                                                    @endif


                                                    <button type="submit" class="btn btn-primary btn-sm mt-1"
                                                            onclick="document.getElementById('transaction_id').value = {{$transaction->id}}"
                                                            data-toggle="modal"
                                                            data-target="#modal-lg-note">
                                                        <i class="fas fa-pen">
                                                        </i>
                                                        Not Ekle
                                                    </button>

                                                </td>
                                            </tr>
                                        @endforeach

                                        </tbody>

                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 table-responsive">
                                {{$transactions->links("pagination::bootstrap-4")}}
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>

        </div>
        <!-- /.col -->
    </div>

    <div class="modal fade" id="modal-lg" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">ISLEM IPTAL !</h6>
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
            {{--            <button id="audio-button" onclick="playAudio()" style="visibility: hidden">--}}
            {{--            </button>--}}
            {{--            <audio id="audio" type="audio/mp3" src="http://127.0.0.1:8000/audio/notification.mp3" autoplay="true" muted="true"/>--}}

            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <div class="modal fade" id="modal-lg-note">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <form action="{{route('financier.transactions.add-note')}}" method="POST">
                    @csrf

                    <div class="modal-body">
                        <label for="note">Note Giriniz</label>
                        <textarea class="form-control" name="note" required id="note" cols="30" rows="10"></textarea>
                        <input type="text" style="display: none" name="transaction_id" id="transaction_id">
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Geri Don</button>
                        <button type="submit" class="btn btn-primary">Kaydet</button>
                    </div>
                </form>

            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <div class="modal fade" id="modal-lg-approve-payment">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <form
                    action="{{ route('financier.transactions.complete', isset($transaction) ? $transaction->id : 1) }}"
                    onsubmit="return confirmDelete('Para sistem hesabina geldi olarak tamamlanacak! Emin misiniz?')"
                    method="POST">

                    @csrf

                    <div class="modal-body">
                        <label for="approved_amount">Miktar</label>


                        <input type="number" class="form-control" name="approved_amount" id="approved_amount">
                        <span style="color: red;font-size: 10px">*** Gerçekleşen tutar belirtilen tutardan farklıysa lütfen gerçekleşen tutarı giriniz</span>

                        <input type="text" class="form-control" style="display: none" name="complete_transaction_id"
                               id="complete_transaction_id">
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Geri Don</button>
                        <button type="submit" class="btn btn-primary">Kaydet</button>
                    </div>
                </form>

            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

@endsection


@section('additional-js')

    <script type="text/javascript">

        function playAudio() {
            const audio = new Audio('{{env('BASE_URL')}}audio/notification-2.mp3')
            audio.volume = 1;
            audio.play()
        }

        $(document).ready(function () {
            const waitingDeposits = $('[id^="waiting-deposits-"]');
            if (waitingDeposits.length >= 1 && window.location.pathname === '/financier/transactions/havale/waiting-deposits') {
                //BEKLEYEN DEPOSIT VAR ALARM CAL !
                const alertContainer = document.getElementById('alert-container');
                alertContainer.style.display = 'block';
                const alertTitle = document.getElementById('alert-message');
                alertTitle.innerHTML = waitingDeposits.length + ' Adet Islem Bekleme Durumuda !!!'


                playAudio();
            }

        });

        let count = 0
        setInterval(function () {
            count++;
            if (window.location.pathname === '/financier/transactions/havale/waiting-deposits' && count % 30 === 0) {
                location.reload();
            }
        }, 1000);


    </script>
@endsection
