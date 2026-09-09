<?php

namespace App\Models;

use CodeIgniter\Model;

class UserRoleModel extends Model
{
    protected $table            = 'user_roles';
    protected $primaryKey       = 'id_user'; // Composite key is hard in CI4 model, using id_user temporarily
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['user_id', 'role_id']; // Not really used for insertion right now

    // Dates
    protected $useTimestamps = false;

    /**
     * Get user data with their role name
     */
    public function getUserWithRole($userId = null)
    {
        $builder = $this->db->table('users');
        $builder->select('users.*, roles.id_role, roles.nama_role');
        $builder->join('user_roles', 'user_roles.id_user = users.id_user');
        $builder->join('roles', 'roles.id_role = user_roles.id_role');
        
        if ($userId !== null) {
            $builder->where('users.id_user', $userId);
            return $builder->get()->getRowArray();
        }

        return $builder->get()->getResultArray();
    }
    
    /**
     * Helper to find user by email with role
     */
    public function getUserByEmailWithRole(string $email)
    {
        $builder = $this->db->table('users');
        $builder->select('users.*, roles.id_role, roles.nama_role');
        $builder->join('user_roles', 'user_roles.id_user = users.id_user', 'left');
        $builder->join('roles', 'roles.id_role = user_roles.id_role', 'left');
        $builder->where('users.email', $email);
        
        return $builder->get()->getRowArray();
    }
}
