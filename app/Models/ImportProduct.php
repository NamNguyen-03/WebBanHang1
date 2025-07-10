<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportProduct extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'product_id',
        'quantity_in',
        'price_in',
        'created_at'

    ];
    protected $primaryKey = 'import_id';
    protected $table = 'tbl_import_product';
    public function products()
    {
        return $this->hasOne(Product::class, 'product_id', 'product_id');
    }
}
