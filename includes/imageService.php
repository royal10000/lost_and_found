<?php

require_once __DIR__ . '/../class/crud.php';
require_once __DIR__ . '/../configs/configs.php';

class ImageService
{
    private CRUD $crud;

    public function __construct(CRUD $crud)
    {
        $this->crud = $crud;
    }

    public function upload(array $file): int
    {
        $this->validate($file);

        // Hash the actual file contents
        $hash = hash_file('sha256', $file['tmp_name']);

        // Check if the exact same image already exists
        $existingImage = $this->crud->query(
            "SELECT id, filename
             FROM images
             WHERE sha256 = ?",
            [$hash],
            true
        );

        // Image already exists
        if ($existingImage) {
            return (int) $existingImage['id'];
        }

        // Create upload directory
        if (!is_dir(IMAGE_UPLOAD_DIR) && !mkdir(IMAGE_UPLOAD_DIR, 0755, true) && !is_dir(IMAGE_UPLOAD_DIR)) {
            throw new Exception("Could not create the image upload directory.");
        }

        // Get extension from MIME type
        $extension = $this->getExtension($file['tmp_name']);

        // Use hash as filename
        $filename = $hash . '.' . $extension;

        $destination = IMAGE_UPLOAD_DIR . $filename;

        // Save physical file
        if (!move_uploaded_file(
            $file['tmp_name'],
            $destination
        )) {
            throw new Exception("Could not save image.");
        }

        // Save image information in database
        $this->crud->insert('images', [
            'filename' => $filename,
            'sha256'   => $hash
        ]);

        return (int) $this->crud->lastInsertId();
    }

    private function validate(array $file): void
    {
        if (
            !isset($file['error']) ||
            $file['error'] !== UPLOAD_ERR_OK
        ) {
            throw new Exception("Image upload failed.");
        }

        if ($file['size'] > IMAGE_MAX_SIZE) {
            throw new Exception(
                "Image size must be less than 5 MB."
            );
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);

        $mimeType = $finfo->file(
            $file['tmp_name']
        );

        if (!in_array(
            $mimeType,
            ALLOWED_IMAGE_TYPES,
            true
        )) {
            throw new Exception("Invalid image type.");
        }
    }

    private function getExtension(string $filePath): string
    {
        $finfo = new finfo(FILEINFO_MIME_TYPE);

        $mimeType = $finfo->file($filePath);

        return match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
            'image/webp' => 'webp',

            default => throw new Exception(
                "Unsupported image type."
            )
        };
    }
}
