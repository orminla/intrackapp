<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailProduct extends Model
{
    use HasFactory;

    protected $table = 'detail_products';
    protected $primaryKey = 'detail_id';

    protected $fillable = [
        'product_id',
        'name',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function scopeSearch($query, $keyword)
    {
        return $query->where('name', 'LIKE', "%{$keyword}%");
    }

    public function autoCompleteDetailProduct($keyword)
    {
        return self::where('name', 'LIKE', "%{$keyword}%")
            ->with('product:product_id,name')
            ->limit(10)
            ->get(['detail_id', 'product_id', 'name']);
    }

    public function schedules()
    {
        return $this->belongsToMany(Schedule::class, 'schedule_details', 'detail_id', 'schedule_id');
    }
}
