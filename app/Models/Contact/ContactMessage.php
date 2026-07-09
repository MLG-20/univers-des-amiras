<?php

namespace App\Models\Contact;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'email', 'message'])]
class ContactMessage extends Model
{
    use HasFactory;
}
