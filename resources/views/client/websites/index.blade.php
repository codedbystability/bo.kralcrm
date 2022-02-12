@extends('layouts.master')

@section('content-title')
    @include('layouts.content-title',['title' =>'Siteler','first' => '','second' => ''])
@endsection

@section('content')


    <div class="row">
        <div class="col-12">
            <div class="card">

                <div class="card-header text-right">
                    <a href="{{route('client.websites.create')}}" type="button" class="btn btn-primary">
                        <i class="fa fa-plus-circle"></i> <strong>Yeni Kayit</strong>
                    </a>


                </div>
                <div class="card-body">
                    <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">
                        <div class="row">
                            <div class="col-sm-12">
                                <table id="example1" class="table table-bordered table-striped dataTable dtr-inline"
                                       role="grid" aria-describedby="example1_info">
                                    <thead>
                                    <tr role="row">
                                        <th style="width: 10px">#</th>
                                        <th class="sorting sorting_asc" tabindex="0" aria-controls="example1"
                                            rowspan="1" colspan="1" aria-sort="ascending"
                                            aria-label="Rendering engine: activate to sort column descending">
                                            DOMAIN
                                        </th>

                                        <th class="sorting sorting_asc" tabindex="0" aria-controls="example1"
                                            rowspan="1" colspan="1" aria-sort="ascending"
                                            aria-label="Rendering engine: activate to sort column descending">
                                            SITE ID
                                        </th>

                                        <th class="sorting sorting_asc" tabindex="0" aria-controls="example1"
                                            rowspan="1" colspan="1" aria-sort="ascending"
                                            aria-label="Rendering engine: activate to sort column descending">
                                            SITE KEY
                                        </th>


                                        {{--                                        <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1"--}}
                                        {{--                                            colspan="1" aria-label="Engine version: activate to sort column ascending">--}}
                                        {{--                                            AKTIF MI?--}}
                                        {{--                                        </th>--}}


                                        <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1"
                                            colspan="1" aria-label="CSS grade: activate to sort column ascending">
                                            ISLEMLER
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @foreach($websites as $key=> $website)
                                        <tr class="{{$key % 2 === 0 ? 'even':'odd'}}">
                                            <td>{{$website->id}}</td>

                                            <td>{{$website->domain}}</td>
                                            <td>{{$website->sid}}</td>
                                            <td>{{$website->key}}</td>


                                            <td class="project-actions d-flex justify-content-around">

                                                <form
                                                    action="{{ route('client.websites.edit', $website->id) }}"
                                                    method="GET">
                                                    <button type="submit" class="btn btn-primary btn-sm mt-1">
                                                        <i class="fas fa-pencil-alt">
                                                        </i>
                                                        Duzenle
                                                    </button>
                                                </form>


                                                <form
                                                    action="{{ route('client.websites.destroy', $website->id) }}"
                                                    method="POST">
                                                    <button type="submit" class="btn btn-danger btn-sm mt-1">
                                                        {{ method_field('DELETE') }}
                                                        {{ csrf_field() }}
                                                        <i class="fas fa-trash">
                                                        </i>
                                                        Sil
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
                <!-- /.card-body -->
            </div>

        </div>
        <!-- /.col -->
    </div>

@endsection
