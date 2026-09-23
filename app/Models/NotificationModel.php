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
        'is_read',
        'action_type',
        'action_id',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $beforeInsert  = ['generateUuid'];
    protected $beforeUpdate  = [];
    
    public function getUnreadCount($id_user)
    {
        return $this->where('id_user', $id_user)
                    ->where('is_read', 0)
                    ->countAllResults();
    }
    
    public function getByUser($id_user)
    {
        return $this->where('id_user', $id_user)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
}
