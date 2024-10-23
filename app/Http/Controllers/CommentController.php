<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Insight;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, $insightId)
    {
        $request->validate([
            'comment' => 'required',
        ]);

        Comment::create([
            'comment' => $request->comment,
            'user_id' => auth()->id(),
            'insight_id' => $insightId,
        ]);

        return redirect()->back()->with('success', 'Comment added successfully.');
    }
}
