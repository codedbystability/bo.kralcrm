@extends('layouts.master')


@section('content-title')
    @include('layouts.content-title',['title' =>'Not Gir','first' => '','second' => ''])
@endsection

@section('content')


    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">

                <!-- form start -->
                <form method="POST" action="{{ route('admin.managers.notes.store') }}">
                    @csrf
                    <div class="card-body">


                        <div class="form-group">
                            <label for="provider_id">Notu Gorecek Kisiler</label>
                            <select data-placeholder="Secilen grubun tum elemanlari icin bos birakiniz ..."
                                    multiple
                                    class="chosen-select form-control"
                                    name="id_list[]">
                                @foreach($financiers as $financier)
                                    <option value="{{$financier->id}}">{{$financier->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="owner">Not Giriniz !</label>
                                        <textarea name="message" class="form-control col-lg-12" id="" cols=""
                                                  required
                                                  rows=""></textarea>
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
