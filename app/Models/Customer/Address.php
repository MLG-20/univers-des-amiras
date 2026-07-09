<?php

namespace App\Models\Customer;

use App\Models\User;
use Database\Factories\Customer\AddressFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['recipient_name', 'phone', 'city', 'address_line', 'landmark', 'is_default'])]
class Address extends Model
{
    /** @use HasFactory<AddressFactory> */
    use HasFactory;

    protected $table = 'customer_addresses';

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
