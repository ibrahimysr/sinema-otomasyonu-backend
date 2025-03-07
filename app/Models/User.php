<?php 
namespace App\Models;  

use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Support\Str;  

class User extends Authenticatable 
{     
    protected $fillable = [
        'name', 'email', 'password', 'api_token', 'role_id',
    ];
    
    protected $hidden = [
        'password', 'api_token',
    ];
    
    public function generateApiToken()
    {
        $this->api_token = Str::random(60);
        $this->save();
        return $this->api_token;
    }
    
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    
    public function hasRole($roleName)
    {
        return $this->role->name === $roleName;
    }
    
    public function isAdmin()
    {
        return $this->hasRole('admin') || $this->hasRole('super_admin');
    }
    
    public function isSuperAdmin()
    {
        return $this->hasRole('super_admin');
    }
}