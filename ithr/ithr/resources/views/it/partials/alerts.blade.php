@if(session('success'))
<div class="alert-success-custom">
  <i class="bi bi-check-circle-fill"></i>
  {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert-danger-custom">
  <i class="bi bi-exclamation-circle-fill"></i>
  {{ session('error') }}
</div>
@endif


