<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UpdateRequest;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\RequestMail;
use App\Services\AuditLogger;

class UpdateRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->isAdminHR()) {
            Notification::where('user_id', $user->id)
                ->where('type', 'request')
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        $filter = $request->input('filter', 'Pending');
        $validFilters = ['Pending', 'Resolved', 'Dismissed', 'All'];
        if (!in_array($filter, $validFilters)) {
            $filter = 'Pending';
        }

        $query = UpdateRequest::query();
        if ($filter !== 'All') {
            $query->where('status', $filter);
        }

        $requests = $query->latest()->get();

        $counts = [];
        foreach ($validFilters as $f) {
            $counts[$f] = $f === 'All' 
                ? UpdateRequest::count() 
                : UpdateRequest::where('status', $f)->count();
        }

        return view('requests.index', compact('requests', 'filter', 'validFilters', 'counts'));
    }

    public function myRequests(Request $request)
    {
        $user = Auth::user();
        Notification::where('user_id', $user->id)
            ->where('type', 'request')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $filter = $request->input('filter', 'All');
        $validFilters = ['All', 'Pending', 'Resolved', 'Dismissed'];
        if (!in_array($filter, $validFilters)) {
            $filter = 'All';
        }

        $query = UpdateRequest::where('requester_id', $user->id);
        if ($filter !== 'All') {
            $query->where('status', $filter);
        }

        $requests = $query->latest()->get();

        $counts = [];
        foreach ($validFilters as $f) {
            $q = UpdateRequest::where('requester_id', $user->id);
            if ($f !== 'All') {
                $q->where('status', $f);
            }
            $counts[$f] = $q->count();
        }

        return view('requests.my_requests', compact('requests', 'filter', 'validFilters', 'counts'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        $fields = $request->input('fields', []);
        $message = trim($request->input('message'));
        
        if (!empty($fields)) {
            $fieldList = implode(', ', array_map('htmlspecialchars', $fields));
            $message = "Fields to update: " . $fieldList . "\n\n" . $message;
        }

        $updateRequest = UpdateRequest::create([
            'requester_id' => $user->id,
            'requester_name' => $user->name,
            'record_type' => $request->input('record_type'),
            'record_id' => (int)$request->input('record_id'),
            'record_reference' => trim($request->input('record_reference')),
            'message' => $message,
            'status' => 'Pending'
        ]);

        // Notify Admins
        $adminIds = User::whereIn('role', ['admin_it', 'admin_hr'])
            ->where('is_active', 1)
            ->where('id', '!=', $user->id)
            ->pluck('id');

        foreach ($adminIds as $adminId) {
            Notification::create([
                'user_id' => $adminId,
                'type' => 'request',
                'title' => 'New Update Request',
                'message' => $user->name . ' submitted a ' . $request->input('record_type') . ' update request',
                'link' => route('requests.index')
            ]);
        }

        $this->sendMail(
            $adminIds->toArray(),
            'New Update Request',
            "{$user->name} has submitted a new update request for {$request->input('record_type')} that requires your review.",
            $updateRequest
        );

        $redirectMap = [
            'Staff Data' => 'staff.index',
            'Training Record' => 'training.index',
            'Family Information' => 'family.index' // Assuming these routes exist
        ];

        $route = $redirectMap[$request->input('record_type')] ?? 'dashboard';

        return redirect()->route($route)->with('success', 'Update request submitted successfully.');
    }

    public function resolve(Request $request, UpdateRequest $updateRequest)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $note = trim($request->input('admin_note'));
        $updateRequest->update([
            'status' => 'Resolved',
            'admin_note' => $note ?: null
        ]);

        Notification::create([
            'user_id' => $updateRequest->requester_id,
            'type' => 'request',
            'title' => 'Request Resolved',
            'message' => 'Your ' . $updateRequest->record_type . ' update request has been resolved' . ($note ? ': ' . $note : '.'),
            'link' => route('my-requests')
        ]);

        $this->sendMail(
            [$updateRequest->requester_id],
            'Update Request Resolved',
            "Great news! Your {$updateRequest->record_type} update request has been resolved by an administrator.",
            $updateRequest,
            $note ?: null,
            '#15803d' // Green
        );

        AuditLogger::log('resolve', 'requests',
            'Resolved ' . $updateRequest->record_type . ' update request #' . $updateRequest->id . ' from ' . $updateRequest->requester_name . '.',
            ['request_id' => $updateRequest->id]
        );

        return redirect()->route('requests.index', ['filter' => 'Pending'])->with('success', 'Request resolved successfully.');
    }

    public function dismiss(Request $request, UpdateRequest $updateRequest)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $note = trim($request->input('admin_note'));
        $updateRequest->update([
            'status' => 'Dismissed',
            'admin_note' => $note ?: null
        ]);

        Notification::create([
            'user_id' => $updateRequest->requester_id,
            'type' => 'request',
            'title' => 'Request Dismissed',
            'message' => 'Your ' . $updateRequest->record_type . ' update request has been dismissed' . ($note ? ': ' . $note : '.'),
            'link' => route('my-requests')
        ]);

        $this->sendMail(
            [$updateRequest->requester_id],
            'Update Request Dismissed',
            "Your {$updateRequest->record_type} update request has been dismissed by an administrator.",
            $updateRequest,
            $note ?: null,
            '#b91c1c' // Red
        );

        AuditLogger::log('dismiss', 'requests',
            'Dismissed ' . $updateRequest->record_type . ' update request #' . $updateRequest->id . ' from ' . $updateRequest->requester_name . '.',
            ['request_id' => $updateRequest->id]
        );

        return redirect()->route('requests.index', ['filter' => 'Pending'])->with('success', 'Request dismissed.');
    }

    private function sendMail(array $userIds, string $heading, string $body, UpdateRequest $updateRequest, ?string $adminNote = null, ?string $color = null): void
    {
        if (empty($userIds)) return;

        if (!$color) {
            $color = match ($updateRequest->status) {
                'Resolved' => '#15803d', // Green
                'Dismissed' => '#b91c1c', // Red
                'Pending' => '#b45309', // Yellow
                default => '#1e40af', // Blue
            };
        }

        $recipients = User::whereIn('id', $userIds)->whereNotNull('email')->where('email', '!=', '')->get();
        foreach ($recipients as $recipient) {
            try {
                Mail::to($recipient->email)->send(new RequestMail($heading, $body, $updateRequest, $adminNote, $color));
            } catch (\Exception $e) {
                \Log::error('RequestMail failed for user ' . $recipient->id . ': ' . $e->getMessage());
            }
        }
    }
}
