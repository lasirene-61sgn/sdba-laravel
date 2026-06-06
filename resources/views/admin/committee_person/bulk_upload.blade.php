@extends('admin.layout.app') 

@define('title', 'Bulk Upload Committee Members')

@section('content')
<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Bulk Upload Committee Members</h4>
            <a href="{{ route('admin.committee.index') }}" class="btn btn-light btn-sm">Back to List</a>
        </div>
        <div class="card-body">
            
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="alert alert-info">
                <h5><i class="fas fa-info-circle"></i> CSV Formatting Rules:</h5>
                <p class="mb-1">Your CSV file <strong>must</strong> include the following exact headers in the first row:</p>
                <code>name,phone,password,post_name,sort_order,status,state</code>
                <ul class="mt-2">
                    <li><strong>status</strong> must be either <code>active</code> or <code>inactive</code>.</li>
                    <li><strong>phone</strong> must be unique (not already present in the database).</li>
                    <li><strong>password</strong> must be at least 6 characters long.</li>
                </ul>
            </div>

            <form action="{{ route('committee.bulk_upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group mb-4">
                    <label for="csv_file" class="form-label"><strong>Choose CSV File:</strong></label>
                    <input type="file" name="csv_file" id="csv_file" class="form-control @error('csv_file') is-invalid @enderror" accept=".csv">
                    @error('csv_file')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-success">
                    <i class="fas fa-upload"></i> Upload & Process
                </button>
            </form>
        </div>
    </div>
</div>
@endsection