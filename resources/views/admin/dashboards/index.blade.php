@extends('admin.master')

@section('meta:title', 'Dashboard')

@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Dashboard</h1>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active" data-target="#tab1" href="javascript:void(0)" data-toggle="tab">Overall Analytics</a></li>
                </ul>
            </div><!-- /.card-header -->
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane show active" id="tab1">
                        <dashboard-analytics
                        :destinations="{{ $destinations }}" 
                        fetch-url="{{ route('admin.analytics.fetch.user') }}"
                        ></dashboard-analytics>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@endsection