@extends('admin.layout.app') {{-- Assumed Layout: resources/views/layouts/app.blade.php --}}

@section('content')

{{-- Include Font Awesome for Icons (Required for action buttons in the table) --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<div class="container-fluid">
    <h2 class="mb-4">🤝 Support Member Management</h2>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    
    {{-- Button to Toggle the Form --}}
    <button class="btn btn-success mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#createFormCollapse" aria-expanded="false" aria-controls="createFormCollapse">
        + Add New Support Member
    </button>

    {{-- The Collapsed Form Section --}}
    <div class="collapse {{ $errors->any() ? 'show' : '' }}" id="createFormCollapse">
        <div class="row mb-4">
            
            {{-- Form Container (5 columns wide) --}}
            <div class="col-md-5">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        Create New Support Member
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.support.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            {{-- Image Upload --}}
                            <div class="form-group mb-3">
                                <label for="image">Member Image</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image">
                                @error('image') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            {{-- Name --}}
                            <div class="form-group mb-3">
                                <label for="name">Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            {{-- Phone --}}
                            <div class="form-group mb-3">
                                <label for="phone">Phone</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                                @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            {{-- POST Dropdown (with AJAX Add New) --}}
                            <div class="form-group mb-3">
                                <label for="post_id">Post (Role)</label>
                                <div class="input-group">
                                    <select class="form-control @error('post_id') is-invalid @enderror" id="post_id" name="post_id" required>
                                        <option value="">Select Post</option>
                                        @foreach($posts as $post)
                                            <option value="{{ $post->id }}" {{ old('post_id') == $post->id ? 'selected' : '' }}>{{ $post->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addPostModal">
                                        + Add New
                                    </button>
                                    @error('post_id') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- SUPPORT CATEGORY Dropdown (with AJAX Add New) --}}
                            <div class="form-group mb-3">
                                <label for="support_category_id">Support Category</label>
                                <div class="input-group">
                                    <select class="form-control @error('support_category_id') is-invalid @enderror" id="support_category_id" name="support_category_id" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('support_category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                                        + Add New
                                    </button>
                                    @error('support_category_id') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success mt-3 w-100">💾 Save Support Member</button>
                        </form>
                    </div>
                </div>
            </div>
            
            {{-- Empty column to push form left and maintain visual spacing --}}
            <div class="col-md-7"></div> 
        </div> 
    </div>
    
    {{-- ## 2. LIST OF SUPPORT MEMBERS (Full Width) --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-secondary text-white">
                    My Support Members
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Post</th>
                                    <th>Category</th>
                                    <th style="width: 150px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($supportMembers as $member)
                                <tr>
                                    <td>
                                        @if($member->image)
                                            <img src="{{ Storage::url($member->image) }}" alt="Image" style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;">
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $member->name }}</td>
                                    <td>{{ $member->post->name }}</td>
                                    <td>{{ $member->category->name }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-info view-member" data-id="{{ $member->id }}" data-bs-toggle="modal" data-bs-target="#viewMemberModal" title="View"><i class="fas fa-eye"></i></button>
                                            <button class="btn btn-sm btn-warning edit-member" data-id="{{ $member->id }}" data-bs-toggle="modal" data-bs-target="#editMemberModal" title="Edit"><i class="fas fa-edit"></i></button>
                                            <button class="btn btn-sm btn-danger delete-member" data-id="{{ $member->id }}" data-name="{{ $member->name }}" data-bs-toggle="modal" data-bs-target="#deleteMemberModal" title="Delete"><i class="fas fa-trash-alt"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No support members added by you yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- --- MODALS FOR AJAX ADDITIONS --- --}}

<div class="modal fade" id="addPostModal" tabindex="-1" aria-labelledby="addPostModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addPostModalLabel">Add New Post (Role)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="text" id="new_post_name" class="form-control" placeholder="Enter new post name">
        <p id="post_error" class="text-danger mt-2" style="display: none;"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="savePostBtn">Save Post</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addCategoryModalLabel">Add New Support Category</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="text" id="new_category_name" class="form-control" placeholder="Enter category name">
        <p id="category_error" class="text-danger mt-2" style="display: none;"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="saveCategoryBtn">Save Category</button>
      </div>
    </div>
  </div>
</div>

{{-- --- CRUD MODALS --- --}}

<div class="modal fade" id="viewMemberModal" tabindex="-1" aria-labelledby="viewMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewMemberModalLabel">View Support Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewMemberBody">
                <div class="text-center mb-3">
                    <img id="view_member_image" src="" alt="Member Image" class="img-thumbnail" style="width: 150px; height: 150px; object-fit: cover; border-radius: 50%;">
                </div>
                <p><strong>Name:</strong> <span id="view_member_name"></span></p>
                <p><strong>Phone:</strong> <span id="view_member_phone"></span></p>
                <p><strong>Post:</strong> <span id="view_member_post"></span></p>
                <p><strong>Category:</strong> <span id="view_member_category"></span></p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editMemberModal" tabindex="-1" aria-labelledby="editMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMemberModalLabel">Edit Support Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editMemberForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group mb-3">
                        <label>Current Image</label>
                        <div class="mb-2">
                             <img id="current_image_preview" src="" alt="Current Image" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                        </div>
                        <label for="edit_image">Change Image (Optional)</label>
                        <input type="file" class="form-control" id="edit_image" name="image">
                    </div>

                    <div class="form-group mb-3">
                        <label for="edit_name">Name</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="edit_phone">Phone</label>
                        <input type="text" class="form-control" id="edit_phone" name="phone">
                    </div>

                    <div class="form-group mb-3">
                        <label for="edit_post_id">Post (Role)</label>
                        <select class="form-control" id="edit_post_id" name="post_id" required>
                            @foreach($posts as $post)
                                <option value="{{ $post->id }}">{{ $post->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="edit_support_category_id">Support Category</label>
                        <select class="form-control" id="edit_support_category_id" name="support_category_id" required>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3 w-100">💾 Update Member</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteMemberModal" tabindex="-1" aria-labelledby="deleteMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteMemberModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the support member: <strong><span id="delete_member_name"></span></strong>?</p>
                
                <form id="deleteMemberForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger mt-3 w-100">Yes, Delete Member</button>
                </form>
            </div>
        </div>
    </div>
</div>


@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        
        const baseStorageUrl = "{{ Storage::url('') }}";
        const placeholderImg = "https://via.placeholder.com/150"; 
        
        // --- 1. AJAX for adding new Post (Role) ---
        $('#savePostBtn').on('click', function() {
            let postName = $('#new_post_name').val().trim();
            if (postName === '') {
                $('#post_error').text('Post name cannot be empty.').show();
                return;
            } else {
                $('#post_error').hide();
            }

            $.ajax({
                url: "{{ route('admin.support.post.store') }}",
                method: 'POST',
                data: {
                    name: postName,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        let newOption = new Option(response.post.name, response.post.id, true, true);
                        $('#post_id, #edit_post_id').append(newOption).trigger('change');
                        $('#addPostModal').modal('hide');
                        $('#new_post_name').val('');
                        alert('Post added and selected successfully!');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'An error occurred.';
                    if (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.name) {
                        errorMessage = xhr.responseJSON.errors.name[0];
                    }
                    $('#post_error').text(errorMessage).show();
                }
            });
        });

        // --- 2. AJAX for adding new Support Category ---
        $('#saveCategoryBtn').on('click', function() {
            let categoryName = $('#new_category_name').val().trim();
            if (categoryName === '') {
                $('#category_error').text('Category name cannot be empty.').show();
                return;
            } else {
                $('#category_error').hide();
            }

            $.ajax({
                url: "{{ route('admin.support.category.store') }}",
                method: 'POST',
                data: {
                    name: categoryName,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        let newOption = new Option(response.category.name, response.category.id, true, true);
                        $('#support_category_id, #edit_support_category_id').append(newOption).trigger('change');
                        $('#addCategoryModal').modal('hide');
                        $('#new_category_name').val('');
                        alert('Category added and selected successfully!');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'An error occurred.';
                    if (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.name) {
                        errorMessage = xhr.responseJSON.errors.name[0];
                    }
                    $('#category_error').text(errorMessage).show();
                }
            });
        });
        
        // --- 3. AJAX for VIEW and EDIT (Using the show method) ---
        
        $('.view-member, .edit-member').on('click', function() {
            const memberId = $(this).data('id');
            const isEdit = $(this).hasClass('edit-member');
            const url = "{{ route('admin.support.show', ':id') }}".replace(':id', memberId);

            $.ajax({
                url: url,
                method: 'GET',
                success: function(member) {
                    const imageUrl = member.image ? baseStorageUrl + '/' + member.image : placeholderImg;
                    
                    if (isEdit) {
                        // Populate EDIT Modal
                        const updateUrl = "{{ route('admin.support.update', ':id') }}".replace(':id', member.id);
                        $('#editMemberForm').attr('action', updateUrl);
                        $('#edit_name').val(member.name);
                        $('#edit_phone').val(member.phone);
                        $('#edit_post_id').val(member.post_id);
                        $('#edit_support_category_id').val(member.support_category_id);
                        $('#current_image_preview').attr('src', imageUrl);

                    } else {
                        // Populate VIEW Modal
                        $('#view_member_name').text(member.name);
                        $('#view_member_phone').text(member.phone || 'N/A');
                        $('#view_member_post').text(member.post.name);
                        $('#view_member_category').text(member.category.name);
                        $('#view_member_image').attr('src', imageUrl);
                    }
                },
                error: function(xhr) {
                    alert('Error loading member data. Please try again.');
                    console.error(xhr);
                }
            });
        });
        
        // --- 4. DELETE Modal ---
        $('.delete-member').on('click', function() {
            const memberId = $(this).data('id');
            const memberName = $(this).data('name');
            
            $('#delete_member_name').text(memberName);
            
            // Set the form action for deletion
            const deleteUrl = "{{ route('admin.support.destroy', ':id') }}".replace(':id', memberId);
            $('#deleteMemberForm').attr('action', deleteUrl);
        });

    });
</script>
@endpush