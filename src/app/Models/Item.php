<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'condition_id', 'name', 'brand', 'price', 'description', 'image_url', 'status'];

    // 出品者
    public function user() {
        return $this->belongsTo(User::class);
    }

    // カテゴリー　多対多
    public function categories() {
        return $this->belongsToMany(Category::class);
    }

    // 商品の状態　1対多
    public function condition() {
        return $this->belongsTo(Condition::class);
    }

    // いいね　1対多
    public function likes() {
        return $this->hasMany(Like::class);
    }

    // コメント　1対多
    public function comments() {
        return $this->hasMany(Comment::class);
    }

    // 購入情報
    public function order() {
        return $this->hasOne(Order::class);
    }

    // SOLD判定
    public function isSold() {
        return $this->order()->exists();
    }
}
