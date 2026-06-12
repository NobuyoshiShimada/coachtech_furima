<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = ['user_id', 'image_url', 'introduction', 'postcode', 'address', 'building'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
