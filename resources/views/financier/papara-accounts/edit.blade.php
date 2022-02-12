@extends('layouts.master')


@section('content-title')
    @include('layouts.content-title',['title' =>'Papara Hesap Guncelle','first' => '','second' => ''])
@endsection

@section('content')


    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">

                <!-- form start -->
                <form method="POST" action="{{ route('financier.papara-accounts.update',$bankAccount->id) }}">
                    @csrf
                    {{ method_field('PUT') }}
                    <div class="card-body">


                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="owner">HESAP SAHIBI</label>
                                        <input name="owner" class="form-control"
                                               type="text"
                                               placeholder="hesap sahibi"
                                               required
                                               value="{{$bankAccount->owner}}"
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
                                               value="{{$bankAccount->accno}}"
                                        >
                                    </div>

                                </div>


                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="min_withdraw">MIN CEKIM</label>
                                        <input name="min_withdraw" class="form-control" type="number" step="0.1"
                                               placeholder="min cekim"
                                               value="{{$bankAccount->min_withdraw}}"
                                               required>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="max_withdraw">MAX CEKIM</label>
                                        <input name="max_withdraw" class="form-control" type="number"
                                               value="{{$bankAccount->max_withdraw}}"
                                               step="0.1"
                                               placeholder="max cekim" required>
                                    </div>
                                </div>


                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="min_deposit">MIN YATIRIM</label>
                                        <input name="min_deposit" class="form-control" type="number"
                                               value="{{$bankAccount->min_deposit}}"
                                               step="0.1"
                                               placeholder="min yatirim" required>
                                    </div>
                                </div>


                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="max_deposit">MAX YATIRIM</label>
                                        <input name="max_deposit" class="form-control" type="text"
                                               placeholder="max yatirim"
                                               value="{{$bankAccount->max_deposit}}"
                                               step="0.1"
                                               required>
                                    </div>
                                </div>


                            </div>
                        </div>


                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">Guncelle</button>
                    </div>
                </form>
            </div>

        </div>

    </div>



@endsection
