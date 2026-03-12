<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ChatsController extends Controller
{
    public function index(): View
    {
        Carbon::setLocale('ru');

        $currentUser = auth()->user();
        $canAccess = in_array($currentUser?->role, ['freelancer', 'employer'], true);
        $myChats = collect();

        if ($canAccess && Schema::hasTable('chats') && Schema::hasTable('messages')) {
            $myChats = Chat::query()
                ->with([
                    'vacancy',
                    'employer',
                    'freelancer',
                    'messages' => fn ($query) => $query->latest('created_at'),
                ])
                ->where(function ($query) use ($currentUser) {
                    $query->where('employer_user_id', $currentUser->id)
                        ->orWhere('freelancer_user_id', $currentUser->id);
                })
                ->get()
                ->map(function (Chat $chat) use ($currentUser): ?array {
                    $otherUser = $currentUser->role === 'freelancer'
                        ? $chat->employer
                        : $chat->freelancer;

                    if (! $chat->vacancy || ! $otherUser) {
                        return null;
                    }

                    $lastMessage = $chat->messages->first();
                    $lastActivityAt = $lastMessage?->created_at ?? $chat->created_at;

                    return [
                        'id' => $chat->id,
                        'vacancy_title' => $chat->vacancy->title,
                        'other_user_name' => $otherUser->name,
                        'last_message_text' => $lastMessage?->text,
                        'last_message_at' => $lastMessage?->created_at,
                        'is_last_message_mine' => (int) ($lastMessage?->sender_user_id) === (int) $currentUser->id,
                        'last_activity_at' => $lastActivityAt,
                    ];
                })
                ->filter()
                ->sortByDesc(fn (array $chat) => $chat['last_activity_at']?->timestamp ?? 0)
                ->values();
        }

        return view('chats', compact('currentUser', 'canAccess', 'myChats'));
    }
}