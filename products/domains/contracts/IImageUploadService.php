<?php
namespace products\domains\contracts;

interface IImageUploadService
{
    /**
     * Upload image and return the path
     * @param mixed $image
     * @param string $folder
     * @return string|null
     */
    public function uploadImage($image, string $folder = 'products'): ?string;

    /**
     * Delete image from storage
     * @param string $imagePath
     * @return bool
     */
    public function deleteImage(string $imagePath): bool;

    /**
     * Validate image file
     * @param mixed $image
     * @return array
     */
    public function validateImage($image): array;
}