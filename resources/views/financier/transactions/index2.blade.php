@extends('layouts.master')


@section('content-title')
    @include('layouts.content-title',['title' => $title,'first' => '','second' => ''])
@endsection

@section('content')

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
                                                    <h5>--</h5>
                                                </td>

                                                <td>--</td>
                                                <td>{{$transaction->amount}} {{$transaction->currency_code}}</td>

                                                <td>--</td>
                                                <td>--</td>


                                                <td class="project-actions flex-row flex-lg-wrap">

                                                    <form
                                                        action="{{ route('financier.transactions.detail', $transaction->id) }}"
                                                        method="GET">
                                                        <button type="submit" class="btn btn-info btn-sm mt-1">
                                                            <i class="fas fa-eye">
                                                            </i>
                                                            Talep Bilgileri

                                                        </button>
                                                    </form>

                                                </td>

                                                <td>
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
                                                </td>
                                            </tr>
                                        @endforeach

                                        </tbody>

                                    </table>
                                </div>
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
