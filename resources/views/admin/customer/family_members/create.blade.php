@extends('admin.layout.app')

@block('content')
<div class="container py-4" style="max-width: 800px;">
    <div class="mb-4">
        <a href="{{ route('admin.customer.family.index', $customer->id) }}" class="text-decoration-none">← Back to Family List</a>
        <h1 class="h3 mt-2">Add Family Member for {{ $customer->name }}</h1>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.customer.family.store', $customer->id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Relationship</label>
                        <input type="text" name="relationship" class="form-control" value="{{ old('relationship') }}" placeholder="e.g., Spouse, Son, Mother">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-select">
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Mobile Number</label>
                        <input type="text" name="mobile" class="form-control" value="{{ old('mobile') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Blood Group</label>
                        <input type="text" name="blood_group" class="form-control" value="{{ old('blood_group') }}" placeholder="e.g., O+, A-">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Anniversary Date</label>
                        <input type="date" name="anniversary_date" class="form-control" value="{{ old('anniversary_date') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Gotra</label>
                        <input type="text" name="gotra" class="form-control" value="{{ old('gotra') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Native Place</label>
                        <input type="text" name="native_place" class="form-control" value="{{ old('native_place') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Occupation</label>
                        <input type="text" name="occupation" class="form-control" value="{{ old('occupation') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Education</label>
                        <input type="text" name="education" class="form-control" value="{{ old('education') }}">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Hobbies</label>
                        <input type="text" name="hobbies" class="form-control" value="{{ old('hobbies') }}" placeholder="Comma separated values">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Profile Image</label>
                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror">
                        @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12 my-2">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="matrimony" value="1" id="matrimonySwitch" {{ old('matrimony') ? 'checked' : '' }}>
                            <label class="form-check-label" for="matrimonySwitch">Open for Matrimony profiles</label>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Internal Admin Notes</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.customer.family.index', $customer->id) }}" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-success">Save Family Member</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endblock