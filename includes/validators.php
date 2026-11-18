<?php 

class Validators{
    function isValidReportDate(string $date): bool
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

?>
