@extends('layouts.admin')

@section('title', 'Manage Categories')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{ editModalOpen: false, editName: '', editId: null }">

    {{-- Success/Error Messages --}}
    @if(session('success') && !str_contains(session('success'), 'deleted') && !str_contains(session('success'), 'added') && !str_contains(session('success'), 'updated'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-2xl p-4 flex items-center gap-3 shadow-sm">
            <svg class="h-5 w-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 rounded-2xl p-4 flex items-center gap-3 shadow-sm">
            <svg class="h-5 w-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
            <span class="font-bold">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Back Button --}}
    <a href="{{ route('admin.dashboard') }}" 
        class="inline-flex items-center gap-3 text-slate-500 hover:text-lib-navy font-black mb-8 transition-all group active:scale-95"
        style="text-decoration: none; font-family: 'Fredoka', sans-serif;">
        <div class="bg-white p-2.5 rounded-xl shadow-sm group-hover:shadow-md transition-all border border-slate-100 flex items-center justify-center">
            <svg class="h-4 w-4 group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
            </svg>
        </div>
        <span class="text-xs font-bold uppercase tracking-widest">Back to Dashboard</span>
    </a>

    {{-- Header --}}
    <div class="mb-10">
        <h1 class="text-4xl font-black text-lib-navy uppercase tracking-tighter mb-2">Manage Categories</h1>
        <p class="text-slate-500 font-medium text-lg">Organize your assets into structured categories.</p>
    </div>
    
    {{-- Add New Category Form --}}
    <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border border-slate-100 mb-10 relative overflow-hidden">
        <h2 class="text-xl font-black text-lib-navy mb-6 flex items-center gap-2 uppercase tracking-wide relative z-10">
            Add New Category
        </h2>
        
        <form action="{{ route('admin.categories.store') }}" method="POST" class="relative z-10">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
                <div class="md:col-span-10">
                    <label for="category_name" class="block font-bold text-slate-400 text-xs uppercase tracking-wider mb-2 ml-1">Category Name <span class="text-red-400">*</span></label>
                    <input type="text" id="category_name" name="name" required placeholder="e.g. Events, Portraits"
                           class="w-full bg-slate-50 border-transparent focus:border-lib-sky focus:ring-0 rounded-2xl font-bold text-lib-navy py-4 px-5 shadow-sm hover:shadow-md focus:shadow-lg transition-all">
                    @error('name')
                        <small class="text-red-500 font-bold mt-2 block">{{ $message }}</small>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <button type="submit" 
                            class="w-full bg-lib-navy hover:bg-lib-sky text-white py-4 rounded-2xl font-black shadow-lg shadow-lib-navy/20 hover:shadow-lib-sky/30 hover:-translate-y-1 active:translate-y-0 transition-all flex items-center justify-center gap-2">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                        Add
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Category List --}}
    <div class="bg-white rounded-[2.5rem] shadow-xl border border-slate-100 overflow-hidden">
        <div class="p-8 border-b border-slate-100">
            <h2 class="text-xl font-black text-lib-navy flex items-center gap-2 uppercase tracking-wide">
                <span>📁</span> Existing Categories <span class="bg-lib-light text-lib-sky px-3 py-1 rounded-full text-xs ml-2">{{ count($categories) ?? 0 }}</span>
            </h2>
        </div>
        
        @if(count($categories) > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-8 py-4 text-left font-bold text-slate-400 text-xs uppercase tracking-wider">ID</th>
                            <th class="px-8 py-4 text-left font-bold text-slate-400 text-xs uppercase tracking-wider">Category Name</th>
                            <th class="px-8 py-4 text-left font-bold text-slate-400 text-xs uppercase tracking-wider">Date Created</th>
                            <th class="px-8 py-4 text-right font-bold text-slate-400 text-xs uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($categories as $category)
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-8 py-5 text-slate-500 font-bold font-mono text-sm">#{{ $category->id }}</td>
                                <td class="px-8 py-5 text-lib-navy font-black text-lg">{{ $category->name }}</td>
                                <td class="px-8 py-5 text-slate-400 font-medium text-sm">{{ $category->created_at->format('d M Y') }}</td>
                                
                                <td class="px-8 py-5 text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-100 translate-y-0 lg:opacity-0 lg:translate-y-2 lg:group-hover:opacity-100 lg:group-hover:translate-y-0 transition-all duration-300">
                                        {{-- Edit Button --}}
                                        <button @click="editId = {{ $category->id }}; editName = '{{ addslashes($category->name) }}'; editModalOpen = true" 
                                                class="w-10 h-10 rounded-full bg-white text-blue-600 shadow-md border border-slate-100 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all transform hover:scale-110 p-0"
                                                title="Edit">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                        </button>

                                        {{-- Delete Form --}}
                                        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="inline-block" onsubmit="return confirmDelete(event, '{{ $category->name }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="w-10 h-10 rounded-full bg-white text-red-500 shadow-md border border-slate-100 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all transform hover:scale-110 p-0"
                                                    title="Delete">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-3xl mx-auto mb-4 text-slate-300">📁</div>
                <h3 class="text-lg font-bold text-slate-600 mb-1">No Categories Found</h3>
                <p class="text-slate-400 text-sm">Add your first category above.</p>
            </div>
        @endif
    </div>

    {{-- Edit Modal --}}
    <div x-show="editModalOpen" x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="bg-white w-full max-w-lg rounded-[2.5rem] p-8 shadow-2xl transform transition-all"
             @click.away="editModalOpen = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4">
            
            <h3 class="text-2xl font-black text-lib-navy mb-6 uppercase tracking-tight">Edit Category</h3>
            
            <form :action="`{{ url('admin/categories') }}/${editId}`" method="POST">
                @csrf
                @method('PUT')
                
                <div class="space-y-6">
                    <div>
                        <label class="block font-bold text-slate-400 text-xs uppercase tracking-wider mb-2 ml-1">Category Name</label>
                        <input type="text" name="name" x-model="editName" required 
                               class="w-full bg-slate-50 border-transparent focus:border-lib-sky focus:ring-0 rounded-2xl font-bold text-lib-navy py-4 px-5 shadow-sm hover:shadow-md focus:shadow-lg transition-all">
                    </div>
                </div>

                <div class="flex gap-4 mt-8">
                    <button type="button" @click="editModalOpen = false" 
                            class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 py-3.5 rounded-2xl font-bold text-sm uppercase tracking-widest transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="flex-1 bg-lib-navy hover:bg-lib-sky text-white py-3.5 rounded-2xl font-black text-sm uppercase tracking-widest shadow-lg shadow-lib-navy/20 hover:shadow-lib-sky/30 transition-all">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>


        function confirmDelete(event, name) {
            event.preventDefault();
            const form = event.target;
            
            Swal.fire({
                title: 'Delete Category?',
                text: `Are you sure you want to delete this category? This might affect existing assets.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, Delete it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                background: '#fff',
                customClass: {
                    title: 'font-black text-slate-800',
                    popup: 'rounded-3xl shadow-2xl',
                    confirmButton: 'rounded-xl font-bold px-6 py-3',
                    cancelButton: 'rounded-xl font-bold px-6 py-3'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>

</div>
@endsection