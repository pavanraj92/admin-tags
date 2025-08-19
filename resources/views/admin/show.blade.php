@extends('admin::admin.layouts.master')

@section('title', 'Tags Management')

@section('page-title', 'Tag Details')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">
        <a href="{{ route('admin.tags.index') }}">Tag Manager</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Tag Details</li>
@endsection

@section('content')
    <!-- Container fluid  -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Header with Back button -->
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h4 class="card-title mb-0">{{ $tag->name ?? 'N/A' }} - Tag</h4>
                            <div>
                                <a href="{{ route('admin.tags.index') }}" class="btn btn-secondary ml-2">
                                    Back
                                </a>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Tag Information -->
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header bg-primary">
                                        <h5 class="mb-0 text-white font-bold">Tag Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Name:</label>
                                            <p>{{ $tag->name ?? 'N/A' }}</p>
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-bold">Slug:</label>
                                            <p>{{ $tag->slug ?? 'N/A' }}</p>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="font-weight-bold">Status:</label>
                                                    <p>{!! config('tag.constants.aryStatusLabel.' . $tag->status, 'N/A') !!}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="font-weight-bold">Created At:</label>
                                                    <p>
                                                        {{ $tag->created_at ? $tag->created_at->format(config('GET.admin_date_time_format') ?? 'Y-m-d H:i:s') : '—' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    @include('admin::admin.seo_meta_data.view', ['seo' => $seo])
                                </div>
                            </div>

                            <!-- SEO Meta Data -->
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header bg-primary">
                                        <h5 class="mb-0 text-white font-bold">Quick Actions</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex flex-column">
                                            @admincan('tags_manager_edit')
                                                <a href="{{ route('admin.tags.edit', $tag) }}" class="btn btn-warning mb-2">
                                                    <i class="mdi mdi-pencil"></i> Edit Tag
                                                </a>
                                            @endadmincan

                                            @admincan('tags_manager_delete')
                                                <button type="button" class="btn btn-danger delete-btn delete-record"
                                                    title="Delete this record"
                                                    data-url="{{ route('admin.tags.destroy', $tag) }}"
                                                    data-redirect="{{ route('admin.tags.index') }}"
                                                    data-text="Are you sure you want to delete this record?"
                                                    data-method="DELETE">
                                                    <i class="mdi mdi-delete"></i> Delete Tag
                                                </button>
                                            @endadmincan
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- row end -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Container fluid  -->
@endsection
