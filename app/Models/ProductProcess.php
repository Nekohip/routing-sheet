<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductProcess extends Model
{
    protected $fillable = [
        'product_id',
        'process_type_id',
        'sequence',
        'status',
        'worker_id',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function processType()
    {
        return $this->belongsTo(ProcessType::class);
    }

    public function worker()
    {
        return $this->belongsTo(User::class, 'worker_id');
    }
}
