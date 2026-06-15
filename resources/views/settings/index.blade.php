@extends('layouts.app')

@section('title', 'System Settings')

@section('content')
<div class="page-header">
    <div>
        <h2>System Settings</h2>
        <p class="page-subtitle">Control which modules are accessible to Admin HR and Staff roles</p>
    </div>
</div>

<div class="card" style="max-width:600px;">
    <div class="card-header"><h3>Module Access Control</h3></div>
    <form method="POST" action="{{ route('settings.store') }}">
        @csrf
        <div style="padding:0 1rem;">
        @foreach ($modules as $key => $label)
        <div style="display:flex;align-items:center;justify-content:space-between;padding:1rem 0;border-bottom:1px solid #f1f5f9;">
            <div>
                <strong>{{ $label }}</strong>
                <p style="color:#64748b;font-size:.85rem;margin:.2rem 0 0;">Module key: <code>module_{{ $key }}</code></p>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" name="module_{{ $key }}" value="1" {{ $settings[$key] ? 'checked' : '' }}>
                <span class="toggle-slider"></span>
            </label>
        </div>
        @endforeach
        </div>
        <div class="modal-footer" style="border-top:1px solid #f1f5f9;">
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </div>
    </form>
</div>

<div class="card" style="max-width:600px; margin-top:1.5rem;">
    <div class="card-header"><h3>Role Permissions Overview</h3></div>
    <div class="table-responsive" style="padding:1rem;">
        <table class="table">
            <thead><tr><th>Module</th><th>Admin IT</th><th>Admin HR</th><th>Staff</th></tr></thead>
            <tbody>
            <tr><td>Staff Registry</td><td>✅ Full CRUD</td><td>✅ Add/Edit</td><td>❌</td></tr>
            <tr><td>Training Records</td><td>✅ Full CRUD</td><td>✅ Add/Edit</td><td>👁️ View Only</td></tr>
            <tr><td>Family Information</td><td>✅ Full CRUD</td><td>✅ Add/Edit</td><td>👁️ View Only</td></tr>
            <tr><td>Meeting Rooms</td><td>✅ Full CRUD</td><td>✅ Book</td><td>✅ Book</td></tr>
            <tr><td>Update Requests</td><td>✅ Manage</td><td>✅ Manage</td><td>📝 Submit</td></tr>
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('styles')
<style>
.toggle-switch {
  position: relative;
  display: inline-block;
  width: 44px;
  height: 24px;
}
.toggle-switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}
.toggle-slider {
  position: absolute;
  cursor: pointer;
  top: 0; left: 0; right: 0; bottom: 0;
  background-color: #cbd5e1;
  transition: .2s;
  border-radius: 34px;
}
.toggle-slider:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: .2s;
  border-radius: 50%;
  box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}
input:checked + .toggle-slider {
  background-color: #10b981;
}
input:focus + .toggle-slider {
  box-shadow: 0 0 1px #10b981;
}
input:checked + .toggle-slider:before {
  transform: translateX(20px);
}
</style>
@endsection
