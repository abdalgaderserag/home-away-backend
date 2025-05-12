<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreTicketRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    public function store(StoreTicketRequest $request)
    {
        $user = Auth::user();
        $user->tickets()->create([
            'title' => $request->message,
            'message' => $request->message,
        ]);
        // $category = Category::where('slug', 'project-approval')->first();
        // $ticket->attachCategories($category);
    }
}
