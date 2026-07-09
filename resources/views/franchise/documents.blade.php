@extends('layouts.franchise')

@section('title', 'Documents')

@section('content')
  <div class="shead"><h4>Documents</h4><p>Agreement copies from R-Tech, and your uploads (ID proof, space photos, etc.)</p></div>

  @forelse ($bookings as $booking)
    <div style="background:var(--card);border:1px solid var(--border);border-radius:14px;padding:20px;margin-bottom:16px">
      <h6 style="font-size:14px;font-weight:700;margin-bottom:14px">{{ $booking->city }} — Booking #{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</h6>

      @forelse ($booking->documents as $doc)
        <div class="d-flex justify-content-between align-items-center mb-2" style="font-size:13px">
          <span><i class="bi bi-file-earmark-text me-2" style="color:var(--orange)"></i>{{ $doc->typeLabel() }}: {{ $doc->label }}</span>
          <a href="{{ route('franchise.documents.download', $doc) }}" style="color:var(--orange)"><i class="bi bi-download"></i></a>
        </div>
      @empty
        <p style="font-size:12px;color:var(--muted)">No documents yet.</p>
      @endforelse

      <form method="POST" action="{{ route('franchise.documents.store') }}" enctype="multipart/form-data" class="mt-3 pt-3" style="border-top:1px solid var(--border)">
        @csrf
        <input type="hidden" name="franchise_booking_id" value="{{ $booking->id }}">
        <div class="row g-2">
          <div class="col-md-4">
            <select class="fctrl" name="type" required>
              @foreach ($documentTypes as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4"><input class="fctrl" type="text" name="label" placeholder="e.g. Aadhaar Card" required/></div>
          <div class="col-md-4"><input class="fctrl" type="file" name="file" required/></div>
        </div>
        <button class="bsave mt-2" style="font-size:12px;padding:6px 14px" type="submit"><i class="bi bi-upload me-1"></i>Upload</button>
      </form>
    </div>
  @empty
    <p style="font-size:13px;color:var(--muted)">Register a franchise first to upload documents.</p>
  @endforelse
@endsection
