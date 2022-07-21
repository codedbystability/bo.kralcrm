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
                        <h6>Talep Bilgileri</h6>
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
                </div>

                @if(isset($bankInfo))

                    {{
    dd($bankInfo)
}}
                    <div class="card-body">
                        <div class="form-group">
                            <h6>Onaylanan Banka Bilgileri</h6>
                            <div class="row">
                                @foreach ($bankInfo as $key => $node)

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
                    </div>

                @endif


                @if(isset($notes) && count($notes) >= 1)

                    <div class="card-body">
                        <div class="form-group">
                            <h6>Islem Notlari</h6>
                            <div class="row">
                                @foreach ($notes as $key => $note)

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <p class="form-control">
                                                {{$note->message}}
                                            </p>
                                        </div>
                                    </div>


                                @endforeach
                            </div>
                        </div>
                    </div>

                @endif


                <div class="d-flex justify-content-around bg-primary">

                    @if($transaction->status->key === 'approved')

                            <div class="card-footer">

                                <button type="submit" class="btn btn-secondary btn-sm mt-1"
                                        onclick="
                                            document.getElementById('complete_transaction_id').value = {{$transaction->id}};
                                            document.getElementById('approved_amount').value = {{$transaction->amount}};"
                                        data-toggle="modal"
                                        data-target="#modal-lg-approve-payment">
                                    <i class="fas fa-wallet">
                                    </i>
                                    PARA HESABA GELDI !
                                </button>
                            </div>


{{--                        </form>--}}

                    @endif

                    @if($transaction->status->key === 'waiting')


                        <form
                            action="{{ route('financier.transactions.assign' ,$transaction->id )}}"
                            method="POST">
                            <input type="hidden" value="{{ $transaction->id }}" name="transaction_id">
                            <div class="card-footer">

                                <button type="submit" class="btn btn-success btn-sm mt-1">
                                    {{ csrf_field() }}
                                    <i class="fas fa-check-circle">
                                    </i>
                                    {{$transaction->type->key==='deposit' ? 'Hesap Yolla' : 'Islemi Tamamla'}}

                                </button>
                            </div>

                        </form>


                        <div style="" class="d-flex justify-content-center align-items-center pr-2 pl-2">
                            <button type="submit" class="btn btn-danger btn-sm "
                                    data-toggle="modal"
                                    onclick="showCancelModal({{$transaction->id}})"
                                    data-target="#modal-lg">
                                {{ csrf_field() }}
                                <i class="fas fa-trash">
                                </i>
                                Iptal Et
                            </button>
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

                        <input type="hidden" id="transaction_to_cancel" name="transaction_to_cancel"
                               value="{{$transaction->id}}">

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

    <div class="modal fade" id="modal-lg-approve-payment">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <form
                    action="{{ route('financier.transactions.complete', $transaction->id) }}"
                    onsubmit="return confirmDelete('Para sistem hesabina geldi olarak tamamlanacak! Emin misiniz?')"
                    method="POST">

                    @csrf

                    <div class="modal-body">
                        <label for="approved_amount">Miktar</label>


                        <input type="number" step=".01" class="form-control" name="approved_amount" id="approved_amount">
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
