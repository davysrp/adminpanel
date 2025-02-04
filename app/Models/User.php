<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable implements FilamentUser, HasName
{
    use  HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function getFilamentName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
    public  function getCurrentUser()
    {

//        $id = session()->get('id');
//        $model = User::find($id);
//        $role = Role::with(['permissions'])->find($model->role_id);
        return true;
    }


    public function canAccessPanel(Panel $panel): bool
    {
//        session('id', $this->id);

//        return  str_ends_with($this->email, '@gmail.com') && $this->hasVerifiedEmail();
//        dd($this->id);
        return true;
    }

    public  static  function checkPermission($id, $access)
    {
        if ($id==1) return true;
        $role = Permission::leftjoin('permission_role', 'permission_role.permission_id', 'permissions.id')
            ->where('permission_role.role_id', $id)
            ->where('permissions.access_name', $access)->count();
        if ($role>0) return true;

        return false;
    }
}
