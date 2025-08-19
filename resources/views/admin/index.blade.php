@extends('admin::admin.layouts.master')

@section('title', 'Tag Management')

@section('page-title', 'Tag Manager')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Tag Manager</li>
@endsection

@section('content')
    <!-- Container fluid  -->
    <div class="container-fluid">
        <!-- Start Tag Content -->
        <div class="row">
            <div class="col-12">
                <div class="card card-body">
                    <h4 class="card-title">Filter</h4>
                    <form action="{{ route('admin.tags.index') }}" method="GET" id="filterForm">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" name="keyword" id="keyword" class="form-control"
                                        value="{{ app('request')->query('keyword') }}" placeholder="Enter tag name">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control select2">
                                        <option value="">All</option>
                                        @foreach (config('tag.constants.status') as $key => $label)
                                            <option value="{{ $key }}"
                                                {{ app('request')->query('status') === (string) $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-auto mt-1 text-right">
                                <div class="form-group">
                                    <label for="created_at">&nbsp;</label>
                                    <button type="submit" form="filterForm" class="btn btn-primary mt-4">Filter</button>
                                    <a href="{{ route('admin.tags.index') }}" class="btn btn-secondary mt-4">Reset</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @admincan('tags_manager_create')
                            <div class="text-right">
                                <a href="{{ route('admin.tags.create') }}" class="btn btn-primary mb-3">Create New Tag</a>
                            </div>
                        @endadmincan

                        <div class="table-responsive">
                            <table class="table">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">S. No.</th>
                                        <th scope="col">@sortablelink('name', 'Name', [], ['class' => 'text-dark'])</th>
                                        <th scope="col">@sortablelink('status', 'Status', [], ['class' => 'text-dark'])</th>
                                        <th scope="col">@sortablelink('created_at', 'Created At', [], ['class' => 'text-dark'])</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($tags) && $tags->count() > 0)
                                        @php
                                            $i = ($tags->currentPage() - 1) * $tags->perPage() + 1;
                                        @endphp
                                        @foreach ($tags as $tag)
                                            <tr>
                                                <th scope="row">{{ $i }}</th>
                                                <td>{{ $tag->name }}</td>
                                                <td>
                                                    @if ($tag->status == '1')
                                                        <a href="javascript:void(0)" data-toggle="tooltip"
                                                            data-placement="top" title="Click to change status to inactive"
                                                            data-url="{{ route('admin.tags.updateStatus') }}"
                                                            data-method="POST" data-status="0"
                                                            data-id="{{ $tag->id }}"
                                                            class="btn btn-success btn-sm update-status">Active</a>
                                                    @else
                                                        <a href="javascript:void(0)" data-toggle="tooltip"
                                                            data-placement="top" title="Click to change status to active"
                                                            data-url="{{ route('admin.tags.updateStatus') }}"
                                                            data-method="POST" data-status="1"
                                                            data-id="{{ $tag->id }}"
                                                            class="btn btn-warning btn-sm update-status">Inactive</a>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $tag->created_at ? $tag->created_at->format(config('GET.admin_date_time_format') ?? 'Y-m-d H:i:s') : '—' }}
                                                </td>
                                                <td style="width: 10%;">
                                                    @admincan('tags_manager_view')
                                                        <a href="{{ route('admin.tags.show', $tag) }}" data-toggle="tooltip"
                                                            data-placement="top" title="View this record"
                                                            class="btn btn-warning btn-sm"><i class="mdi mdi-eye"></i></a>
                                                    @endadmincan
                                                    @admincan('tags_manager_edit')
                                                        <a href="{{ route('admin.tags.edit', $tag) }}" data-toggle="tooltip"
                                                            data-placement="top" title="Edit this record"
                                                            class="btn btn-success btn-sm"><i class="mdi mdi-pencil"></i></a>
                                                    @endadmincan
                                                    @admincan('tags_manager_delete')
                                                        <a href="javascript:void(0)" data-toggle="tooltip" data-placement="top"
                                                            title="Delete this record"
                                                            data-url="{{ route('admin.tags.destroy', $tag) }}"
                                                            data-text="Are you sure you want to delete this record?"
                                                            data-method="DELETE" class="btn btn-danger btn-sm delete-record"><i
                                                                class="mdi mdi-delete"></i></a>
                                                    @endadmincan
                                                </td>
                                            </tr>
                                            @php
                                                $i++;
                                            @endphp
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5" class="text-center">No records found.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>

                            @if ($tags->count() > 0)
                                {{ $tags->links('admin::pagination.custom-admin-pagination') }}
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Tag Content -->
    </div>
    <!-- End Container fluid  -->
@endsection
