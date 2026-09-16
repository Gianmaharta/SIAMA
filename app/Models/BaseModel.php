<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Publisher\Publisher;

class BaseModel extends Model
{
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // To be overridden if needed
    protected $beforeInsert = ['generateUuid', 'setCreatedBy'];
    protected $beforeUpdate = ['setUpdatedBy'];

    protected function generateUuid(array $data)
    {
        if (empty($data['data'][$this->primaryKey])) {
            // Using a simple UUID v4 generator if Publisher doesn't have it natively exposed in older versions
            // Or use sprintf directly
            $data['data'][$this->primaryKey] = $this->uuidv4();
        }
        return $data;
    }

    protected function setCreatedBy(array $data)
    {
        if (function_exists('session') && session()->has('user_id')) {
            $data['data']['created_by'] = session()->get('user_id');
        }
        return $data;
    }

    protected function setUpdatedBy(array $data)
    {
        if (function_exists('session') && session()->has('user_id')) {
            $data['data']['updated_by'] = session()->get('user_id');
        }
        return $data;
    }

    private function uuidv4()
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
