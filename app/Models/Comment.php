<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['comment', 'user_id', 'insight_id'];

    public function insight()
    {
        return $this->belongsTo(Insight::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
