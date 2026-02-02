<?php
namespace products\application\commands;

use products\domains\contracts\IProductRepository;
use products\domains\contracts\IInventoryCommandsGetway;
use products\domains\contracts\IImageUploadService;

class CreateProductHandler
{
    private $repository;
    private $createProduct;
    private $inventoryCommands;
    private $imageUploadService;

    public function __construct(
        IProductRepository $repository,
        CreateProduct $createProduct,
        IInventoryCommandsGetway $inventoryCommands,
        IImageUploadService $imageUploadService
    )
    {
        $this->repository = $repository;
        $this->createProduct = $createProduct;
        $this->inventoryCommands = $inventoryCommands;
        $this->imageUploadService = $imageUploadService;
    }

    public function handle(array $data)
    {
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
            
            // Upload image
            $imageUrl = $this->imageUploadService->uploadImage($image, 'products');
            if (!$imageUrl) {
                return [
                    'success' => false,
                    'message' => 'Failed to upload image'
                ];
            }
            
            // Add image URL to product data (store in images array)
            $data['images'] = [$imageUrl];
            unset($data['image']);
        }
        
        $productData = $this->createProduct::execute($data);
        $createdProduct = $this->repository->create($productData);
        $productId = $createdProduct->id; // Extract the actual ID
        
        $quantity = isset($data['quantity']) ? (int) $data['quantity'] : 0;
        $this->inventoryCommands->createInventoryForProduct($productId, [
            'quantity' => max(0, $quantity),
        ]);

        return [
            'success' => true,
            'product' => $createdProduct,
        ];
    }
}
