<?php
require_once __DIR__ . "/crud.php";
require_once __DIR__ . "/../includes/imageService.php";
require_once __DIR__ . "/../configs/configs.php";

class Report extends CRUD
{
    public function searchPublicReports(
        string $type = '',
        string $search = '',
        string $location = '',
        int $page = 1,
        int $limit = 9
    ): array {
        $type = in_array($type, ['lost', 'found'], true) ? $type : '';
        $limit = max(1, min(50, $limit));
        $conditions = ["reports.status = 'approved'"];
        $params = [];

        if ($type !== '') {
            $conditions[] = 'reports.report_type = ?';
            $params[] = $type;
        }
        if ($search !== '') {
            $conditions[] = '(reports.title LIKE ? OR reports.description LIKE ? OR reports.location LIKE ?)';
            $term = '%' . $search . '%';
            array_push($params, $term, $term, $term);
        }
        if ($location !== '') {
            $conditions[] = 'reports.location LIKE ?';
            $params[] = '%' . $location . '%';
        }

        $where = implode(' AND ', $conditions);
        $totalRow = $this->query(
            'SELECT COUNT(*) AS total FROM reports WHERE ' . $where,
            $params,
            true
        );
        $totalItems = (int) ($totalRow['total'] ?? 0);
        $totalPages = max(1, (int) ceil($totalItems / $limit));
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * $limit;

        $items = $this->query(
            'SELECT reports.*, category.categoryName, images.filename AS image
             FROM reports
             LEFT JOIN CATEGORY AS category ON reports.category_id = category.id
             LEFT JOIN images ON reports.image_id = images.id
             WHERE ' . $where . '
             ORDER BY reports.created_at DESC
             LIMIT ' . $limit . ' OFFSET ' . $offset,
            $params
        );

        return [
            'items' => $items,
            'page' => $page,
            'totalItems' => $totalItems,
            'totalPages' => $totalPages,
        ];
    }

    public function fetchReports(int $userId, int $page = 1, int $limit = 6): array
    {
        $limit = max(1, min(50, $limit));
        $totalRow = $this->query(
            'SELECT COUNT(*) AS total FROM reports WHERE user_id = ?',
            [$userId],
            true
        );
        $totalItems = (int) ($totalRow['total'] ?? 0);
        $totalPages = max(1, (int) ceil($totalItems / $limit));
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * $limit;

        $items = $this->query(
            'SELECT reports.*, category.categoryName, images.filename AS image
             FROM reports
             LEFT JOIN CATEGORY AS category ON reports.category_id = category.id
             LEFT JOIN images ON reports.image_id = images.id
             WHERE reports.user_id = ?
             ORDER BY reports.created_at DESC
             LIMIT ' . $limit . ' OFFSET ' . $offset,
            [$userId]
        );

        return [
            'items' => $items,
            'page' => $page,
            'totalItems' => $totalItems,
            'totalPages' => $totalPages,
        ];
    }

    public function fetchReport(int $reportId, int $userId): ?array
    {
        return $this->query(
            'SELECT reports.*, category.categoryName, images.filename AS image
             FROM reports
             LEFT JOIN CATEGORY AS category ON reports.category_id = category.id
             LEFT JOIN images ON reports.image_id = images.id
             WHERE reports.id = ? AND reports.user_id = ?
             LIMIT 1',
            [$reportId, $userId],
            true
        ) ?: null;
    }

    public function fetchPublicReport(int $reportId): ?array
    {
        return $this->query(
            "SELECT reports.*, category.categoryName, images.filename AS image
             FROM reports
             LEFT JOIN CATEGORY AS category ON reports.category_id = category.id
             LEFT JOIN images ON reports.image_id = images.id
             WHERE reports.id = ? AND reports.status = 'approved'
             LIMIT 1",
            [$reportId],
            true
        ) ?: null;
    }
    /** Delete a report and release its reference to the shared image, if any. */
    public function deleteReport(int $reportId, int $userId): bool
    {
        $filenameToRemove = null;

        $this->conn->beginTransaction();
        try {
            $report = $this->query(
                'SELECT image_id FROM reports WHERE id = ? AND user_id = ? FOR UPDATE',
                [$reportId, $userId],
                true
            );

            if (!$report) {
                $this->conn->rollBack();
                return false;
            }

            $imageId = $report['image_id'];
            $this->delete('reports', $reportId);

            if ($imageId !== null) {
                $image = $this->query(
                    'SELECT filename, image_count FROM images WHERE id = ? FOR UPDATE',
                    [(int) $imageId],
                    true
                );

                if ($image) {
                    $remaining = max(0, (int) $image['image_count'] - 1);
                    if ($remaining === 0) {
                        $this->delete('images', (int) $imageId);
                        $filenameToRemove = basename($image['filename']);
                    } else {
                        $this->query(
                            'UPDATE images SET image_count = ? WHERE id = ?',
                            [$remaining, (int) $imageId]
                        );
                    }
                }
            }

            $this->conn->commit();
        } catch (Throwable $exception) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw $exception;
        }

        // Remove the physical file only after the database transaction commits.
        if ($filenameToRemove !== null) {
            $path = IMAGE_UPLOAD_DIR . $filenameToRemove;
            if (is_file($path)) {
                @unlink($path);
            }
        }

        return true;
    }

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

        $this->conn->beginTransaction();
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
            $this->conn->commit();
        } catch (Throwable $exception) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
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
