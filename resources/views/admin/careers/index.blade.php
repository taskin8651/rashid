@extends('layouts.admin')

@section('title', 'Job Postings')

@section('content')
  <div class="shead mb-4 d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div><h4>Job Postings</h4><p>Openings shown on the public Careers page &mdash; students &amp; visitors can apply with a resume</p></div>
    <div class="d-flex gap-2">
      <a href="{{ route('admin.job-applications.index') }}" class="bghost" style="text-decoration:none"><i class="bi bi-file-earmark-person-fill me-1"></i>View Applications</a>
      <button type="button" class="bsave" data-bs-toggle="modal" data-bs-target="#createJob"><i class="bi bi-plus-lg me-1"></i>Post a Job</button>
    </div>
  </div>

  <div class="modal fade" id="createJob" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Post a Job</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <form method="POST" action="{{ route('admin.careers.store') }}">
          @csrf
          <div class="modal-body">
            @include('admin.careers._fields')
          </div>
          <div class="modal-footer">
            <button type="button" class="bghost" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="bsave">Publish</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="card-rt">
    <div class="table-wrap"><table class="table-rt">
      <thead><tr><th>Title</th><th>Company</th><th>Type</th><th>Applications</th><th>Apply By</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        @forelse ($postings as $job)
          <tr>
            <td>{{ $job->title }}<div style="font-size:11px;color:var(--muted)">{{ optional($job->course)->name ?: '—' }}</div></td>
            <td>{{ $job->company_name }}<div style="font-size:11px;color:var(--muted)">{{ $job->location ?: '—' }}</div></td>
            <td>{{ $job->job_type ?: '—' }}<div style="font-size:11px;color:var(--muted)">{{ $job->work_mode ?: '—' }}</div></td>
            <td><a href="{{ route('admin.job-applications.index', ['job' => $job->id]) }}">{{ $job->applications_count }}</a></td>
            <td>{{ optional($job->apply_by)->format('d M Y') ?: 'Open-ended' }}</td>
            <td>
              <form method="POST" action="{{ route('admin.careers.toggle', $job) }}">
                @csrf
                <button type="submit" class="badge-rt {{ $job->status === 'open' ? 'bg-active' : 'bg-inactive' }}" style="border:none;cursor:pointer">{{ ucfirst($job->status) }}</button>
              </form>
            </td>
            <td>
              <button class="action-btn" style="border:none;background:none" title="Edit" data-bs-toggle="modal" data-bs-target="#editJob{{ $job->id }}"><i class="bi bi-pencil-square"></i></button>
              <form method="POST" action="{{ route('admin.careers.destroy', $job) }}" onsubmit="return confirm('Delete this job posting? Applications will also be removed.')" class="d-inline">
                @csrf @method('DELETE')
                <button type="submit" class="action-btn danger" style="border:none;background:none" title="Delete"><i class="bi bi-trash"></i></button>
              </form>
            </td>
          </tr>

          <div class="modal fade" id="editJob{{ $job->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit Job Posting</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                <form method="POST" action="{{ route('admin.careers.update', $job) }}">
                  @csrf
                  <div class="modal-body">
                    @include('admin.careers._fields', ['job' => $job])
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="bghost" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="bsave">Save Changes</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        @empty
          <tr><td colspan="7" style="color:var(--muted)">No job postings yet.</td></tr>
        @endforelse
      </tbody>
    </table></div>
  </div>
@endsection
