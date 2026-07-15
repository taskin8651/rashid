@extends('layouts.admin')

@section('title', 'Franchise Leads & Bookings')

@section('content')
  <div class="shead mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div><h4>Franchise Inquiries</h4><p>Follow up on inquiries and track paid registrations</p></div>
    <a href="{{ route('admin.franchise.export') }}" class="bghost" style="text-decoration:none;font-size:14px;padding:11px 20px"><i class="bi bi-download me-1"></i>Export Leads CSV</a>
  </div>

  <div class="card-rt mb-4">
    <div class="card-title">Inquiries ({{ $leads->count() }})</div>
    <div class="table-wrap"><table class="table-rt">
      <thead><tr><th>Name</th><th>Phone</th><th>City</th><th>Budget</th><th>Received</th><th>Status</th></tr></thead>
      <tbody>
        @forelse ($leads as $lead)
          <tr>
            <td>{{ $lead->name }}</td>
            <td>{{ $lead->phone }}</td>
            <td>{{ $lead->city }}</td>
            <td>{{ $lead->budget_range ?? '—' }}</td>
            <td>{{ $lead->created_at->format('d M Y') }}</td>
            <td>
              <form method="POST" action="{{ route('admin.franchise.leads.update', $lead) }}" class="d-flex gap-2">
                @csrf
                <select class="fctrl" name="status" style="font-size:12px;padding:4px 8px" onchange="this.form.submit()">
                  @foreach (['new','contacted','converted','rejected'] as $s)
                    <option value="{{ $s }}" @selected($lead->status === $s)>{{ ucfirst($s) }}</option>
                  @endforeach
                </select>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" style="color:var(--muted)">No inquiries yet.</td></tr>
        @endforelse
      </tbody>
    </table></div>
  </div>

  <div class="card-rt">
    <div class="card-title">Paid Registrations ({{ $bookings->count() }})</div>
    <div class="table-wrap"><table class="table-rt">
      <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>City</th><th>Amount</th><th>Payment</th><th>Stage</th><th>Documents</th></tr></thead>
      <tbody>
        @forelse ($bookings as $b)
          <tr>
            <td>{{ $b->name }}</td>
            <td>{{ $b->email }}</td>
            <td>{{ $b->phone }}</td>
            <td>{{ $b->city }}</td>
            <td>₹{{ number_format($b->amount, 0) }}</td>
            <td><span class="badge-rt {{ $b->status === 'paid' ? 'bg-paid' : ($b->status === 'failed' ? 'bg-failed' : 'bg-pending') }}">{{ ucfirst($b->status) }}</span></td>
            <td>
              @if ($b->status === 'paid')
                <form method="POST" action="{{ route('admin.franchise.bookings.update', $b) }}">
                  @csrf
                  <select class="fctrl" name="stage" style="font-size:12px;padding:4px 8px" onchange="this.form.submit()">
                    @foreach ($stages as $key => $label)
                      <option value="{{ $key }}" @selected($b->stage === $key)>{{ $label }}</option>
                    @endforeach
                  </select>
                </form>
              @else
                <span style="color:var(--muted);font-size:12px">—</span>
              @endif
            </td>
            <td>
              @if ($b->status === 'paid')
                <details>
                  <summary style="cursor:pointer;font-size:12px;color:var(--orange)">{{ $b->documents->count() }} file(s)</summary>
                  <div class="mt-2" style="min-width:220px">
                    @foreach ($b->documents as $doc)
                      <div class="d-flex justify-content-between align-items-center mb-1" style="font-size:12px">
                        <span>{{ $doc->typeLabel() }}: {{ $doc->label }}</span>
                        <a href="{{ route('admin.franchise.documents.download', $doc) }}" style="color:var(--orange)"><i class="bi bi-download"></i></a>
                      </div>
                    @endforeach
                    <form method="POST" action="{{ route('admin.franchise.bookings.upload', $b) }}" enctype="multipart/form-data" class="mt-2">
                      @csrf
                      <input class="fctrl mb-1" style="font-size:12px" type="text" name="label" placeholder="e.g. Signed Agreement" required/>
                      <input class="fctrl mb-1" style="font-size:12px" type="file" name="file" required/>
                      <button class="bsave" style="font-size:11px;padding:5px 10px" type="submit">Upload Agreement</button>
                    </form>
                  </div>
                </details>
              @else
                <span style="color:var(--muted);font-size:12px">—</span>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="8" style="color:var(--muted)">No franchise registrations yet.</td></tr>
        @endforelse
      </tbody>
    </table></div>
  </div>
@endsection
