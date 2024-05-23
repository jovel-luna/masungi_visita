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
                <a href="{{ route('admin.media.create') }}" class="btn btn-primary text-white">
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
                                    <td>
                                        <div id={{ $item->id }}>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                <path
                                                    d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z" />
                                                <path
                                                    d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z" />
                                            </svg>
                                        </div>
                                    </td>
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
