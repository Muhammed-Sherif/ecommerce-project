<?php
namespace products\infrastructure\services;

use products\domains\contracts\IImageUploadService;
use shared\traits\ImageUploadTrait;

class ImageUploadService implements IImageUploadService
{
    use ImageUploadTrait {
        ImageUploadTrait::uploadImage as traitUploadImage;
        ImageUploadTrait::deleteImage as traitDeleteImage;
        ImageUploadTrait::validateImage as traitValidateImage;
    }

    /**
     * Upload image and return the path
     * @param mixed $image
     * @param string $folder
     * @return string|null
     */
    public function uploadImage($image, string $folder = 'products'): ?string
    {
        return $this->traitUploadImage($image, $folder);
    }

    /**
     * Delete image from storage
     * @param string $imagePath
     * @return bool
     */
    public function deleteImage(string $imagePath): bool
    {
        return $this->traitDeleteImage($imagePath);
    }

    /**
     * Validate image file
     * @param mixed $image
     * @return array
     */
    public function validateImage($image): array
    {
        return $this->traitValidateImage($image);
    }
}