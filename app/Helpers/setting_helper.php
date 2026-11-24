<?php

use App\Models\WebsiteSettingModel;

if (!function_exists('get_setting')) {
    function get_setting(string $key, ?string $default = null): string
    {
        $model = new WebsiteSettingModel();
        return $model->getValue($key, $default);
    }
}

if (!function_exists('get_all_settings')) {
    function get_all_settings(bool $forceRefresh = false): array
    {
        $model = new WebsiteSettingModel();
        return $model->getAllMap($forceRefresh);
    }
}
