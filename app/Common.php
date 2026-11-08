<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

if (!function_exists('notification_service')) {
    function notification_service(): \App\Services\NotificationService
    {
        return new \App\Services\NotificationService();
    }
}

if (!function_exists('send_notification')) {
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
