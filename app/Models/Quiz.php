<?php

declare(strict_types=1);

namespace App\Models;

use App\Helpers\ImageHelper;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory, HasUuids;

    //protected $appends = ['image_base64'];

    protected $fillable = ['title', 'description', 'image'];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function attempts()
    {
        return $this->hasMany(Attempt::class);
    }

    // public function getImageBase64Attribute(): ?string
    // {
    //     return ImageHelper::getImageBase64($this->image);
    // }
}
