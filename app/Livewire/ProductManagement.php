<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class ProductManagement extends Component
{
    use WithPagination;

    public string $search = '';

    public string $categoryFilter = '';

    // Product modal
    public bool $showProductModal = false;

    public ?int $editingProductId = null;

    public string $sku = '';

    public string $name = '';

    public string $categoryId = '';

    public string $baseUnit = 'pcs';

    public string $costingMethod = 'fifo';

    public ?float $minStock = null;

    public ?float $maxStock = null;

    public string $description = '';

    // Category modal
    public bool $showCategoryModal = false;

    public ?int $editingCategoryId = null;

    public string $catCode = '';

    public string $catName = '';

    public string $catDescription = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function categories()
    {
        return Category::withCount('products')->orderBy('name')->get();
    }

    #[Computed]
    public function products()
    {
        return Product::with('category')
            ->when($this->search, fn ($q) => $q->where(fn ($q2) => $q2->where('name', 'like', "%{$this->search}%")->orWhere('sku', 'like', "%{$this->search}%")))
            ->when($this->categoryFilter, fn ($q) => $q->where('category_id', $this->categoryFilter))
            ->orderBy('name')
            ->paginate(10);
    }

    public function openProductModal(?int $productId = null): void
    {
        $this->resetValidation();
        $this->editingProductId = $productId;

        if ($productId) {
            $product = Product::findOrFail($productId);
            $this->sku = $product->sku;
            $this->name = $product->name;
            $this->categoryId = (string) $product->category_id;
            $this->baseUnit = $product->base_unit;
            $this->costingMethod = $product->costing_method;
            $this->minStock = (float) $product->min_stock;
            $this->maxStock = $product->max_stock !== null ? (float) $product->max_stock : null;
            $this->description = (string) $product->description;
        } else {
            $this->sku = '';
            $this->name = '';
            $this->categoryId = '';
            $this->baseUnit = 'pcs';
            $this->costingMethod = 'fifo';
            $this->minStock = 0;
            $this->maxStock = null;
            $this->description = '';
        }

        $this->showProductModal = true;
    }

    public function saveProduct(): void
    {
        $this->validate([
            'sku' => ['required', 'string', 'max:40', 'unique:products,sku,'.$this->editingProductId],
            'name' => ['required', 'string', 'max:255'],
            'categoryId' => ['nullable'],
            'baseUnit' => ['required', 'string', 'max:20'],
            'costingMethod' => ['required', 'in:fifo,average'],
            'minStock' => ['required', 'numeric', 'min:0'],
            'maxStock' => ['nullable', 'numeric', 'gte:minStock'],
        ]);

        Product::updateOrCreate(['id' => $this->editingProductId], [
            'sku' => $this->sku,
            'name' => $this->name,
            'category_id' => $this->categoryId ?: null,
            'base_unit' => $this->baseUnit,
            'costing_method' => $this->costingMethod,
            'min_stock' => $this->minStock,
            'max_stock' => $this->maxStock,
            'description' => $this->description,
            'is_active' => true,
        ]);

        $this->showProductModal = false;
        unset($this->products);
    }

    public function toggleProductActive(int $productId): void
    {
        $product = Product::findOrFail($productId);
        $product->update(['is_active' => ! $product->is_active]);
        unset($this->products);
    }

    public function openCategoryModal(?int $categoryId = null): void
    {
        $this->resetValidation();
        $this->editingCategoryId = $categoryId;

        if ($categoryId) {
            $category = Category::findOrFail($categoryId);
            $this->catCode = $category->code;
            $this->catName = $category->name;
            $this->catDescription = (string) $category->description;
        } else {
            $this->catCode = '';
            $this->catName = '';
            $this->catDescription = '';
        }

        $this->showCategoryModal = true;
    }

    public function saveCategory(): void
    {
        $this->validate([
            'catCode' => ['required', 'string', 'max:20', 'unique:categories,code,'.$this->editingCategoryId],
            'catName' => ['required', 'string', 'max:255'],
        ]);

        Category::updateOrCreate(['id' => $this->editingCategoryId], [
            'code' => $this->catCode,
            'name' => $this->catName,
            'description' => $this->catDescription,
        ]);

        $this->editingCategoryId = null;
        $this->catCode = '';
        $this->catName = '';
        $this->catDescription = '';
        unset($this->categories);
    }

    public function deleteCategory(int $categoryId): void
    {
        Category::findOrFail($categoryId)->delete();
        unset($this->categories);
        unset($this->products);
    }

    public function render()
    {
        return view('livewire.product-management')->layout('layouts.app', ['title' => 'Produk & Kategori']);
    }
}
