@extends('admin.layout.app')

@block('content')
<div class="container py-4" style="max-width: 800px;">
    <div class="mb-4">
        <a href="{{ route('admin.customer.family.index', $customer->id) }}" class="text-decoration-none">← Back to Family List</a>
        <h1 class="h3 mt-2">Edit Family Member Details</h1>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.customer.family.update', [$customer->id, $familyMember->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $familyMember->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Relationship</label>
                        <input type="text" name="relationship" class="form-control" value="{{ old('relationship', $familyMember->relationship) }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-select">
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender', $familyMember->gender) == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $familyMember->gender) == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender', $familyMember->gender) == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Mobile Number</label>
                        <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $familyMember->mobile) }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Blood Group</label>
                        <input type="text" name="blood_group" class="form-control" value="{{ old('blood_group', $familyMember->blood_group) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $familyMember->date_of_birth ? \Carbon\Carbon::parse($familyMember->date_of_birth)->format('Y-m-d') : '') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Anniversary Date</label>
                        <input type="date" name="anniversary_date" class="form-control" value="{{ old('anniversary_date', $familyMember->anniversary_date ? \Carbon\Carbon::parse($familyMember->anniversary_date)->format('Y-m-d') : '') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Gotra</label>
                        <input type="text" name="gotra" class="form-control" value="{{ old('gotra', $familyMember->gotra) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Native Place</label>
                        <input type="text" name="native_place" class="form-control" value="{{ old('native_place', $familyMember->native_place) }}">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label d-block">Profile Image</label>
                        @if($familyMember->image)
                            <div class="mb-2">
                                <img src="{{ asset($familyMember->image) }}" class="img-thumbnail" style="max-height: 120px;">
                                <div class="small text-muted">Current file: {{ $familyMember->image }}</div>
                            </div>
                        @endif
                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror">
                        <div class="small text-muted mt-1">Leave blank to keep your current image.</div>
                        @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12 my-2">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="matrimony" value="1" id="matrimonySwitch" {{ old('matrimony', in_array($familyMember->matrimony, [1, true, '1', 'true'])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="matrimonySwitch">Open for Matrimony profiles</label>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Internal Admin Notes</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $familyMember->notes) }}</textarea>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.customer.family.index', $customer->id) }}" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endblock