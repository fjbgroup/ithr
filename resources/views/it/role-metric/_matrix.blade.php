@php
  $yes = '<span class="rm-icon rm-yes" title="Full access"><i class="bi bi-check-lg"></i></span>';
  $no = '<span class="rm-icon rm-no" title="No access"><i class="bi bi-x-lg"></i></span>';
@endphp

<div class="rm-wrap">
  <div class="rm-head">
    <div>
      <div class="rm-kicker">IT System</div>
      <div class="rm-title">Role Permissions Matrix</div>
      <div class="rm-sub">A read-only breakdown of module access and approval capabilities for each IT System role.</div>
    </div>
    <div class="rm-readonly"><i class="bi bi-eye-fill"></i> Read only</div>
  </div>

  <div class="rm-card">
    <div class="table-responsive">
      <table class="rm-table">
        <thead>
          <tr>
            <th style="width:30%">Module / Feature</th>
            <th><span class="rm-role rm-admin">Admin IT</span></th>
            <th><span class="rm-role rm-finance">Finance Admin</span></th>
            <th><span class="rm-role rm-hou">HOU</span></th>
            <th><span class="rm-role rm-gm">GM</span></th>
            <th><span class="rm-role rm-ceo">CEO</span></th>
            <th><span class="rm-role rm-user">User</span></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><span class="rm-feature">Dashboard</span><span class="rm-desc">View IT System overview, quick actions, and account context.</span></td>
            <td>{!! $yes !!}</td><td>{!! $yes !!}</td><td>{!! $yes !!}</td><td>{!! $yes !!}</td><td>{!! $yes !!}</td><td>{!! $yes !!}</td>
          </tr>
          <tr>
            <td><span class="rm-feature">IT Assets</span><span class="rm-desc">View asset records. HOU, GM, and CEO have view-only access; users submit change requests.</span></td>
            <td>{!! $yes !!}</td><td>{!! $yes !!}</td><td><span class="rm-icon rm-partial" title="Read-only access"><i class="bi bi-eye-fill"></i></span></td><td><span class="rm-icon rm-partial" title="Read-only access"><i class="bi bi-eye-fill"></i></span></td><td><span class="rm-icon rm-partial" title="Read-only access"><i class="bi bi-eye-fill"></i></span></td><td><span class="rm-icon rm-partial" title="Submit requests, not direct edits"><i class="bi bi-send"></i></span></td>
          </tr>
          <tr>
            <td><span class="rm-feature">Non-IT Assets</span><span class="rm-desc">View non-IT asset records. HOU, GM, and CEO have view-only access; users submit change requests.</span></td>
            <td>{!! $yes !!}</td><td>{!! $yes !!}</td><td><span class="rm-icon rm-partial" title="Read-only access"><i class="bi bi-eye-fill"></i></span></td><td><span class="rm-icon rm-partial" title="Read-only access"><i class="bi bi-eye-fill"></i></span></td><td><span class="rm-icon rm-partial" title="Read-only access"><i class="bi bi-eye-fill"></i></span></td><td><span class="rm-icon rm-partial" title="Submit requests, not direct edits"><i class="bi bi-send"></i></span></td>
          </tr>
          <tr>
            <td><span class="rm-feature">Pending / My Requests</span><span class="rm-desc">Track pending inventory requests and personal request history.</span></td>
            <td>{!! $yes !!}</td><td>{!! $yes !!}</td><td>{!! $no !!}</td><td>{!! $no !!}</td><td>{!! $no !!}</td><td>{!! $yes !!}</td>
          </tr>
          <tr>
            <td><span class="rm-feature">Write Off</span><span class="rm-desc">Submit and route write-off items through HOU, GM, and CEO approval.</span></td>
            <td>{!! $yes !!}</td><td>{!! $yes !!}</td><td><span class="rm-icon rm-partial" title="HOU signature step"><i class="bi bi-pen-fill"></i></span></td><td><span class="rm-icon rm-partial" title="GM recommendation step"><i class="bi bi-pen-fill"></i></span></td><td><span class="rm-icon rm-partial" title="CEO approval step"><i class="bi bi-check2-circle"></i></span></td><td>{!! $yes !!}</td>
          </tr>
          <tr>
            <td><span class="rm-feature">Write Off Inventory</span><span class="rm-desc">Finance inventory staging for routing approved write-off batches.</span></td>
            <td>{!! $no !!}</td><td>{!! $yes !!}</td><td>{!! $no !!}</td><td>{!! $no !!}</td><td>{!! $no !!}</td><td>{!! $no !!}</td>
          </tr>
          <tr>
            <td><span class="rm-feature">E-Waste / Disposal</span><span class="rm-desc">Review E-Waste and Disposal records. HOU, GM, and CEO can view only; Admin and Finance manage records.</span></td>
            <td>{!! $yes !!}</td><td>{!! $yes !!}</td><td><span class="rm-icon rm-partial" title="View records only"><i class="bi bi-eye-fill"></i></span></td><td><span class="rm-icon rm-partial" title="View records only"><i class="bi bi-eye-fill"></i></span></td><td><span class="rm-icon rm-partial" title="View records only"><i class="bi bi-eye-fill"></i></span></td><td><span class="rm-icon rm-partial" title="Submit requests where available"><i class="bi bi-send"></i></span></td>
          </tr>
          <tr>
            <td><span class="rm-feature">IT Request Form</span><span class="rm-desc">Create IT requests and process HOU, validator, or admin approval steps.</span></td>
            <td>{!! $yes !!}</td><td>{!! $no !!}</td><td><span class="rm-icon rm-partial" title="Create requests and perform HOU review"><i class="bi bi-check2-square"></i></span></td><td>{!! $no !!}</td><td>{!! $no !!}</td><td>{!! $no !!}</td>
          </tr>
          <tr>
            <td><span class="rm-feature">IT Validator</span><span class="rm-desc">Special reviewer permission controlled by the validator flag, not the role alone.</span></td>
            <td>{!! $no !!}</td><td>{!! $no !!}</td><td><span class="rm-icon rm-partial" title="Only users marked as IT Validator"><i class="bi bi-shield-check"></i></span></td><td>{!! $no !!}</td><td>{!! $no !!}</td><td>{!! $no !!}</td>
          </tr>
          <tr>
            <td><span class="rm-feature">Reports</span><span class="rm-desc">Generate IT and Non-IT asset reports and exports.</span></td>
            <td>{!! $yes !!}</td><td>{!! $yes !!}</td><td>{!! $no !!}</td><td>{!! $no !!}</td><td>{!! $no !!}</td><td>{!! $no !!}</td>
          </tr>
          <tr>
            <td><span class="rm-feature">User, Activity, and Masterdata Admin</span><span class="rm-desc">Manage accounts, review activity logs, and maintain asset classes, brands, and locations.</span></td>
            <td>{!! $yes !!}</td><td>{!! $no !!}</td><td>{!! $no !!}</td><td>{!! $no !!}</td><td>{!! $no !!}</td><td>{!! $no !!}</td>
          </tr>
          <tr>
            <td><span class="rm-feature">Profile, Notifications, and Role Metric</span><span class="rm-desc">Maintain own profile, receive notifications, and read this matrix.</span></td>
            <td>{!! $yes !!}</td><td>{!! $yes !!}</td><td>{!! $yes !!}</td><td>{!! $yes !!}</td><td>{!! $yes !!}</td><td>{!! $yes !!}</td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="rm-note">
      <span><span class="rm-icon rm-yes"><i class="bi bi-check-lg"></i></span> Full access</span>
      <span><span class="rm-icon rm-partial"><i class="bi bi-dash-lg"></i></span> Conditional / limited access</span>
      <span><span class="rm-icon rm-no"><i class="bi bi-x-lg"></i></span> No access</span>
    </div>
  </div>
</div>
