<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles; // HasRoles from spatie for user role permission

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles; // HasRoles from spatie for user role permission

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'occupation',
        'avatar',
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

     // konsep many to many sehingga harus memiliki pivot table atau table jembatan antara users -> course_students -> courses
     public function courses() {
        return $this->belongsToMany(Course::class, 'course_students'); // user memiliki data course, namun datanya di simpan di dalam table course_students. jadi tidak langsung ke table course | ini di karenakan many to many jadi harus ada jembatan table baru untuk menghubungkan antara users dan courses
    }

    // pengecekan sudah subscribe atau belum
    public function subscribe_transaction() {
        // hasMany karna satu pengguna bisa berlangganan berkali2
        return $this->hasMany(SubscribeTransaction::class);
    }

    // pengecekan data subscribenya masih aktif atau ngga
    public function hasActiveSubscription() {
        $latestSubscription = $this->subscribe_transactions() // ambil subscribe_transactions
        ->where('is_paid', true) // cek is_paidnya true atau ngga
        ->latest('updated_at') // ambil data terakhir dari updated_at karena data aktif akan di hitung semenjak admin sudah approve dan timenya akan terupdate di updated_at
        ->first(); // ambil satu aja yg paling terakhir

        // jika tidak memiliki paket langganan yg aktif maka kirimkan boolean false
        if(!$latestSubscription) {
            return false;
        }
        
        // jika berlangganan jalankan code di bawah
        // subscription_start_date = ambil data aktifnya dan aktifkan sampai 1 bulan
        $subscriptionEndDate = Carbon::parse($latestSubscription->subscription_start_date)->addMonths(1); // Carbon itu helper

        // jika subscriptionEndDate kurang dari 1 bulan atau sama dengan 1 bulan maka return == true (dia berlangganan)
        return Carbon::now()->lessThanOrEqualTo($subscriptionEndDate); // Carbon now artinya di hitung sejak hari ini
    }
}
