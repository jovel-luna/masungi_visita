@extends('admin.master')

@section('meta:title', 'Create Fee')

@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Create Fee</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.conservation-fees.index') }}">Fees</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Create</a></li>
                </ol>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <conservation-fee-view
        fetch-url="{{ route('admin.conservation-fees.fetch-item') }}"
        submit-url="{{ route('admin.conservation-fees.store') }}"
        ></conservation-fee-view>
    </section>
</div>

@endsection