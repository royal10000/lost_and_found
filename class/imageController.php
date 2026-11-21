<?php
require_once __DIR__ ."/../includes/imageService.php";
class ImageController
{
    private ImageService $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function upload(array $file): int
    {
        return $this->imageService->upload($file);
    }
    
}?> 