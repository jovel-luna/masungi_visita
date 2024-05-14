@extends('web.master')

@section('meta:title', 'My Account')

@section('content')

<nav>
    <ol class="breadcrumb justify-content-end">
        <li class="breadcrumb-item"><a href="{{ route('web.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">My Account</li>
    </ol>
</nav>

<div class="container mb-5">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="row mb-2">
            <div class="col-sm-12">
                <h3>My Account</h3>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active" data-target="#tab1" href="javascript:void(0)" data-toggle="tab">Information</a></li>
                    <li class="nav-item"><a class="nav-link" data-target="#tab2" href="javascript:void(0)" data-toggle="tab">Change Password</a></li>
                     <li class="nav-item"><a @click="initList('table-1')" class="nav-link" data-target="#tab3" href="javascript:void(0)" data-toggle="tab">Activity Logs</a></li>
                </ul>
            </div><!-- /.card-header -->
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane show active" id="tab1">
                       <user-view
                       :editable="false"
                       fetch-url="{{ route('web.profiles.fetch') }}"
                       submit-url="{{ route('web.profiles.update') }}"
                       ></user-view>
                    </div>
                    <div class="tab-pane" id="tab2">
                        <change-password-form submit-url="{{ route('web.profiles.change-password') }}"></change-password-form>
                    </div>
                    <div class="tab-pane" id="tab3">
                        <activity-log-table 
                        ref="table-1"
                        disabled
                        fetch-url="{{ route('web.activity-logs.fetch.profiles') }}" 
                        hide-causer
                        ></activity-log-table>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>

@endsection