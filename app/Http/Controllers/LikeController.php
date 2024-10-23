<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Insight;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function store($insightId)
    {
        $insight = Insight::findOrFail($insightId);
        $like = $insight->likes()->where('user_id', auth()->id())->first();

        if ($like) {
            $like->delete();
            return redirect()->back()->with('success', 'Like removed.');
        } else {
            $insight->likes()->create(['user_id' => auth()->id()]);
            return redirect()->back()->with('success', 'Liked.');
        }
    }
}
