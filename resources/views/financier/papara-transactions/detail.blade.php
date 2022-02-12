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

                                @if(gettype($node) !== 'string')   @continue @endif

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
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <table class="table table-striped projects">
                                <thead>
                                <th></th>
                                <th>BANKA</th>
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
                                    <tr>
                                        <td>
                                            {{$account->accountable->id}}
                                        </td>
                                        <td>
                                            {{$transaction->method->key === 'papara' ? 'PAPARA' : $account->bank->name }}
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
                                                action="{{ route('transactions.assign.complete', $account->id) }}"
                                                method="POST">
                                                <input type="hidden" value="{{$transaction->id}}" name="transaction_id">
                                                <button type="submit" class="btn btn-success btn-sm mt-1">
                                                    {{ csrf_field() }}
                                                    <i class="fas fa-trash">
                                                    </i>
                                                    HESABI YOLLA
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

                <div class="d-flex justify-content-around bg-primary">

                    @if($transaction->status->key === 'waiting')
                        <form action="{{ route('transactions.approve', $transaction->id) }}" method="POST"
                              onsubmit="return confirmDelete('Islem Onaylanacak. Emin misiniz ?')">
                            @csrf
                            <div class="card-footer">
                                <button type="submit" class="btn btn-success">Onayla</button>
                            </div>
                        </form>


                        <form action="{{ route('transactions.cancel', $transaction->id) }}" method="POST"
                              onsubmit="return confirmDelete('Islem Iptal Edilecek. Emin misiniz ?')">
                            @csrf
                            <div class="card-footer">
                                <button type="submit" class="btn btn-danger">Iptal Et</button>
                            </div>
                        </form>

                    @endif


                    <div class="card-footer">
                        <a href="{{url()->previous()}}" class="btn btn-info"
                           onclick="{{\Illuminate\Support\Facades\Redirect::back()}}">Geri Don</a>
                    </div>
                </div>


            </div>

        </div>

    </div>



@endsection
