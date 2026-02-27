<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Category;
use Illuminate\Support\Str;

class ManageCategories extends Component
{
    public $showManageModal = false;
    public $showAddModal = false;
    public $showEditModal = false;

    public $categories = [];
    public $name; // For Add
    public $editName; // For Edit
    public $editId;

    // Listener untuk menerima acara dari halaman induk (photo.blade.php)
    protected $listeners = ['openCategoryManager' => 'openManageModal'];

    protected $rules = [
        'name' => 'required|string|max:255',
        'editName' => 'required|string|max:255',
    ];

    public function mount()
    {
        $this->loadCategories();
    }

    public function loadCategories()
    {
        $this->categories = Category::orderBy('id', 'desc')->get();
    }

    // ================= MODAL CONTROLS =================
    public function openManageModal()
    {
        $this->loadCategories();
        $this->showManageModal = true;
    }

    public function closeManageModal()
    {
        $this->showManageModal = false;
        $this->showAddModal = false;
        $this->showEditModal = false;
        $this->resetFields();
        // Emit acara untuk memberitahu halaman induk memuat semula senarai kategori
        $this->emit('categoryUpdated'); 
    }

    public function openAddModal()
    {
        $this->reset('name');
        $this->showAddModal = true;
    }

    public function closeAddModal()
    {
        $this->reset('name');
        $this->showAddModal = false;
    }

    public function openEditModal($id)
    {
        $category = Category::find($id);
        if ($category) {
            $this->editId = $id;
            $this->editName = $category->name;
            $this->showEditModal = true;
        }
    }

    public function closeEditModal()
    {
        $this->reset(['editName', 'editId']);
        $this->showEditModal = false;
    }

    // ================= CRUD =================
    public function addCategory()
    {
        $this->validateOnly('name');

        Category::create([
            'name' => $this->name,
            'slug' => Str::slug($this->name)
        ]);

        session()->flash('success', 'Category added successfully.');
        $this->loadCategories();
        $this->closeAddModal();
        $this->emit('categoryUpdated'); // Emit acara
    }

    public function updateCategory()
    {
        $this->validateOnly('editName');

        $category = Category::find($this->editId);
        if ($category) {
            $category->update([
                'name' => $this->editName,
                'slug' => Str::slug($this->editName),
            ]);
            session()->flash('success', 'Category updated successfully.');
            $this->loadCategories();
            $this->closeEditModal();
            $this->emit('categoryUpdated'); // Emit acara
        }
    }

    public function deleteCategory($id)
    {
        $category = Category::find($id);
        if ($category) {
            $category->delete();
            session()->flash('success', 'Category deleted successfully.');
            $this->loadCategories();
            $this->emit('categoryUpdated'); // Emit acara
        }
    }

    private function resetFields()
    {
        $this->reset(['name', 'editName', 'editId']);
    }

    public function render()
    {
        return view('livewire.manage-category');
    }
}