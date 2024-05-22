@extends('admin.master')
@section('meta:title', 'Create Media Library')
@section('content')

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Add to Media Library</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Media Library</a></li>
                </ol>
            </div>
        </div>
    </section>

    <section class="content">
        <media-view
        submit-url="{{ route('admin.media.store') }}"
        ></media-view>
    </section>


</div>


@endsection

