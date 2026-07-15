@php
  $unreadNotifications = auth()->user()->unreadNotifications()->take(8)->get();
  $unreadCount = auth()->user()->unreadNotifications()->count();
@endphp
<div class="dropdown">
  <a href="#" class="ibtn" data-bs-toggle="dropdown" title="Notifications">
    <i class="bi bi-bell-fill"></i>
    @if ($unreadCount > 0)
      <span class="notif-dot">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
    @endif
  </a>
  <div class="dropdown-menu dropdown-menu-end notif-menu">
    <div class="notif-head">
      <span>Notifications</span>
      @if ($unreadCount > 0)
        <form method="POST" action="{{ route('notifications.read-all') }}">
          @csrf
          <button type="submit" class="notif-mark-all">Mark all read</button>
        </form>
      @endif
    </div>
    @forelse ($unreadNotifications as $n)
      <form method="POST" action="{{ route('notifications.read', $n->id) }}" class="notif-item-form">
        @csrf
        <button type="submit" class="notif-item">
          <i class="bi {{ $n->data['icon'] ?? 'bi-bell-fill' }}"></i>
          <span>
            <b>{{ $n->data['title'] ?? 'Notification' }}</b>
            <small>{{ $n->data['body'] ?? '' }}</small>
            <em>{{ $n->created_at->diffForHumans() }}</em>
          </span>
        </button>
      </form>
    @empty
      <div class="notif-empty">You're all caught up!</div>
    @endforelse
    <a href="{{ route('notifications.index') }}" class="notif-view-all">View all notifications</a>
  </div>
</div>
