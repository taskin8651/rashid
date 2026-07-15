@extends('layouts.admin')

@section('title', 'Blog')

@section('content')
  <div class="shead mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div><h4>Blog</h4><p>Publish articles shown on the public blog</p></div>
    <button class="bsave" data-bs-toggle="modal" data-bs-target="#addPost"><i class="bi bi-plus-lg me-1"></i>New Post</button>
  </div>

  <div class="card-rt">
    <div class="table-wrap"><table class="table-rt">
      <thead><tr><th>Title</th><th>Author</th><th>Status</th><th>Published</th><th>Actions</th></tr></thead>
      <tbody>
        @forelse ($posts as $post)
          <tr>
            <td>{{ $post->title }}</td>
            <td>{{ $post->author->name ?? '—' }}</td>
            <td><span class="badge-rt {{ $post->status === 'published' ? 'bg-active' : 'bg-inactive' }}">{{ ucfirst($post->status) }}</span></td>
            <td>{{ optional($post->published_at)->format('d M Y') ?? '—' }}</td>
            <td>
              @if ($post->status === 'published')
                <a href="{{ route('blog.show', $post) }}" target="_blank" class="action-btn" title="View"><i class="bi bi-eye-fill"></i></a>
              @endif
              <button class="action-btn" data-bs-toggle="modal" data-bs-target="#editPost{{ $post->id }}" title="Edit"><i class="bi bi-pencil-fill"></i></button>
              <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" onsubmit="return confirm('Delete this post?')" class="d-inline">
                @csrf @method('DELETE')
                <button type="submit" class="action-btn danger" style="border:none;background:none" title="Delete"><i class="bi bi-trash-fill"></i></button>
              </form>
            </td>
          </tr>

          <div class="modal fade" id="editPost{{ $post->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit Post</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                <form method="POST" action="{{ route('admin.posts.update', $post) }}" enctype="multipart/form-data">
                  @csrf
                  <div class="modal-body">
                    <div class="mb-3"><label class="flbl">Title</label><input class="fctrl" name="title" value="{{ $post->title }}" required/></div>
                    <div class="mb-3"><label class="flbl">Excerpt</label><input class="fctrl" name="excerpt" value="{{ $post->excerpt }}"/></div>
                    <div class="mb-3"><label class="flbl">Body</label><textarea class="fctrl" name="body" rows="8" required>{{ $post->body }}</textarea></div>
                    <div class="row g-3">
                      <div class="col-md-6"><label class="flbl">Cover Image</label><input class="fctrl" type="file" name="cover" accept="image/*"/></div>
                      <div class="col-md-6">
                        <label class="flbl">Status</label>
                        <select class="fctrl" name="status">
                          <option value="draft" @selected($post->status === 'draft')>Draft</option>
                          <option value="published" @selected($post->status === 'published')>Published</option>
                        </select>
                      </div>
                    </div>
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
          <tr><td colspan="5" style="color:var(--muted)">No posts yet.</td></tr>
        @endforelse
      </tbody>
    </table></div>
  </div>

  <div class="modal fade" id="addPost" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">New Post</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <form method="POST" action="{{ route('admin.posts.store') }}" enctype="multipart/form-data">
          @csrf
          <div class="modal-body">
            <div class="mb-3"><label class="flbl">Title</label><input class="fctrl" name="title" required/></div>
            <div class="mb-3"><label class="flbl">Excerpt</label><input class="fctrl" name="excerpt" placeholder="Short summary shown on the blog listing"/></div>
            <div class="mb-3"><label class="flbl">Body</label><textarea class="fctrl" name="body" rows="8" required></textarea></div>
            <div class="row g-3">
              <div class="col-md-6"><label class="flbl">Cover Image</label><input class="fctrl" type="file" name="cover" accept="image/*"/></div>
              <div class="col-md-6">
                <label class="flbl">Status</label>
                <select class="fctrl" name="status">
                  <option value="draft">Draft</option>
                  <option value="published">Published</option>
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="bghost" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="bsave">Create Post</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection
