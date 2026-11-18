<?php
require_once __DIR__ . "/crud.php";
require_once __DIR__ . "/../includes/imageService.php";

class Report extends CRUD
{
    public function getCategories(): array
    {
        return $this->query("SELECT * FROM CATEGORY ORDER BY categoryName ASC");
    }

    public function getDateBounds(): array
    {
        $timezone = new DateTimeZone('Asia/Seoul');
        $today = new DateTimeImmutable('today', $timezone);

        return [
            'minimum' => $today->modify('-2 months')->format('Y-m-d'),
            'maximum' => $today->format('Y-m-d'),
        ];
    }

    public function createReport(array $data, array $imageFile, int $userId): array
    {
        $reportType = trim($data['report_type'] ?? '');
        $title = trim($data['title'] ?? '');
        $categoryId = trim($data['category'] ?? '');
        $description = trim($data['description'] ?? '');
        $location = trim($data['location'] ?? '');
        $reportDate = trim($data['date'] ?? '');
        $contact = trim($data['contact'] ?? '');
        $errors = [];

        if (!in_array($reportType, ['lost', 'found'], true)) {
            $errors['report_type'] = 'Please select Lost Item or Found Item.';
        }
        if (mb_strlen($title) < 3) {
            $errors['title'] = 'Title must be at least 3 characters.';
        }
        if (
            $categoryId === '' ||
            !ctype_digit($categoryId) ||
            !$this->query('SELECT id FROM CATEGORY WHERE id = ?', [$categoryId], true)
        ) {
            $errors['category'] = 'Please select a valid category.';
        }
        if (mb_strlen($description) < 10) {
            $errors['description'] = 'Description must be at least 10 characters.';
        }
        if ($location === '') {
            $errors['location'] = 'Please enter a location.';
        }
        if (!$this->isValidReportDate($reportDate)) {
            $errors['date'] = 'Choose a valid date from the last two months through today.';
        }
        if (!preg_match('/^[0-9]{10,15}$/', $contact)) {
            $errors['contact'] = 'Phone number must contain 10 to 15 digits.';
        }
        if ($userId <= 0) {
            $errors['general'] = 'Please sign in before submitting a report.';
        }

        if ($errors) {
            return $errors;
        }

        try {
            $imageId = null;
            if (($imageFile['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                $imageId = (new ImageService($this))->upload($imageFile);
            }

            $this->insert('reports', [
                'user_id' => $userId,
                'category_id' => $categoryId,
                'report_type' => $reportType,
                'title' => $title,
                'lost_found_date' => $reportDate,
                'description' => $description,
                'location' => $location,
                'contact' => $contact,
                'image_id' => $imageId,
                'status' => 'pending',
            ]);
        } catch (Throwable $exception) {
            return ['general' => 'Unable to submit your report. Please check the image and try again.'];
        }

        return [];
    }

    private function isValidReportDate(string $date): bool
    {
        $timezone = new DateTimeZone('Asia/Seoul');
        $parsedDate = DateTimeImmutable::createFromFormat('!Y-m-d', $date, $timezone);
        $dateErrors = DateTimeImmutable::getLastErrors();

        if (
            $parsedDate === false ||
            ($dateErrors !== false && ($dateErrors['warning_count'] > 0 || $dateErrors['error_count'] > 0)) ||
            $parsedDate->format('Y-m-d') !== $date
        ) {
            return false;
        }

        $today = new DateTimeImmutable('today', $timezone);
        $minimumDate = $today->modify('-2 months');

        return $parsedDate >= $minimumDate && $parsedDate <= $today;
    }
}
