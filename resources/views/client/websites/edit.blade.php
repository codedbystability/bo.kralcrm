@extends('layouts.master')


@section('content-title')
    @include('layouts.content-title',['title' =>'Site Duzenleme','first' => '','second' => ''])
@endsection

@section('content')


    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">

                <!-- form start -->
                <form method="POST" action="{{ route('websites.update', $website->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="card-body">


                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="domain">SITE DOMAIN</label>
                                        <input name="domain" class="form-control"
                                               type="text"
                                               placeholder="https://domain.com"
                                               required
                                               value="{{$website->domain}}"
                                        >
                                    </div>

                                </div>

                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="key">SITE ID</label>
                                        <input name="key" class="form-control"
                                               type="text"
                                               value="{{$website->sid}}"
                                               disabled
                                        >
                                    </div>
                                </div>


                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="owner">SITE KEY</label>
                                        <input name="sid" class="form-control"
                                               type="text"
                                               value="{{$website->key}}"
                                               disabled
                                        >
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
