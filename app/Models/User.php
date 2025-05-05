<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


// JWT
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_card',
        'name',
        'email',
        'password',
        'address',
        'gender',
        'phone_number',
        'profile_picture',
        'role',
        'is_verified',
        'verified_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
        'is_verified' => 'boolean'
    ];

    // Implementasi JWT Subject (Wajib Harus ada 2 function dibawah ini)
    public function getJWTIdentifier()
    {
        return $this->getKey(); // mengambil primary key sebagai identifier
    }

    public function getJWTCustomClaims()
    {
        return []; // custom klaim (contoh : menambah role/status)
    }

    // Relasi ke tabel permissions
    public function permission(){
        return $this->hasMany(Permission::class,'user_id','id');
    }
}
