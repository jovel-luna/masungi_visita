@extends('admin.master')

@section('meta:title', 'Create Age Range')

@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Create Age Range</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.age-ranges.index') }}">Age Ranges</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Create</a></li>
                </ol>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <age-range-view
        fetch-url="{{ route('admin.age-ranges.fetch-item') }}"
        submit-url="{{ route('admin.age-ranges.store') }}"
        ></age-range-view>
    </section>
</div>

@endsection