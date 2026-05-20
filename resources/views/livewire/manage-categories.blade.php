<div>

    <!-- ================= MANAGE MODAL ================= -->
    @if($showManageModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-lg shadow-lg relative">

                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Manage Categories</h3>
                    <button wire:click="closeManageModal" class="text-gray-500 text-2xl leading-none">✕</button>
                </div>

                <!-- Add Category Button -->
                <div class="mb-4">
                    <button wire:click="openAddModal" class="w-full px-4 py-2 bg-green-600 text-white rounded hover:bg-green-500">
                        + Add New Category
                    </button>
                </div>

               <!-- Category List -->
                <div class="space-y-2 max-h-96 overflow-auto">
                    @forelse($categories as $category)
                        <div class="p-3 border rounded flex justify-between items-center transition-all duration-300 hover:bg-gray-50 hover:shadow-md"
                            onmouseover="this.querySelector('.category-actions').style.opacity='1'; this.querySelector('.category-actions').style.transform='translateY(0)';"
                            onmouseout="this.querySelector('.category-actions').style.opacity='0'; this.querySelector('.category-actions').style.transform='translateY(10px)';">
                            
                            <span>{{ $category->name }}</span>
                            
                            <div class="category-actions flex gap-2" style="opacity: 0; transform: translateY(10px); transition: opacity 0.3s ease, transform 0.3s ease;">
                                <button wire:click="openEditModal({{ $category->id }})" 
                                        class="px-4 py-2 bg-blue-600 text-white rounded-lg text-xs font-semibold hover:bg-blue-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105">
                                    Edit
                                </button>
                                <button wire:click="deleteCategory({{ $category->id }})" 
                                        class="px-4 py-2 bg-red-600 text-white rounded-lg text-xs font-semibold hover:bg-red-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105">
                                    Delete
                                </button>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">No categories found.</p>
                    @endforelse
                </div>

                <div class="flex justify-end mt-4">
                    <button wire:click="closeManageModal" class="px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-500">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- ================= ADD MODAL ================= -->
    {{-- @if($showAddModal) --}}
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-lg relative">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Add Category</h3>
                    <button wire:click="closeAddModal" class="text-gray-500 text-2xl leading-none">✕</button>
                </div>

                <input type="text" wire:model.defer="name" placeholder="Category name" class="w-full border rounded p-2 mb-4">

                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                <div class="flex justify-end gap-2">
                    <button wire:click="closeAddModal" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Cancel</button>
                    <button wire:click="addCategory" class="px-4 py-2 bg-slate-800 text-white rounded hover:bg-blue-600">Create</button>
                </div>
            </div>
        </div>
    {{-- @endif --}}

    <!-- ================= EDIT MODAL ================= -->
    @if($showEditModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-lg relative">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Edit Category</h3>
                    <button wire:click="closeEditModal" class="text-gray-500 text-2xl leading-none">✕</button>
                </div>

                <input type="text" wire:model.defer="editName" placeholder="Category name" class="w-full border rounded p-2 mb-4">

                @error('editName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                <div class="flex justify-end gap-2">
                    <button wire:click="closeEditModal" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Cancel</button>
                    <button wire:click="updateCategory" class="px-4 py-2 bg-slate-800 text-white rounded hover:bg-blue-600">Update</button>
                </div>
            </div>
        </div>
    @endif
</div>
