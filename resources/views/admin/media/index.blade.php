@extends('admin.master')

@section('meta:title', 'Media Library')

@section('content')


    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Media Library</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Media Library</a></li>
                    </ol>
                </div>
            </div>

            <div class="mb-4">
                <a href="" class="btn btn-primary text-white">
                    <i class="fa fa-plus"></i>
                    Add Media
                </a>
            </div>

        </section>

        <!-- Main content -->
        <section class="content">

            {{-- <media-table
        fetch-url="{{ route('admin.media.fetchall') }}"
        ></media-table> --}}

            <div class="table-responsive" style="overflow: auto hidden;">
                <table class="table table-hover table-striped table-bordered text-center" style="white-space: nowrap;">
                    <thead>
                        <tr>
                            <th><span>Ref ID</span></th>
                            <th><span>Media Name</span></th>
                            <th><span>Media URL</span></th>
                            <th><span>Created At</span></th>
                            <th>
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($media) > 0)
                            @foreach ($media as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->url }}</td>
                                    <td>{{ $item->created_at }}</td>
                                    <td></td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5">No Results Found</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>



        </section>
    </div>


@endsection
