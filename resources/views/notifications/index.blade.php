@extends(auth()->user()->hasRole('admin') ? 'layouts.admin' : (auth()->user()->hasRole('franchisee') ? 'layouts.franchise' : 'layouts.student'))

@section('title', 'Notifications')

@section('content')
  <div class="shead mb-4"><h4>Notifications</h4><p>Everything that's happened on your account</p></div>

  <div class="card-rt p-0" style="overflow:hidden">
    @forelse ($notifications as $n)
      <div class="d-flex align-items-start gap-3 p-3" style="border-bottom:1px solid var(--border);{{ $n->read_at ? '' : 'background:rgba(var(--accent-rgb),.05)' }}">
        <div style="width:36px;height:36px;border-radius:10px;background:rgba(var(--accent-rgb),.1);display:flex;align-items:center;justify-content:center;flex-shrink:0">
          <i class="bi {{ $n->data['icon'] ?? 'bi-bell-fill' }}" style="color:var(--orange)"></i>
        </div>
        <div class="flex-grow-1">
          <div style="font-size:13.5px;font-weight:700;color:var(--text)">{{ $n->data['title'] ?? 'Notification' }}</div>
          <div style="font-size:12.5px;color:var(--muted);margin:2px 0 4px">{{ $n->data['body'] ?? '' }}</div>
          <div style="font-size:11px;color:var(--muted)">{{ $n->created_at->diffForHumans() }}</div>
        </div>
        @if (!empty($n->data['url']))
          <a href="{{ $n->data['url'] }}" class="action-btn" title="Open"><i class="bi bi-arrow-up-right"></i></a>
        @endif
      </div>
    @empty
      <div class="text-center" style="padding:48px 24px">
        <i class="bi bi-bell-slash" style="font-size:28px;color:var(--muted);margin-bottom:10px;display:inline-block"></i>
        <p style="font-size:13px;color:var(--muted);margin:0">No notifications yet.</p>
      </div>
    @endforelse
  </div>

  <div class="mt-3">{{ $notifications->links() }}</div>
@endsection
