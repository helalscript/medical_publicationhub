<?php

use App\Models\Chat;
use App\Models\ChatRoom;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Broadcast::channel('chat.{chatRoomId}', function ($user, $chatRoomId) {
//     if (! $user) {
//         return false;
//     }

//     if (($user->role ?? null) === 'admin') {
//         return true;
//     }

//     $room = ChatRoom::find($chatRoomId);
//     if (! $room) {
//         return false;
//     }

//     return (int) $user->id === (int) $room->seeker_id || (int) $user->id === (int) $room->clergy_id;
// });

Broadcast::channel('opportunity_chat.{chatId}', function ($user, $chatId) {
    Log::info($chatId);
    if (! $user) {
        return false;
    }
    if (($user->role ?? null) === 'admin') {
        return true;
    }

    return \App\Models\ChatUser::query()
        ->where('chat_id', (int) $chatId)
        ->where('user_id', (int) $user->id)
        ->exists();
});
