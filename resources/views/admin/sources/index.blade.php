@extends('admin.master')

@section('meta:title', 'Sources')

@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Sources</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Sources</a></li>
                </ol>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="col-xs-12">
            <div class="card">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item"><a @click="initList('table-1')" class="nav-link active" href="#tab1" data-toggle="tab">Active</a></li>
                        <li class="nav-item"><a @click="initList('table-2')" class="nav-link" href="#tab2" data-toggle="tab">Archived</a></li>
                    </ul>
                </div><!-- /.card-header -->
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane show active" id="tab1">
                            <sources-table 
                            ref="table-1"
                            fetch-url="{{ route('admin.sources.fetch') }}"
                            ></sources-table>
                        </div>
                        <div class="tab-pane" id="tab2">
                            <sources-table
                            ref="table-2"
                            disabled
                            fetch-url="{{ route('admin.sources.fetch-archive') }}"
                            ></sources-table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@endsection