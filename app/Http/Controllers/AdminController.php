<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\NotifyUserOfDecision;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Accept a member's registration
    public function acceptMember(User $user)
    {
        // Update user status to 'accepted'
        $user->status = 'approved';
        $user->save();

        // Notify the user of acceptance
        $user->notify(new NotifyUserOfDecision('approved'));

        return response()->json(['message' => 'Member accepted successfully.'], 200);
    }

    // Reject a member's registration
    public function rejectMember(User $user)
    {
        // Update user status to 'rejected'
        $user->status = 'rejected';
        $user->save();

        // Notify the user of rejection
        $user->notify(new NotifyUserOfDecision('rejected'));

        return response()->json(['message' => 'Member rejected successfully.'], 200);
    }
}
