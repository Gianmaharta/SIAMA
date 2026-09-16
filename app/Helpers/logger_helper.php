<?php

if (!function_exists('generate_uuidv4')) {
    function generate_uuidv4() {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}

if (!function_exists('log_access')) {
    /**
     * Mencatat access log (login, logout, failed_login)
     *
     * @param string|null $userId
     * @param string $event
     */
    function log_access($userId, $event)
    {
        $db = \Config\Database::connect();
        $request = \Config\Services::request();
        
        $data = [
            'id'         => generate_uuidv4(),
            'user_id'    => $userId,
            'ip_address' => $request->getIPAddress(),
            'user_agent' => $request->getUserAgent()->getAgentString(),
            'event'      => $event,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        
        $db->table('access_log')->insert($data);
    }
}

if (!function_exists('log_activity')) {
    /**
     * Mencatat activity log (create, update, delete)
     *
     * @param string $module
     * @param string $action
     * @param string $description
     * @param array|null $oldData
     * @param array|null $newData
     */
    function log_activity($module, $action, $description, $oldData = null, $newData = null)
    {
        $db = \Config\Database::connect();
        $userId = session()->has('user_id') ? session()->get('user_id') : null;
        
        $data = [
            'id'          => generate_uuidv4(),
            'user_id'     => $userId,
            'module'      => $module,
            'action'      => $action,
            'description' => $description,
            'old_data'    => $oldData ? json_encode($oldData) : null,
            'new_data'    => $newData ? json_encode($newData) : null,
            'created_at'  => date('Y-m-d H:i:s'),
        ];
        
        $db->table('activity_log')->insert($data);
    }
}
