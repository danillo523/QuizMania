<?php

declare(strict_types=1);

namespace App\Models;

use App\Helpers\ImageHelper;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory, HasUuids;

    //protected $appends = ['image_base64'];

    protected $fillable = ['quiz_id', 'text', 'image', 'correct_answer'];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function toArray()
    {
        $array = parent::toArray();

        if (auth()->user()->role === 'user') {
            unset($array['correct_answer']);
        }

        return $array;
    }

    // public function getImageBase64Attribute(): ?string
    // {
    //     return ImageHelper::getImageBase64($this->image);
    // }
}
