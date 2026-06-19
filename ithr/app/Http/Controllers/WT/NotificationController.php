<?php

namespace App\Http\Controllers\WT;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markAsRead(Request $request, string $id): RedirectResponse
    {
        $notification = $request->user()->notifications()->where('id', $id)->first();
        if ($notification && $notification->read_at === null) {
            $notification->markAsRead();
        }

        $redirectUrl = $request->input('redirect_url');
        if (is_string($redirectUrl) && $this->isSafeRedirectUrl($request, $redirectUrl)) {
            return redirect()->to($redirectUrl);
        }

        return back();
    }

    public function markAllAsRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return back();
    }

    private function isSafeRedirectUrl(Request $request, string $url): bool
    {
        if (str_starts_with($url, '/')) {
            return ! str_starts_with($url, '//');
        }

        $host = parse_url($url, PHP_URL_HOST);

        return $host === $request->getHost();
    }
}


