<?php

namespace App\Models;

class NotificationModel extends BaseModel
{
    protected $table            = 'notifications';
    protected $primaryKey       = 'id_notification';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields    = [
        'id_notification',
        'id_user',
        'title',
        'message',
        'type',
        'reference_id',
        'reference_type',
        'url',
        'is_read',
        'action_type',
        'action_id',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $beforeInsert  = ['generateUuid'];
    protected $beforeUpdate  = [];

    /**
     * Menghitung total notifikasi belum dibaca untuk user tertentu
     */
    public function getUnreadCount($id_user)
    {
        if (empty($id_user)) {
            return 0;
        }

        return $this->where('id_user', $id_user)
                    ->where('is_read', 0)
                    ->countAllResults();
    }

    /**
     * Mengambil daftar notifikasi user dengan filter status ('all', 'unread', 'read')
     */
    public function getByUser($id_user, string $filter = 'all', ?int $limit = null, int $offset = 0)
    {
        if (empty($id_user)) {
            return [];
        }

        $builder = $this->where('id_user', $id_user);

        if ($filter === 'unread') {
            $builder->where('is_read', 0);
        } elseif ($filter === 'read') {
            $builder->where('is_read', 1);
        }

        $builder->orderBy('created_at', 'DESC');

        if ($limit !== null) {
            $builder->limit($limit, $offset);
        }

        return $builder->findAll();
    }

    /**
     * Mengambil daftar ringkas untuk dropdown notifikasi navbar
     */
    public function getDropdownNotifications($id_user, int $limit = 6)
    {
        if (empty($id_user)) {
            return [];
        }

        return $this->where('id_user', $id_user)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Tandai satu notifikasi sebagai telah dibaca
     */
    public function markAsRead($id_notification, $id_user): bool
    {
        if (empty($id_notification) || empty($id_user)) {
            return false;
        }

        return (bool) $this->where('id_notification', $id_notification)
                           ->where('id_user', $id_user)
                           ->set(['is_read' => 1])
                           ->update();
    }

    /**
     * Tandai seluruh notifikasi user sebagai telah dibaca
     */
    public function markAllAsRead($id_user): bool
    {
        if (empty($id_user)) {
            return false;
        }

        return (bool) $this->where('id_user', $id_user)
                           ->where('is_read', 0)
                           ->set(['is_read' => 1])
                           ->update();
    }

    /**
     * Helper pembuatan notifikasi dengan fallback UUID
     */
    public function send(array $data): ?string
    {
        if (empty($data['id_user']) || empty($data['title']) || empty($data['message'])) {
            return null;
        }

        if (empty($data['id_notification'])) {
            $data['id_notification'] = $this->generateUuidV4();
        }

        if (!isset($data['is_read'])) {
            $data['is_read'] = 0;
        }

        // Sinkronisasi kolom action_type & type jika salah satu diisi
        if (isset($data['type']) && empty($data['action_type'])) {
            $data['action_type'] = $data['type'];
        } elseif (isset($data['action_type']) && empty($data['type'])) {
            $data['type'] = $data['action_type'];
        }

        // Sinkronisasi kolom reference_id & action_id jika salah satu diisi
        if (isset($data['reference_id']) && empty($data['action_id'])) {
            $data['action_id'] = $data['reference_id'];
        } elseif (isset($data['action_id']) && empty($data['reference_id'])) {
            $data['reference_id'] = $data['action_id'];
        }

        if ($this->insert($data)) {
            return $data['id_notification'];
        }

        return null;
    }

    private function generateUuidV4(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
