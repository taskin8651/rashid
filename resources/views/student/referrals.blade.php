@extends('layouts.student')

@section('title', 'Refer & Earn')

@section('content')
  <div class="ov-banner">
    <div class="ov-ribbon"><i class="bi bi-gift-fill"></i>Refer &amp; Earn</div>
    <h4>Invite Friends, Earn Rewards</h4>
    <p>Share your referral link — when a friend signs up and enrolls in their first course, you get a ₹{{ \App\Models\Referral::REWARD_AMOUNT }} coupon.</p>
    <div class="ov-banner-stats">
      <div><b>{{ $stats['total'] }}</b><span>Friends Referred</span></div>
      <div><b>{{ $stats['rewarded'] }}</b><span>Rewards Earned</span></div>
      <div><b>{{ $stats['pending'] }}</b><span>Pending</span></div>
    </div>
  </div>

  <div class="card-rt mb-4">
    <div class="card-title">Your Referral Link</div>
    <div class="d-flex gap-2 flex-wrap">
      <input class="fctrl" id="refLink" readonly value="{{ route('home', ['ref' => $user->referral_code]) }}" style="flex:1;min-width:240px">
      <button class="bsave" type="button" onclick="navigator.clipboard.writeText(document.getElementById('refLink').value); this.innerText='Copied!'; setTimeout(() => this.innerText='Copy Link', 1500)">Copy Link</button>
    </div>
    <p style="font-size:11.5px;color:var(--muted);margin:10px 0 0">Referral Code: <b style="color:var(--text)">{{ $user->referral_code }}</b></p>
  </div>

  <div class="card-rt">
    <div class="card-title">Referral History</div>
    <div class="table-wrap"><table class="table-rt">
      <thead><tr><th>Friend</th><th>Joined</th><th>Status</th><th>Reward</th></tr></thead>
      <tbody>
        @forelse ($referrals as $r)
          <tr>
            <td>{{ $r->referredUser->name }}</td>
            <td>{{ $r->created_at->format('d M Y') }}</td>
            <td><span class="badge-rt {{ $r->status === 'rewarded' ? 'bg-active' : 'bg-pending' }}">{{ ucfirst($r->status) }}</span></td>
            <td>{{ $r->rewardCoupon->code ?? '—' }}</td>
          </tr>
        @empty
          <tr><td colspan="4" style="color:var(--muted)">No referrals yet. Share your link to get started!</td></tr>
        @endforelse
      </tbody>
    </table></div>
  </div>
@endsection
