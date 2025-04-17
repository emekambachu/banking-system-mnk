<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hash',
        'user_id',
        'fund_transfer_id',
        'amount',
        'type',
        'description',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function fundTransfer(): BelongsTo
    {
        return $this->belongsTo(FundTransfer::class, 'fund_transfer_id', 'id');
    }
}
