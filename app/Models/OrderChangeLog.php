<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderChangeLog extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'order_id',
        'admin_id',
        'user_id',
        'field',
        'old_value',
        'new_value',
        'changed_at',
        'reason_change'

    ];
    protected $table = 'tbl_order_change_log';
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'admin_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
