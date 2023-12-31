<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // protected $fillable = [
    //     'name',
    //     'email',
    //     'password',
    // ];

    protected $guarded = ['id'];


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

    // public function scopeFilter($query, array $filters){
    //     $query->when($filters['search'] ?? false, function($query, $search){
    //         return $query->where('nama', 'like', '%'. $search .'%');
    //     });
    // }

    public function generateOtp()
    {
        $otp = rand(100000, 999999);

        $this->update([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(10), // Atur berakhirnya waktu OTP
        ]);

        return $otp;
    }
    public function resetPassword($newPassword)
    {
        $this->update([
            'password' => bcrypt($newPassword),
            'otp' => null,
            'otp_expires_at' => null,
        ]);
    }
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
    public function surats()
    {
        return $this->hasMany(Surat::class);
    }
    public function rt()
    {
        return $this->belongsTo(Rt::class);
    }
}
