@extends('layouts.master')


@section('content-title')
    @include('layouts.content-title',['title' =>'Finansci Listesi','first' => '','second' => ''])
@endsection

@section('content')


    <div class="row">
        <div class="col-12">
            <div class="card">

                <div class="card-header text-right">
                    <a href="{{route('admin.financiers.create')}}" type="button" class="btn btn-primary">
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
                                            ISIM
                                        </th>

                                        <th class="sorting sorting_asc" tabindex="0" aria-controls="example1"
                                            rowspan="1" colspan="1" aria-sort="ascending"
                                            aria-label="Rendering engine: activate to sort column descending">
                                            KULLANICI ADI
                                        </th>


                                        <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1"
                                            colspan="1" aria-label="CSS grade: activate to sort column ascending">
                                            ISLEMLER
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @foreach($financiers as $key=> $financier)
                                        <tr class="{{$key % 2 === 0 ? 'even':'odd'}}">

                                            <td>{{$financier->id}}</td>
                                            <td>{{$financier->name}}</td>
                                            <td>{{$financier->username}}</td>


                                            <td class="project-actions d-flex justify-content-around">

                                                <form
                                                    action="{{ route('admin.financiers.edit', $financier->id) }}"
                                                    method="GET">
                                                    <button type="submit" class="btn btn-primary btn-sm mt-1">
                                                        <i class="fas fa-pencil-alt">
                                                        </i>
                                                        Duzenle
                                                    </button>
                                                </form>


                                                <form action="{{ route('admin.financiers.destroy', $financier->id) }}"
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
