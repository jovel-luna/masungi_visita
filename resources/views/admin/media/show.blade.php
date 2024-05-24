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
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Media Information</a></li>
                </ol>
            </div>
        </div>
    </section>
    <section class="content">
        <div style="display: flex; flex-direction: column; align-content: center; justify-content: center; align-items: center;">
            <img src={{$media->url}}>
            
        </div>
        <div>
            {{-- <a href={{ route('media.delete') }} >Delete media</a> --}}
            <form method="POST" action={{route('admin.media.delete') }}>
                {{ csrf_field() }}
                <input type="hidden" name="id" value={{$media->id}}>
                <button type="submit" class="btn-danger">Delete</button>
            </form>
        </div>
        
    </section>
</div>

@endsection
