<div class="table-wrap">
  <table class="table-rt">
    <thead>
      <tr><th style="width:26px"></th><th>Title</th><th>Type</th><th>Duration</th><th>Status</th><th style="text-align:right">Actions</th></tr>
    </thead>
    <tbody>
      @foreach ($videos as $video)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $video->title }} @unless ($video->getFirstMedia('file')) <span class="badge-rt bg-pending">No file</span> @endunless</td>
          <td><span class="badge-rt {{ $video->type === 'demo' ? 'bg-active' : 'bg-pending' }}">{{ $video->type === 'demo' ? 'Demo · Public' : 'Premium' }}</span></td>
          <td>{{ gmdate('i:s', $video->duration_seconds) }}</td>
          <td><span class="badge-rt {{ $video->status === 'active' ? 'bg-active' : 'bg-inactive' }}">{{ $video->status }}</span></td>
          <td style="text-align:right;white-space:nowrap">
            <button type="button" class="action-btn" title="Preview" onclick="document.getElementById('prev-{{ $video->id }}').showModal()"><i class="bi bi-play-fill"></i></button>
            <button type="button" class="action-btn" title="Edit" onclick="var r=document.getElementById('edit-{{ $video->id }}');r.style.display=r.style.display==='table-row'?'none':'table-row'"><i class="bi bi-pencil-fill"></i></button>
            <form method="POST" action="{{ route('franchise.courses.videos.destroy', [$course, $video]) }}" onsubmit="return confirm('Delete this video? This cannot be undone.')" style="display:inline">
              @csrf @method('DELETE')
              <button type="submit" class="action-btn danger" title="Delete"><i class="bi bi-trash-fill"></i></button>
            </form>
          </td>
        </tr>
        <tr id="edit-{{ $video->id }}" class="video-edit-row" style="display:none">
          <td colspan="6">
            <form method="POST" action="{{ route('franchise.courses.videos.update', [$course, $video]) }}" enctype="multipart/form-data" class="py-2 js-video-upload-form">
              @csrf
              <div class="row g-2">
                <div class="col-md-4"><input class="fctrl" type="text" name="title" value="{{ $video->title }}" required/></div>
                <div class="col-md-3">
                  <select class="fctrl" name="module_id">
                    <option value="">Unassigned</option>
                    @foreach ($course->modules as $m)
                      <option value="{{ $m->id }}" @selected($m->id === $video->module_id)>{{ $m->title }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-2">
                  <select class="fctrl" name="type">
                    <option value="demo" @selected($video->type === 'demo')>Demo</option>
                    <option value="premium" @selected($video->type === 'premium')>Premium</option>
                  </select>
                </div>
                <div class="col-md-2">
                  <select class="fctrl" name="status">
                    <option value="active" @selected($video->status === 'active')>Active</option>
                    <option value="inactive" @selected($video->status === 'inactive')>Inactive</option>
                  </select>
                </div>
                <div class="col-md-1"><input class="fctrl" type="number" name="duration_seconds" value="{{ $video->duration_seconds }}" min="0"/></div>
                <div class="col-md-4"><input class="fctrl" type="file" name="file" accept="video/mp4,video/webm,video/ogg,video/quicktime"/><span style="font-size:10px;color:var(--muted)">Leave empty to keep current file</span></div>
                <div class="col-md-2"><button class="bsave" type="submit" style="font-size:12px;padding:8px 16px;width:100%">Save</button></div>
              </div>
              @include('partials.video-upload-progress-bar')
            </form>
          </td>
        </tr>
        <dialog id="prev-{{ $video->id }}" style="border:none;border-radius:14px;padding:0;max-width:640px;width:92vw">
          <div style="padding:16px">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <strong style="font-size:13px">{{ $video->title }}</strong>
              <button type="button" class="action-btn" onclick="this.closest('dialog').close()"><i class="bi bi-x-lg"></i></button>
            </div>
            <video src="{{ $video->fileUrl() }}" controls controlsList="nodownload noremoteplayback" disablePictureInPicture disableRemotePlayback oncontextmenu="return false" style="width:100%;border-radius:8px;background:#000"></video>
          </div>
        </dialog>
      @endforeach
    </tbody>
  </table>
</div>
