@extends('layouts.master')


@section('content-title')
    @include('layouts.content-title',['title' =>'','first' => '','second' => ''])
@endsection

@section('content')

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Islem Detay</h3>

            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-md-12  order-2 order-md-1">
                    <div class="row">
                        @foreach($widgets as $widget)
                            <div class="col-12 col-sm-3">
                                <div class="info-box {{$widget['bg']}}">
                                    <div class="info-box-content">
                                        <span class="info-box-text text-center text-bold" style="font-size: 10px;color: #bdbdbd">{{$widget['title']}}</span>
                                        <span
                                            class="info-box-number text-center font-weight-light mb-0" style="font-size: 16px">
                                                {{$widget['value']}}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>

                    <div class="row">
                        <div class="col-12 col-sm-12">
                            <div class="card card-default">
                                <div class="card-header">
                                    <h3 class="card-title">Istek Bilgileri</h3>

                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 d-flex flex-wrap">
                                            @foreach($request as $key=>$item)
                                                <div class="form-group {{$item['class']}}">
                                                    <label for="{{$key}}">{{$item['title']}}</label>
                                                    <input type="text" class="form-control" disabled name="{{$key}}"
                                                           value="{{$item['value']}}">
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                </div>
                            </div>
                        </div>
                    </div>
                    @if(isset($bank_info))
                        <div class="row">
                            <div class="col-12 col-sm-12">
                                <div class="card card-default">
                                    <div class="card-header">
                                        <h3 class="card-title">Banka Bilgileri</h3>

                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12 d-flex flex-wrap">
                                                @foreach($bank_info as $key=>$info)
                                                    <div class="form-group {{$info['class']}}">
                                                        <label for="{{$key}}">{{$info['title']}}</label>
                                                        <input type="text" class="form-control" disabled name="{{$key}}"
                                                               value="{{$info['value']}}">
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.card-body -->
                                    <div class="card-footer">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-12 col-sm-12">
                            <div class="card card-default">
                                <div class="card-header">
                                    <h3 class="card-title">Yonetici Notlari</h3>

                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 d-flex flex-wrap">
                                            @if($transaction->notes && count($transaction->notes)>=1)
                                                @foreach($transaction->notes as $key=>$note)
                                                    <div class="form-group col-md-12">
                                                    <span for="note-{{$key}}">
                                                        <span
                                                            style="font-weight: 600;font-size: 15px">{{$key + 1}}</span> - {{$note->financier->name}}</span>
                                                        <p class="form-control h-75"
                                                           disabled
                                                        >
                                                            {!! $note['message'] !!}
                                                        </p>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="form-group">

                                                    <p>Not bulunmamaktadir .</p>
                                                </div>

                                            @endif

                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
