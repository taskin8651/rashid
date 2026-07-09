@extends('layouts.admin')

@section('title', 'Coupon Management')

@section('content')
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
    <div class="shead mb-0"><h4>Coupon Management</h4><p>Create and manage discount coupons</p></div>
  </div>

  <div class="card-rt mb-4">
    <form method="POST" action="{{ route('admin.coupons.store') }}">
      @csrf
      <div class="row g-3">
        <div class="col-md-3"><label class="flbl">Coupon Code</label><input class="fctrl" name="code" placeholder="e.g. RTECH10" required/></div>
        <div class="col-md-3"><label class="flbl">Discount (₹)</label><input class="fctrl" type="number" name="discount_amount" placeholder="1500" required/></div>
        <div class="col-md-3"><label class="flbl">Valid Until</label><input class="fctrl" type="date" name="valid_until"/></div>
        <div class="col-md-3"><label class="flbl">Usage Limit</label><input class="fctrl" type="number" name="usage_limit" placeholder="100"/></div>
        <div class="col-12"><button class="bsave" type="submit"><i class="bi bi-check2-circle me-1"></i>Create Coupon</button></div>
      </div>
    </form>
  </div>

  <div class="card-rt">
    <div class="table-wrap"><table class="table-rt">
      <thead><tr><th>Code</th><th>Discount</th><th>Used</th><th>Valid Until</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        @forelse ($coupons as $c)
          <tr>
            <td><strong>{{ $c->code }}</strong></td>
            <td>₹{{ number_format($c->discount_amount, 0) }}</td>
            <td>{{ $c->used_count }} / {{ $c->usage_limit ?? 'unlimited' }}</td>
            <td>{{ optional($c->valid_until)->format('d M Y') ?? 'No expiry' }}</td>
            <td><span class="badge-rt {{ $c->status === 'active' ? 'bg-active' : 'bg-inactive' }}">{{ $c->status }}</span></td>
            <td>
              <form method="POST" action="{{ route('admin.coupons.destroy', $c) }}" onsubmit="return confirm('Deactivate this coupon?')">
                @csrf @method('DELETE')
                <button type="submit" class="action-btn danger" style="border:none;background:none"><i class="bi bi-trash-fill"></i></button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" style="color:var(--muted)">No coupons yet.</td></tr>
        @endforelse
      </tbody>
    </table></div>
  </div>
@endsection
