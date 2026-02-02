<?php
namespace shared\traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait ImageUploadTrait
{
    /**
     * Upload image and return the path
     * @param UploadedFile $image
     * @param string $folder
     * @return string|null
     */
    public function uploadImage(UploadedFile $image, string $folder = 'products'): ?string
    {
        try {
            // Generate unique filename
            $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
            
            // Store image in public disk
            $path = $image->storeAs($folder, $filename, 'public');
            
            // Return full URL
            return Storage::url($path);
        } catch (\Exception $e) {
            \Log::error('Image upload failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete image from storage
     * @param string $imagePath
     * @return bool
     */
    public function deleteImage(string $imagePath): bool
    {
        try {
            // Extract relative path from URL
            $relativePath = str_replace('/storage/', '', $imagePath);
            return Storage::disk('public')->delete($relativePath);
        } catch (\Exception $e) {
            \Log::error('Image deletion failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Validate image file
     * @param UploadedFile $image
     * @return array
     */
    public function validateImage(UploadedFile $image): array
    {
        $errors = [];
        
        // Check file size (max 5MB)
        if ($image->getSize() > 5 * 1024 * 1024) {
            $errors[] = 'Image size must be less than 5MB';
        }
        
        // Check file type
        $allowedTypes = ['jpeg', 'jpg', 'png', 'gif', 'webp', "avif"];
        $extension = strtolower($image->getClientOriginalExtension());
        
        if (!in_array($extension, $allowedTypes)) {
            $errors[] = 'Image must be of type: ' . implode(', ', $allowedTypes);
        }
        
        return $errors;
    }
}