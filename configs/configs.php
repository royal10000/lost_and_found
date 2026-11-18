<?php

define('IMAGE_UPLOAD_DIR', __DIR__ . '/../public/uploads/images/');
define('IMAGE_MAX_SIZE', 5 * 1024 * 1024); // 5 MB

define('ALLOWED_IMAGE_TYPES', [
    'image/jpeg',
    'image/png',
    'image/gif',
    'image/webp'
]);