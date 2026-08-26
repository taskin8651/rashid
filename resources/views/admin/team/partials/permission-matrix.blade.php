@php
  $moduleLabels = [
    'leads' => 'Leads',
    'students' => 'Students',
    'courses' => 'Courses',
    'categories' => 'Categories',
    'coupons' => 'Coupons',
    'payments' => 'Payments',
    'expenses' => 'Expenses',
    'franchise-leads' => 'Franchise Leads & Bookings',
    'franchise-resources' => 'Franchise Resources',
    'gallery' => 'Gallery',
    'reviews' => 'Reviews',
    'certificate-applications' => 'Certificate Applications',
    'certificates' => 'Certificates',
    'careers' => 'Careers (Job Postings)',
    'job-applications' => 'Job Applications',
    'placements' => 'Placements',
    'attendance-locations' => 'Attendance Locations',
    'attendance' => 'Attendance Records',
    'daily-reports' => 'Daily Reports',
    'staff' => 'Staff & Teachers',
    'faqs' => 'FAQs',
    'blog' => 'Blog',
    'team' => 'Team & Roles',
    'team-members' => 'Our Team (public profiles)',
  ];
  $actionColumns = [
    'index' => 'List',
    'show' => 'View',
    'create' => 'Create',
    'edit' => 'Edit',
    'delete' => 'Delete',
    'follow-up' => 'Follow-up',
  ];
@endphp
<div class="table-wrap">
  <table class="table-rt" style="font-size:12px">
    <thead>
      <tr>
        <th>Module</th>
        @foreach ($actionColumns as $label)
          <th style="text-align:center">{{ $label }}</th>
        @endforeach
      </tr>
    </thead>
    <tbody>
      @foreach ($permissionModules as $module => $actions)
        <tr>
          <td>{{ $moduleLabels[$module] ?? $module }}</td>
          @foreach (array_keys($actionColumns) as $action)
            <td style="text-align:center">
              @if (in_array($action, $actions))
                @php $perm = "{$module}-{$action}"; @endphp
                <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $perm }}" id="{{ $idPrefix }}_{{ $perm }}" @checked(in_array($perm, $checkedPermissions))>
              @endif
            </td>
          @endforeach
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
