@extends('admin::admin.layouts.master')

@section('title', 'Tag Management')

@section('page-title', isset($tag) ? 'Edit Tag' : 'Create Tag')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('admin.tags.index') }}">Tag Manager</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ isset($tag) ? 'Edit Tag' : 'Create Tag' }}</li>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Start Tag Content -->
        <form action="{{ isset($tag) ? route('admin.tags.update', $tag->id) : route('admin.tags.store') }}" method="POST"
            id="tagForm">
            @if (isset($tag))
                @method('PUT')
            @endif
            @csrf
            <div class="row">
                <div class="col-8">
                    <div class="card card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Name<span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ $tag?->name ?? old('name') }}" required>
                                    @error('name')
                                        <div class="text-danger validation-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Status<span class="text-danger">*</span></label>
                                    <select name="status" class="form-control select2" required>
                                        <option value="1"
                                            {{ ($tag?->status ?? old('status')) == '1' ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="0"
                                            {{ ($tag?->status ?? old('status')) == '0' ? 'selected' : '' }}>Inactive
                                        </option>
                                    </select>
                                    @error('status')
                                        <div class="text-danger validation-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary"
                                id="saveBtn">{{ isset($tag) ? 'Update' : 'Save' }}</button>
                            <a href="{{ route('admin.tags.index') }}" class="btn btn-secondary">Back</a>
                        </div>

                    </div>
                </div>
                <div class="col-md-4">
                    @include('admin::admin.seo_meta_data.seo', ['seo' => $seo ?? null])
                </div>
            </div>
        </form>
        <!-- End Tag Content -->
    </div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('backend/custom.css') }}">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2();

            $('#tagForm').validate({
                ignore: [],
                rules: {
                    name: {
                        required: true,
                        minlength: 2
                    }
                },
                messages: {
                    name: {
                        required: "Please enter a tag name",
                        minlength: "Name must be at least 2 characters"
                    }
                },
                submitHandler: function(form) {
                    const $btn = $('#saveBtn');
                    $btn.prop('disabled', true).text($btn.text().trim().toLowerCase() === 'update' ?
                        'Updating...' : 'Saving...');
                    form.submit();
                },
                errorElement: 'div',
                errorClass: 'text-danger custom-error',
                errorPlacement: function(error, element) {
                    $('.validation-error').hide();
                    error.insertAfter(element);
                }
            });
        });
    </script>
@endpush
