@extends('layouts.master')


@section('content-title')
    @include('layouts.content-title',['title' =>'Banka Hesaplari','first' => '','second' => ''])
@endsection

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">

                <div class="card-header text-right">
                    {{--                    @can('add bank account')--}}
                    {{--                        <a href="{{route('financier.banks.create')}}" type="button" class="btn btn-primary">--}}
                    {{--                            <i class="fa fa-plus-circle"></i> <strong>Yeni Kayit</strong>--}}
                    {{--                        </a>--}}
                    {{--                    @endcan--}}


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
                                            BANKA
                                        </th>


                                        <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1"
                                            colspan="1" aria-label="Engine version: activate to sort column ascending">
                                            AKTIF MI?
                                        </th>


                                        <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1"
                                            colspan="1" aria-label="CSS grade: activate to sort column ascending">
                                            ISLEMLER
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @foreach($data as $key=> $bank)
                                        <tr class="{{$key % 2 === 0 ? 'even':'odd'}}">
                                            <td>{{$bank->id}}</td>

                                            <td>{{$bank->name}}</td>
                                            {{--                                            <td>--}}
                                            {{--                                                <a href="">--}}
                                            {{--                                                    <img src="{{env('BO')}}" alt="">--}}
                                            {{--                                                </a>--}}
                                            {{--                                            </td>--}}

                                            <td>
                                                @if($bank->is_active)
                                                    <span class="badge bg-success"> EVET</span>
                                                @else

                                                    <span class="badge bg-danger"> HAYIR</span>
                                                @endif
                                            </td>

                                            <td class="project-actions d-flex justify-content-around">


                                                @can('delete bank account')

                                                    <form
                                                        action="{{ route('financier.banks.destroy', $bank->id) }}"
                                                        method="POST">
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            {{ method_field('DELETE') }}
                                                            {{ csrf_field() }}
                                                            <i class="fas fa-trash">
                                                            </i>
                                                            Sil
                                                        </button>
                                                    </form>

                                                    @if($bank->is_active)

                                                        <form
                                                            action="{{ route('financier.banks.passive', $bank->id) }}"
                                                            method="GET">
                                                            <button type="submit" class="btn btn-warning btn-sm">
                                                                <i class="fas fa-trash">
                                                                </i>
                                                                Pasif Et
                                                            </button>
                                                        </form>

                                                    @else

                                                        <form
                                                            action="{{ route('financier.banks.passive', $bank->id) }}"
                                                            method="GET">
                                                            <button type="submit" class="btn btn-success btn-sm">
                                                                {{ csrf_field() }}
                                                                <i class="fas fa-check-circle">
                                                                </i>
                                                                Aktif Et
                                                            </button>
                                                        </form>
                                                    @endif

                                                @endcan

                                            </td>


                                        </tr>
                                    @endforeach

                                    </tbody>

                                </table>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-sm-12 col-md-5">
                                <div class="dataTables_info" id="example1_info" role="status" aria-live="polite">
                                    Showing {{$data->currentPage() * $data->perPage()  - $data->perPage() }}
                                    to {{$data->currentPage() * $data->perPage()  - $data->perPage()  + $data->count() }}
                                    of {{$data->total()}} entries
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-7 d-flex justify-content-end">
                                {{$data->links("pagination::bootstrap-4")}}
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
