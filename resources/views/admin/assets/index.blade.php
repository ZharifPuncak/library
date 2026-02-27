@extends('layouts.admin')

@section('title', 'Asset Management')

@section('content')
<div style="max-width: 1400px; margin: 0 auto;">

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="alert alert-success">
            ✓ {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            ✕ {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            <ul style="margin: 0; padding-left: 1.5rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Header with Action Buttons --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1 class="page-title" style="margin: 0;">ASSET MANAGEMENT</h1>
        
        {{-- <div style="display: flex; gap: 0.75rem;">
            <button onclick="openModal('categoryModal')" 
               style="background: #1778b8; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; border: none; font-weight: 600; cursor: pointer; transition: all 0.3s;">
                Add Category
            </button>
            <button onclick="openModal('photoModal')" 
               style="background: #1778b8; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; border: none; font-weight: 600; cursor: pointer; transition: all 0.3s;">
                Add Photo
            </button>
            <button onclick="openModal('taggingModal')" 
               style="background: #1778b8; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; border: none; font-weight: 600; cursor: pointer; transition: all 0.3s;">
                Add Tagging
            </button>
        </div> --}}
    </div>

    {{-- Filter Section --}}
    <div style="background: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 1.5rem; margin-bottom: 1.5rem;">
        <h2 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 1rem; color: #333;">Filter Assets</h2>
        <form action="{{ route('admin.assets.index') }}" method="GET" style="display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: flex-end;">
            <div style="flex: 1; min-width: 200px;">
                <input type="text" name="search" placeholder="Search title/description..."
                    value="{{ request('search') }}" 
                    style="width: 100%; border: 1px solid #ddd; padding: 0.6rem 1rem; border-radius: 6px; font-size: 0.9rem;">
            </div>

            <select name="type" style="border: 1px solid #ddd; padding: 0.6rem 1rem; border-radius: 6px; font-size: 0.9rem;">
                <option value="">All Types</option>
                <option value="Photo" @selected(request('type')=='Photo')>Photo</option>
                <option value="Video" @selected(request('type')=='Video')>Video</option>
                <option value="Ebook" @selected(request('type')=='Ebook')>e-book</option>
            </select>

            <select name="year" style="border: 1px solid #ddd; padding: 0.6rem 1rem; border-radius: 6px; font-size: 0.9rem;">
                <option value="">All Years</option>
                @foreach(range(date('Y'), 2000) as $year)
                    <option value="{{ $year }}" @selected(request('year') == $year)>{{ $year }}</option>
                @endforeach
            </select>

            <button type="submit" style="background: #007bff; color: white; padding: 0.6rem 1.5rem; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 0.9rem;">
                Apply Filter
            </button>
            
            @if(request()->hasAny(['search', 'type', 'year']))
                <a href="{{ route('admin.assets.index') }}" 
                   style="background: #6c757d; color: white; padding: 0.6rem 1.5rem; border-radius: 6px; text-decoration: none; font-weight: 600; display: inline-block;">
                    Clear
                </a>
            @endif
        </form>
    </div>

    {{-- Assets Table --}}
    <div style="background: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: linear-gradient(135deg, #1e4972 0%, #2a6398 100%); color: white;">
                        <th style="padding: 1rem 1.5rem; text-align: left; font-weight: 600;">Title</th>
                        <th style="padding: 1rem 1.5rem; text-align: left; font-weight: 600;">Type</th>
                        <th style="padding: 1rem 1.5rem; text-align: left; font-weight: 600;">Uploader</th>
                        <th style="padding: 1rem 1.5rem; text-align: left; font-weight: 600;">Uploaded Date</th>
                        <th style="padding: 1rem 1.5rem; text-align: center; font-weight: 600;">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($assets as $asset)
                    <tr style="border-bottom: 1px solid #e5e7eb; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#f9fafb'" onmouseout="this.style.backgroundColor='white'">
                        <td style="padding: 1rem 1.5rem;">
                            <div style="font-weight: 500; color: #111; margin-bottom: 0.25rem;">{{ $asset->title }}</div>
                            @if($asset->description)
                                <div style="font-size: 0.85rem; color: #6b7280;">{{ Str::limit($asset->description, 50) }}</div>
                            @endif
                        </td>
                        <td style="padding: 1rem 1.5rem;">
                            <span style="display: inline-block; padding: 0.35rem 0.75rem; border-radius: 12px; font-size: 0.85rem; font-weight: 500;
                                {{ $asset->assetType == 'Photo' ? 'background: #dbeafe; color: #1e40af;' : '' }}
                                {{ $asset->assetType == 'Video' ? 'background: #e9d5ff; color: #6b21a8;' : '' }}
                                {{ $asset->assetType == 'Ebook' ? 'background: #d1fae5; color: #065f46;' : '' }}">
                                {{ $asset->assetType }}
                            </span>
                        </td>
                        <td style="padding: 1rem 1.5rem; color: #374151;">
                            {{ $asset->uploader->name ?? '-' }}
                        </td>
                        <td style="padding: 1rem 1.5rem; color: #374151;">
                            {{ \Carbon\Carbon::parse($asset->dateUploaded)->format('d M Y') }}
                        </td>
                        <td style="padding: 1rem 1.5rem;">
                            <div style="display: flex; justify-content: center; gap: 0.5rem;">
                                <a href="{{ route('admin.assets.edit', $asset->id) }}" 
                                   style="background: #17a2b8; color: white; padding: 0.5rem 1rem; border-radius: 6px; text-decoration: none; font-size: 0.85rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.25rem;">
                                    ✏️ Edit
                                </a>
                                <form action="{{ route('admin.assets.destroy', $asset->id) }}" method="POST" style="display: inline; margin: 0;">
                                    @csrf 
                                    @method('DELETE')
                                    <button type="submit" class="delete-confirm" data-message="Are you sure you want to permanently delete this asset?" 
                                            style="background: #dc3545; color: white; padding: 0.5rem 1rem; border: none; border-radius: 6px; font-size: 0.85rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 0.25rem;">
                                        🗑️ Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="padding: 3rem; text-align: center; color: #6b7280;">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">📦</div>
                            <p style="font-size: 1.1rem; font-weight: 500; margin-bottom: 0.5rem;">No assets found</p>
                            <p style="font-size: 0.9rem;">Try adjusting your filters or add new assets</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($assets->hasPages())
    <div style="margin-top: 1.5rem;">
        {{ $assets->links() }}
    </div>
    @endif


</div>

{{-- Modal: Add Category --}}
{{-- <div id="categoryModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Add New Category</h2>
            <span class="close" onclick="closeModal('categoryModal')">&times;</span>
        </div>
        <form action="{{ route('admin.categories.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="category_name">Category Name *</label>
                <input type="text" id="category_name" name="categoryName" required placeholder="e.g. Award, Sports Day">
            </div>
            <div class="form-group">
                <label for="category_desc">Description</label>
                <textarea id="category_desc" name="description" rows="3" placeholder="Optional description"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('categoryModal')">Cancel</button>
                <button type="submit" class="btn-submit">Create Category</button>
            </div>
        </form>
    </div>
</div> --}}

{{-- Modal: Add Photo --}}
{{-- <div id="photoModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Add New Photo</h2>
            <span class="close" onclick="closeModal('photoModal')">&times;</span>
        </div>
        <form action="{{ route('admin.assets.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="assetType" value="Photo">
            
            <div class="form-group">
                <label for="photo_title">Title</label>
                <input type="text" id="photo_title" name="title" placeholder="Enter photo title (optional - will use filename if empty)">
            </div>
            
            <div class="form-group">
                <label for="photo_desc">Description</label>
                <textarea id="photo_desc" name="description" rows="3" placeholder="Enter description"></textarea>
            </div>
            
            <div class="form-group">
                <label for="photo_file">Upload Photo *</label>
                <input type="file" id="photo_file" name="file" accept="image/*" required>
                <small style="color: #666; display: block; margin-top: 0.25rem;">Max size: 500MB</small>
            </div>
            
            <div class="form-group">
                <label for="photo_date">Date *</label>
                <input type="date" id="photo_date" name="date" value="{{ date('Y-m-d') }}" required>
            </div>
            
            <div class="form-group">
                <label for="photo_tags">Tags (comma separated)</label>
                <input type="text" id="photo_tags" name="tags_input" placeholder="e.g. graduation, 2025, ceremony">
                <small style="color: #666; display: block; margin-top: 0.25rem;">Separate multiple tags with commas</small>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('photoModal')">Cancel</button>
                <button type="submit" class="btn-submit">Upload Photo</button>
            </div>
        </form>
    </div>
</div> --}}

{{-- Modal: Add Tagging --}}
{{-- <div id="taggingModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Add New Tag</h2>
            <span class="close" onclick="closeModal('taggingModal')">&times;</span>
        </div>
        <form action="{{ route('admin.tags.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="tag_name">Tag Name *</label>
                <input type="text" id="tag_name" name="tagName" required placeholder="e.g. Graduation, Competition">
            </div>
            <div class="form-group">
                <label for="tag_color">Tag Color</label>
                <input type="color" id="tag_color" name="color" value="#17a2b8">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('taggingModal')">Cancel</button>
                <button type="submit" class="btn-submit">Create Tag</button>
            </div>
        </form>
    </div>
</div> --}}

<style>
/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    animation: fadeIn 0.3s;
}

.modal.active {
    display: flex;
    align-items: center;
    justify-content: center;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.modal-content {
    background-color: white;
    border-radius: 12px;
    width: 90%;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    animation: slideIn 0.3s;
}

@keyframes slideIn {
    from {
        transform: translateY(-50px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 2px solid #e5e7eb;
}

.modal-header h2 {
    margin: 0;
    color: #1e4972;
    font-size: 1.5rem;
}

.close {
    font-size: 2rem;
    font-weight: bold;
    color: #999;
    cursor: pointer;
    transition: color 0.3s;
    line-height: 1;
}

.close:hover {
    color: #333;
}

.modal form {
    padding: 1.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #333;
}

.form-group input[type="text"],
.form-group input[type="file"],
.form-group textarea,
.form-group select {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 0.95rem;
    transition: border-color 0.3s;
}

.form-group input[type="color"] {
    width: 100px;
    height: 45px;
    border: 1px solid #ddd;
    border-radius: 6px;
    cursor: pointer;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
    outline: none;
    border-color: #17a2b8;
    box-shadow: 0 0 0 3px rgba(23, 162, 184, 0.1);
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #e5e7eb;
}

.btn-cancel,
.btn-submit {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-cancel {
    background: #6c757d;
    color: white;
}

.btn-cancel:hover {
    background: #5a6268;
}

.btn-submit {
    background: #1785b8;
    color: white;
}

.btn-submit:hover {
    background: #138496;
}

/* Hover effects for header buttons */
button[onclick*="openModal"]:hover {
    background: #138496 !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

button[type="submit"]:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}

table button:hover {
    opacity: 0.85;
}

/* Alert Messages */
.alert {
    padding: 1rem 1.5rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    font-weight: 500;
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

@media (max-width: 768px) {
    .page-title {
        font-size: 1.5rem !important;
    }
    
    div[style*="justify-content: space-between"] {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 1rem;
    }
    
    div[style*="justify-content: space-between"] > div {
        width: 100%;
        flex-wrap: wrap;
    }
    
    button[onclick*="openModal"] {
        flex: 1;
        min-width: 150px;
    }
    
    table {
        font-size: 0.85rem;
    }
    
    th, td {
        padding: 0.5rem !important;
    }
    
    .modal-content {
        width: 95%;
        margin: 1rem;
    }
}
</style>

<script>
// Open Modal
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.add('active');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

// Close Modal
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.remove('active');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
    
    // Reset form
    const form = modal.querySelector('form');
    if (form) form.reset();
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.classList.remove('active');
        event.target.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modals = document.querySelectorAll('.modal.active');
        modals.forEach(modal => {
            modal.classList.remove('active');
            modal.style.display = 'none';
        });
        document.body.style.overflow = 'auto';
    }
});

// Handle form submission for photo with tags
document.addEventListener('DOMContentLoaded', function() {
    const photoForm = document.querySelector('#photoModal form');
    if (photoForm) {
        photoForm.addEventListener('submit', function(e) {
            // Convert comma-separated tags to array format
            const tagsInput = document.getElementById('photo_tags');
            if (tagsInput && tagsInput.value.trim()) {
                const tags = tagsInput.value.split(',').map(tag => tag.trim()).filter(tag => tag);
                
                // Remove existing hidden tag inputs
                photoForm.querySelectorAll('input[name="tags[]"]').forEach(input => input.remove());
                
                // Add tags as array
                tags.forEach(tag => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'tags[]';
                    input.value = tag;
                    photoForm.appendChild(input);
                });
            }
        });
    }
});
</script>
@endsection