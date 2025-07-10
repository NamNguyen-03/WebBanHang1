<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'order_code',
        'customer_id',
        'order_coupon',
        'order_ship',
        'order_total',
        'created_at',
        'order_status'

    ];
    protected $primaryKey = 'order_id';
    protected $table = 'tbl_order';
    public function order_details()
    {
        return $this->hasMany(OrderDetails::class, 'order_id');
    }
    public function shipping()
    {
        return $this->hasOne(Shipping::class, 'order_id');
    }
    public function getRouteKeyName()
    {
        return 'order_code';
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }
    public function change_logs()
    {
        return $this->hasMany(OrderChangeLog::class, 'order_id', 'order_id');
    }
}
