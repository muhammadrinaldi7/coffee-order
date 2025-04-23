<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
     use HasFactory;

    protected $fillable = [
        'table_id', 'customer_name', 'status', 'payment_status'
    ];

    public function table()
    {
        return $this->belongsTo(Table::class)->select('id', 'table_number');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
