<?php
namespace products\infrastructure\repositories;

use products\domains\contracts\IProductRepository;
use App\Models\Product;

class ProductRepository implements IProductRepository
{
    public function create(array $productData)
    {   
        $user = auth()->user();
        if (!$user) {
            throw new \Exception('Authentication required for creating products');
        }
        $productData['user_id'] = $user->id;
        return Product::create($productData);
    }

    public function update($id, array $productData)
    {
        \Log::info('ProductRepository::update called', ['product_id' => $id, 'data' => $productData]);
        $user = auth()->user();
        if (!$user) {
            throw new \Exception('Authentication required for updating products');
        }
        
        if ($user->role === 'admin' && strtolower((string) ($user->status ?? '')) === 'active') {
            return Product::where('id', $id)->first()->update($productData);
        }
        return Product::where('id', $id)->where("user_id", $user->id)->first()->update($productData);
    }

    public function delete($id)
    {    
        $user = auth()->user();
        if (!$user) {
            throw new \Exception('Authentication required for deleting products');
        }
        
        if ($user->role === 'admin' && strtolower((string) ($user->status ?? '')) === 'active') {
            return Product::where('id', $id)->delete();
        }   
        return Product::where('id', $id)->where("user_id", $user->id)->delete();
    }

    public function findById($id)
    {
        $user = auth()->user();
        if (!$user) {
            throw new \Exception('Authentication required for accessing products');
        }
        
        if ($user->role === 'admin' && strtolower((string) ($user->status ?? '')) === 'active') {
            return Product::with('inventory')->where('id', $id)->first();
        }   
        return Product::with('inventory')->where('id', $id)->where("user_id", $user->id)->first();
    }

    public function findByIdForDashboard($id)
    {
        // Dashboard method - shows product (active and inactive) based on user role
        // Requires authentication (token)
        $user = auth()->user();
        if (!$user) {
            throw new \Exception('Authentication required for dashboard access');
        }
        
        if ($user->role === 'admin' && strtolower((string) ($user->status ?? '')) === 'active') {
            return Product::with('inventory')->where('id', $id)->first();
        }   
        return Product::with('inventory')->where('id', $id)->where("user_id", $user->id)->first();
    }

    public function findByIdPublic($id)
    {
        // Public method for product details page - shows only active products
        // No authentication required
        return Product::with('inventory')
            ->where('id', $id)
            ->where('status', 'active') // Only show active products publicly
            ->first();
    }

    public function getAllForDashboard()
    {
        // Dashboard method - shows all products (active and inactive) based on user role
        // Requires authentication (token)
        $user = auth()->user();
        
        if (!$user) {
            \Log::warning('getAllForDashboard: No authenticated user found. Auth guard: ' . auth()->getDefaultDriver());
            \Log::warning('Request headers: ' . json_encode(request()->headers->all()));
            throw new \Exception('Authentication required for dashboard access. Please login first.');
        }
        
        \Log::info('getAllForDashboard: User authenticated - ' . $user->email . ' (Role: ' . $user->role . ')');
        
        if ($user->role === 'admin' && strtolower((string) ($user->status ?? '')) === 'active') {
            return Product::with('inventory')->orderBy('created_at', 'desc')->get();
        }
        return Product::with('inventory')->where("user_id", $user->id)->orderBy('created_at', 'desc')->get();
    }

    public function getAllActivePublic()
    {
        // Public method for landing page - shows only active products
        // No authentication required
        return Product::with('inventory')
            ->where('status', 'active') // Assuming there's a status column
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
