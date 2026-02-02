<?php
namespace products\application\commands;

use products\domains\contracts\IProductRepository;
use products\domains\contracts\IInventoryCommandsGetway;
use products\domains\contracts\IImageUploadService;

class UpdateProductHandler
{
    private $repository;
    private $updateProduct;
    private $inventoryCommands;
    private $imageUploadService;

    public function __construct(
        IProductRepository $repository, 
        UpdateProduct $updateProduct, 
        IInventoryCommandsGetway $inventoryCommands,
        IImageUploadService $imageUploadService
    ) {
        $this->repository = $repository;
        $this->updateProduct = $updateProduct;
        $this->inventoryCommands = $inventoryCommands;
        $this->imageUploadService = $imageUploadService;
    }

    public function handle($id, array $data)
    {
        \Log::info('UpdateProductHandler::handle called', ['product_id' => $id, 'data' => $data]);
        $existing = $this->repository->findById($id);
        if (!$existing) {
            return ['success' => false, 'message' => 'Product not found'];
        }

        // Handle image upload if present
        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            $image = $data['image'];
            
            // Validate image
            $validationErrors = $this->imageUploadService->validateImage($image);
            if (!empty($validationErrors)) {
                return [
                    'success' => false,
                    'message' => implode(', ', $validationErrors)
                ];
            }
            
            // Upload new image
            $imageUrl = $this->imageUploadService->uploadImage($image, 'products');
            if (!$imageUrl) {
                return [
                    'success' => false,
                    'message' => 'Failed to upload image'
                ];
            }
            
            // Delete old image if exists
            if ($existing->image_url) {
                $this->imageUploadService->deleteImage($existing->image_url);
            }
            // Add new image URL to product data (store in images array)
            $data['images'] = [$imageUrl];
            unset($data['image']);
        }

        $this->updateProduct::execute($data);

        $quantity = null;
        if (array_key_exists('quantity', $data)) {
            $quantity = max(0, (int) $data['quantity']);
            unset($data['quantity']);
        }
        $updated = $this->repository->update($id, $data);

        if ($quantity !== null) {
            $inventoryResult = $this->inventoryCommands->updateInventoryForProduct($id, [
                'quantity' => $quantity,
            ]);
        }
        return ['success' => true, 'product' => $updated ?? $existing];
    }
}
