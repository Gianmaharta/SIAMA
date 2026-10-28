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
     * Hanya mencatat jika ada perubahan data aktual (tidak mencatat jika hanya menekan simpan tanpa mengubah data).
     *
     * @param string $module
     * @param string $action
     * @param string $description
     * @param array|null $oldData
     * @param array|null $newData
     * @return bool True jika log berhasil dicatat, false jika diabaikan karena tidak ada perubahan data
     */
    function log_activity($module, $action, $description, $oldData = null, $newData = null): bool
    {
        // Validasi perubahan data aktual untuk aksi UPDATE / EDIT
        if (in_array(strtolower($action), ['update', 'edit']) && is_array($oldData) && is_array($newData)) {
            $hasChanged = false;
            $ignoredKeys = [
                'updated_at', 'created_at', 'deleted_at', 
                'id', 'id_user', 'id_opd', 'id_bidang', 'id_arsip', 'id_spt', 'id_berita_acara', 'id_assignment',
                'csrf_test_name'
            ];

            foreach ($newData as $key => $newVal) {
                if (in_array($key, $ignoredKeys, true)) {
                    continue;
                }

                if (array_key_exists($key, $oldData)) {
                    $oldVal = $oldData[$key];
                    $strOld = is_null($oldVal) ? '' : trim((string)$oldVal);
                    $strNew = is_null($newVal) ? '' : trim((string)$newVal);
                    if ($strOld !== $strNew) {
                        $hasChanged = true;
                        break;
                    }
                } else {
                    // Ada field baru di newData yang tidak kosong
                    if (!is_null($newVal) && trim((string)$newVal) !== '') {
                        $hasChanged = true;
                        break;
                    }
                }
            }

            // Jika tidak ada perubahan data sama sekali (hanya klik tombol tanpa ubah apapun), jangan catat ke log!
            if (!$hasChanged) {
                return false;
            }
        }

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
        return true;
    }
}
