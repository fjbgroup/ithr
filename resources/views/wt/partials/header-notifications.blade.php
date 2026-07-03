@php
    $wtNotificationUser = Auth::guard('wt')->user();
    $wtUnreadNotificationCount = $wtNotificationUser
        ? $wtNotificationUser->unreadNotifications()->count()
        : 0;
    $wtHeaderNotifications = $wtNotificationUser
        ? $wtNotificationUser->notifications()->take(6)->get()
        : collect();
@endphp

<div class="relative" id="notificationBellWrap">
    <button id="notifBellBtn" type="button" class="topbar-action-btn" title="Notifications" aria-label="Notifications" aria-expanded="false">
        <i class="fas fa-bell" style="font-size:16px"></i>
        @if($wtUnreadNotificationCount > 0)
            <span id="notifBellBadge" class="absolute -right-1 -top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-600 px-1 text-[9px] font-black leading-none text-white">
                {{ $wtUnreadNotificationCount > 99 ? '99+' : $wtUnreadNotificationCount }}
            </span>
        @endif
    </button>

    <div id="notificationDropdown" class="hidden absolute right-0 top-[42px] z-[120] w-[340px] max-w-[calc(100vw-24px)] overflow-hidden">
        <div class="flex items-center justify-between gap-3 px-4 py-3">
            <div>
                <p class="m-0 text-[10px] font-black uppercase tracking-[.16em] text-slate-500 dark:text-slate-300">Notifications</p>
                <p class="m-0 mt-0.5 text-xs font-bold text-slate-900 dark:text-white">{{ $wtUnreadNotificationCount }} unread</p>
            </div>
            @if($wtUnreadNotificationCount > 0)
                <form action="{{ route('wt.notifications.read_all') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="notification-item-action">Mark all read</button>
                </form>
            @endif
        </div>

        <div class="max-h-[360px] overflow-y-auto py-1" data-auto-refresh="true" id="notificationDropdownList">
            @forelse($wtHeaderNotifications as $notification)
                @php
                    $notificationData = is_array($notification->data) ? $notification->data : (json_decode((string) $notification->data, true) ?: []);
                    $notificationTitle = $notificationData['title'] ?? 'WT Notification';
                    $notificationMessage = $notificationData['message'] ?? '';
                    $notificationCategory = $notificationData['category'] ?? 'general';
                @endphp
                <form action="{{ route('wt.notifications.read', $notification->id) }}" method="POST" class="m-0">
                    @csrf
                    <input type="hidden" name="redirect_url" value="{{ request()->fullUrl() }}">
                    <button type="submit" class="notification-item-btn {{ is_null($notification->read_at) ? 'bg-sky-50/60 dark:bg-sky-950/20' : '' }}">
                        <span class="mt-1 h-2 w-2 flex-none rounded-full {{ is_null($notification->read_at) ? 'bg-sky-500' : 'bg-slate-300 dark:bg-slate-600' }}"></span>
                        <span class="min-w-0 flex-1 pl-3">
                            <span class="block text-[10px] font-black uppercase tracking-[.12em] text-sky-700 dark:text-sky-300">{{ $notificationCategory }}</span>
                            <span class="mt-1 block text-sm font-black leading-snug text-slate-900 dark:text-white">{{ $notificationTitle }}</span>
                            @if($notificationMessage !== '')
                                <span class="mt-1 block text-xs font-semibold leading-snug text-slate-500 dark:text-slate-300">{{ $notificationMessage }}</span>
                            @endif
                            <span class="mt-2 block text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ optional($notification->created_at)->diffForHumans() }}</span>
                        </span>
                    </button>
                </form>
            @empty
                <div class="px-4 py-8 text-center">
                    <i class="fas fa-bell-slash text-lg text-slate-300 dark:text-slate-600"></i>
                    <p class="m-0 mt-2 text-xs font-bold text-slate-500 dark:text-slate-300">No notifications yet.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
