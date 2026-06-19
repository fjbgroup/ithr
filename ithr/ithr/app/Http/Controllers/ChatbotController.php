<?php

namespace App\Http\Controllers;

use App\Models\MeetingRoom;
use App\Models\RoomBooking;
use App\Models\Notification;
use App\Models\User;
use App\Services\AuditLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ChatbotController extends Controller
{
    private function systemPrompt(): string
    {
        $today   = now()->format('Y-m-d');
        $dayName = now()->format('l');

        return <<<PROMPT
You are a helpful HR Assistant for an HR Admin System. Today is {$dayName}, {$today}.

You assist staff with HR policies, training records, travel requests, and meeting room bookings.

## Booking a meeting room
When the user wants to book a room, collect these details one by one if not already provided:
1. Date (convert "today", "tomorrow", "next Monday" etc. to YYYY-MM-DD)
2. Room — only suggest rooms from the available list provided below
3. Start time and end time (24-hour HH:MM, between 07:00 and 20:00)
4. Purpose of the meeting
5. Number of attendees (optional, default 1)

Important rules:
- Only propose rooms that are listed as available for the requested time slot.
- If a room has a conflict, inform the user and suggest another room or time.
- Once you have ALL details confirmed, output a confirmation message then append this JSON block at the very end (no text after it):
<BOOKING>{"room_id":ROOM_ID,"booking_date":"YYYY-MM-DD","start_time":"HH:MM","end_time":"HH:MM","purpose":"PURPOSE","attendees":N}</BOOKING>

Be concise and friendly. Do not output the <BOOKING> tag until you have all details confirmed by the user.
PROMPT;
    }

    public function chat(Request $request)
    {
        $request->validate(['message' => 'required|string|max:2000']);

        $history = session('chatbot_history', []);
        $history[] = ['role' => 'user', 'content' => $request->message];

        $systemContent = $this->systemPrompt();

        if ($this->isBookingRelated($request->message, $history)) {
            $roomContext = $this->buildRoomContext($request->message, $history);
            if ($roomContext) {
                $systemContent .= "\n\n## Available meeting rooms\n" . $roomContext;
            }
        }

        $messages = array_merge(
            [['role' => 'system', 'content' => $systemContent]],
            array_slice($history, -20)
        );

        try {
            $response = Http::timeout(60)->post('http://localhost:11434/api/chat', [
                'model'    => config('chatbot.model', 'llama3.2'),
                'messages' => $messages,
                'stream'   => false,
            ]);

            if ($response->failed()) {
                return response()->json(['error' => 'Ollama returned an error. Is the model pulled?'], 503);
            }

            $reply = $response->json('message.content') ?? 'Sorry, I could not get a response.';

            $bookingResult = $this->tryCreateBooking($reply);
            if ($bookingResult !== null) {
                $cleanReply = trim(preg_replace('/<BOOKING>.*?<\/BOOKING>/s', '', $reply));

                if ($bookingResult['success']) {
                    $cleanReply .= "\n\n✅ **Booking submitted!** Your booking for **{$bookingResult['room']}** on **{$bookingResult['date']}** ({$bookingResult['time']}) is now pending approval.";
                } else {
                    $cleanReply .= "\n\n❌ **Booking failed:** {$bookingResult['error']} Please try a different time or room.";
                }

                $reply = $cleanReply;
            }

            $history[] = ['role' => 'assistant', 'content' => $reply];
            session(['chatbot_history' => $history]);

            return response()->json(['reply' => $reply]);
        } catch (\Illuminate\Http\Client\ConnectionException) {
            return response()->json(['error' => 'Cannot connect to Ollama. Make sure Ollama is running on your machine.'], 503);
        }
    }

    public function clearHistory()
    {
        session()->forget('chatbot_history');
        return response()->json(['ok' => true]);
    }

    private function isBookingRelated(string $message, array $history): bool
    {
        $keywords = ['book', 'room', 'meeting', 'reserve', 'schedule', 'conference', 'hall', 'available', 'slot'];
        $msg = strtolower($message);

        foreach ($keywords as $kw) {
            if (str_contains($msg, $kw)) return true;
        }

        // Check if we're already in a booking conversation
        foreach (array_slice($history, -8) as $h) {
            $content = strtolower($h['content'] ?? '');
            foreach (['room', 'book', 'meeting', 'reserve'] as $kw) {
                if (str_contains($content, $kw)) return true;
            }
        }

        return false;
    }

    private function buildRoomContext(string $message, array $history): string
    {
        $date  = $this->extractDate($message, $history);
        $rooms = MeetingRoom::all();

        if ($rooms->isEmpty()) return '';

        $lines = [];
        foreach ($rooms as $room) {
            $desc = $room->description ? " — {$room->description}" : '';
            $cap  = $room->capacity    ? ", capacity {$room->capacity}" : '';

            if ($date) {
                $booked = RoomBooking::where('room_id', $room->id)
                    ->where('booking_date', $date)
                    ->whereNotIn('status', ['Rejected'])
                    ->get(['start_time', 'end_time']);

                $slots = $booked->isEmpty()
                    ? 'fully available'
                    : 'booked: ' . $booked->map(fn($b) => substr($b->start_time, 0, 5) . '–' . substr($b->end_time, 0, 5))->join(', ');

                $lines[] = "- Room ID {$room->id}: {$room->name}{$desc}{$cap} | {$date}: {$slots}";
            } else {
                $lines[] = "- Room ID {$room->id}: {$room->name}{$desc}{$cap}";
            }
        }

        $suffix = $date ? " (availability shown for {$date})" : '';
        return implode("\n", $lines) . $suffix;
    }

    private function extractDate(string $message, array $history): ?string
    {
        $corpus = $message;
        foreach (array_slice($history, -10) as $h) {
            $corpus .= ' ' . ($h['content'] ?? '');
        }

        if (preg_match('/\b(\d{4}-\d{2}-\d{2})\b/', $corpus, $m)) {
            return $m[1];
        }

        $lower = strtolower($corpus);
        if (str_contains($lower, 'today'))    return now()->format('Y-m-d');
        if (str_contains($lower, 'tomorrow')) return now()->addDay()->format('Y-m-d');

        if (preg_match('/next\s+(monday|tuesday|wednesday|thursday|friday|saturday|sunday)/i', $lower, $m)) {
            return Carbon::parse('next ' . $m[1])->format('Y-m-d');
        }

        return null;
    }

    private function tryCreateBooking(string $reply): ?array
    {
        if (!preg_match('/<BOOKING>(.*?)<\/BOOKING>/s', $reply, $matches)) {
            return null;
        }

        $data = json_decode(trim($matches[1]), true);

        if (!$data || !isset($data['room_id'], $data['booking_date'], $data['start_time'], $data['end_time'])) {
            return ['success' => false, 'error' => 'Could not parse booking details from the response.'];
        }

        $room = MeetingRoom::find((int) $data['room_id']);
        if (!$room) {
            return ['success' => false, 'error' => 'The selected room no longer exists.'];
        }

        $date      = $data['booking_date'];
        $startTime = $data['start_time'];
        $endTime   = $data['end_time'];
        $purpose   = $data['purpose']   ?? 'Meeting';
        $attendees = $data['attendees'] ?? 1;
        $user      = Auth::user();

        if (Carbon::parse("{$date} {$startTime}")->isPast()) {
            return ['success' => false, 'error' => 'The requested time slot is in the past.'];
        }

        $conflict = RoomBooking::where('room_id', $room->id)
            ->where('booking_date', $date)
            ->whereNotIn('status', ['Rejected'])
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime)
            ->exists();

        if ($conflict) {
            return ['success' => false, 'error' => "That time slot for {$room->name} is already booked."];
        }

        $booking = RoomBooking::create([
            'room_id'        => $room->id,
            'booked_by_id'   => $user->id,
            'booked_by_name' => $user->name,
            'booking_date'   => $date,
            'start_time'     => $startTime,
            'end_time'       => $endTime,
            'is_full_day'    => false,
            'purpose'        => $purpose,
            'attendees'      => (int) $attendees,
            'status'         => 'Pending',
        ]);
        $booking->setRelation('room', $room);

        $picIds   = $room->pics()->pluck('users.id')->toArray();
        $adminIds = User::where('role', 'admin_it')->pluck('id')->toArray();
        $targets  = array_unique(array_merge($picIds, $adminIds));

        foreach ($targets as $tid) {
            Notification::create([
                'user_id' => $tid,
                'type'    => 'booking',
                'title'   => 'New Booking Request',
                'message' => $user->name . ' requested ' . $room->name . ' on ' . date('d M', strtotime($date)) . ' ' . substr($startTime, 0, 5) . '–' . substr($endTime, 0, 5),
                'link'    => route('rooms.index', ['date' => $date]),
                'is_read' => false,
            ]);
        }

        AuditLogger::log('create', 'rooms',
            'Submitted room booking via chatbot for ' . $room->name . ' on ' . $date . '.',
            ['booking_id' => $booking->id]
        );

        return [
            'success' => true,
            'room'    => $room->name,
            'date'    => date('d M Y', strtotime($date)),
            'time'    => substr($startTime, 0, 5) . '–' . substr($endTime, 0, 5),
        ];
    }
}
