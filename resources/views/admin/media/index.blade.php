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


        <media-table></media-table>


    </section>
</div>


@endsection
