@extends('layouts.master')


@section('content-title')
    @include('layouts.content-title',['title' =>'Finansci Ekle','first' => 'Agreements','second' => 'Create'])
@endsection

@section('content')

    <form method="POST" action="{{ route('admin.financiers.customer-store') }}" onsubmit="return setPermissionsArray()">

        @csrf

        <input type="hidden" id="financier_id" name="financier_id" value="{{$financier->id}}">

        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">

                    <div class="card-body">


                        <div class="form-group">
                            <div class="row">

                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="username">FINANSCI</label>
                                        <input name="username" class="form-control"
                                               type="text"
                                               disabled="disabled"
                                               required
                                               value="{{$financier->name}}"
                                        >
                                    </div>
                                </div>


                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="name">MUSTERI LISTESI</label>

                                        <select class="form-control" name="clients[]" id="clients" multiple>

                                            @foreach($allClients as $client)

                                                <option value="{{$client->id}}">{{$client->username}}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                </div>

                            </div>
                        </div>


                    </div>


                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">Kaydet</button>
                    </div>

                </div>


            </div>

        </div>

    </form>

@endsection
