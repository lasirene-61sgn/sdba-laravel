@extends('admin.layout.app')

@block('content')
<div class="container mt-4">
    <div class="card shadow-sm col-md-8 mx-auto">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 text-primary fw-bold">Configure Social Media Links</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.links.store') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label class="form-label fw-bold">WhatsApp Link</label>
                    <input type="text" name="whatsapp_link" class="form-control @error('whatsapp_link') is-invalid @enderror" placeholder="https://wa.me/yournumber" value="{{ old('whatsapp_link') }}">
                    @error('whatsapp_link') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Facebook Link</label>
                    <input type="url" name="facebook_link" class="form-control @error('facebook_link') is-invalid @enderror" placeholder="https://facebook.com/profile" value="{{ old('facebook_link') }}">
                    @error('facebook_link') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Email Address</label>
                    <input type="email" name="email_link" class="form-control @error('email_link') is-invalid @enderror" placeholder="admin@example.com" value="{{ old('email_link') }}">
                    @error('email_link') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Twitter (X) Link</label>
                    <input type="url" name="twitter_link" class="form-control @error('twitter_link') is-invalid @enderror" placeholder="https://x.com/username" value="{{ old('twitter_link') }}">
                    @error('twitter_link') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Instagram Link</label>
                    <input type="url" name="instagram_link" class="form-control @error('instagram_link') is-invalid @enderror" placeholder="https://instagram.com/username" value="{{ old('instagram_link') }}">
                    @error('instagram_link') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">LinkedIn Link</label>
                    <input type="url" name="linkedin_link" class="form-control @error('linkedin_link') is-invalid @enderror" placeholder="https://linkedin.com/in/username" value="{{ old('linkedin_link') }}">
                    @error('linkedin_link') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('admin.links.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">Save Configuration</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endblock