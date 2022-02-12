@extends('layouts.master')


@section('content-title')
    @include('layouts.content-title',['title' =>'Ana Sayfa','first' => 'Ana Sayfa','second' => 'Raporlar'])
@endsection

@section('content')

    @if(isset($notes) && count($notes) >= 1)
        <div class="row ">
            <div class="col-12 col-sm-12">
                <div class="card card-default">
                    <div class="card-header  bg-info">
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
                            @foreach($notes as $key=>$note)
                                <div class="post p-3 col-lg-12">
                                    <div class="user-block">
                                        <span class="">
                                            <a href="#">{{$note->user->name}}</a>
                                        </span>
                                        <br>
                                        <span class="">{{$note->created_at}}</span>
                                    </div>
                                    <!-- /.user-block -->
                                    <p>
                                        {{$note['message']}}
                                    </p>

                                    <p>
                                    <span class="float-right">
                                      <a href="{{route('financier.notes.mark',$note->id)}}" class="link-black text-sm">
                                        <i class="fas fa-book-open mr-1">
                                            Okundu Isaretle
                                        </i>
                                      </a>
                        </span>
                                    </p>

                                </div>
                            @endforeach
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                    </div>
                </div>
            </div>
        </div>

    @endif





    @foreach($data as $item)

        <div class="card p-2 collapsed-card">
            <div class="card-header bg-primary">

                <div class="card-title">
                    {{$item['client_name']}}
                </div>

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
                    @foreach($item['statistics'] as $stat)
                        <div class="col-md-6">
                            <!-- Widget: user widget style 1 -->
                            <div class="card collapsed-card card-widget widget-user shadow">

                                <div class="card-header">
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <!-- Add the bg color to the header using any of the bg-* classes -->
                                <div class="widget-user-header bg-info">
                                    <h3 class="widget-user-username">{{$stat['title']}}</h3>
                                    {{--                                    <h5 class="widget-user-desc">Founder &amp; CEO</h5>--}}
                                    <img class="img-circle elevation-2" style="width: 50px"
                                         src="{{url('img/avatar.png')}}"
                                         alt="User Avatar">
                                </div>

                                <div class="card-footer">
                                    <div class="row">

                                        @foreach($stat['data'] as $key=>$statItem)

                                            <div
                                                class="{{$statItem['width']}} p-1 mt-2 {{$statItem['bg']}}  {{$key % 2 === 0 ? 'border-right' : ''}}">
                                                <div class="ribbon-wrapper">
                                                    <div class="ribbon {{$statItem['bg']}} ">
                                                        {{$key}}
                                                    </div>
                                                </div>
                                                <div class="description-block">
                                                    <h5 class="description-header">{{$statItem['data']}}</h5>
                                                    <span class="description-text">{{$statItem['title']}}</span>
                                                </div>
                                                <!-- /.description-block -->
                                            </div>
                                        @endforeach


                                    </div>
                                </div>
                            </div>
                        </div>

                    @endforeach

                </div>

            </div>

        </div>

    @endforeach


@endsection


