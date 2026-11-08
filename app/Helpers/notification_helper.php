<?php

if (!function_exists('notification_service')) {
    /**
     * Helper instance untuk NotificationService
     */
    function notification_service(): \App\Services\NotificationService
    {
        return new \App\Services\NotificationService();
    }
}

if (!function_exists('send_notification')) {
    /**
     * Helper cepat mengirim notifikasi ke user tertentu
     */
    function send_notification(
        string $userId,
        string $title,
        string $message,
        string $type = 'general',
        ?string $referenceId = null,
        ?string $referenceType = null,
        ?string $url = null
    ): ?string {
        return notification_service()->sendToUser(
            $userId,
            $title,
            $message,
            $type,
            $referenceId,
            $referenceType,
            $url
        );
    }
}

