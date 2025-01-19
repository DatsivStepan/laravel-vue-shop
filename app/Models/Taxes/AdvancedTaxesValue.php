<?php

namespace App\Models\Taxes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdvancedTaxesValue extends Model
{
    protected $guarded = ['id'];

    public function field(): BelongsTo
    {
        return $this->belongsTo(AdvancedTaxesField::class, 'field_id');
    }
}
