@extends('admin.layout.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>📝 About Us</h2>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Manage About Us Content</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.about-us.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="5" 
                              placeholder="Enter company description...">{{ old('description', $aboutUs->description) }}</textarea>
                </div>
                
                <div class="mb-4">
                    <label for="vision" class="form-label">Vision</label>
                    <textarea class="form-control" id="vision" name="vision" rows="3" 
                              placeholder="Enter company vision...">{{ old('vision', $aboutUs->vision) }}</textarea>
                </div>
                
                <div class="mb-4">
                    <label for="mission" class="form-label">Mission</label>
                    <textarea class="form-control" id="mission" name="mission" rows="3" 
                              placeholder="Enter company mission...">{{ old('mission', $aboutUs->mission) }}</textarea>
                </div>
                
                <div class="mb-4">
                    <label for="image" class="form-label">Image</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    @if($aboutUs->image_path)
                        <div class="mt-2">
                            <p>Current Image:</p>
                            <img src="{{ asset('storage/' . $aboutUs->image_path) }}" alt="About Us Image" 
                                 class="img-fluid" style="max-width: 200px;">
                        </div>
                    @endif
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-success">Update About Us</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection