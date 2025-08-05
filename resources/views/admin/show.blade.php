@extends('admin::admin.layouts.master')

@section('title', 'Tags Management')

@section('page-title', 'Tag Details')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('admin.tags.index') }}">Tag Manager</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tag Details</li>
@endsection

@section('content')
    <!-- Container fluid  -->
    <div class="container-fluid">
        <!-- Start Tag Content -->
        <div class="row">
            <div class="col-8">
                <div class="card">                    
                    <div class="table-responsive">
                         <div class="card-body">      
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <th scope="row">Name</th>
                                        <td scope="col">{{ $tag->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Slug</th>
                                        <td scope="col">{{ $tag->slug ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Status</th>
                                        <td scope="col"> {!! config('tag.constants.aryStatusLabel.' . $tag->status, 'N/A') !!}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Created At</th>
                                        <td scope="col">{{ $tag->created_at
                                            ? $tag->created_at->format(config('GET.admin_date_time_format') ?? 'Y-m-d H:i:s')
                                            : '—' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                                             
                            <a href="{{ route('admin.tags.index') }}" class="btn btn-secondary">Back</a>
                        </div>
                    </div>
                </div>
            </div>
             <div class="col-md-4">
                @include('admin::admin.seo_meta_data.view', ['seo' => $seo])
             </div>
        </div>
        <!-- End Tag Content -->
    </div>
    <!-- End Container fluid  -->
@endsection
