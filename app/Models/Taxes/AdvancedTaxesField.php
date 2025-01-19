<?php

namespace App\Models\Taxes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdvancedTaxesField extends Model
{
    protected $guarded = ['id'];

    public function values(): HasMany
    {
        return $this->hasMany(AdvancedTaxesValue::class, 'field_id');
    }
}
