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

@if ($errors->any())
<div class="alert-danger-custom">
  <i class="bi bi-exclamation-circle-fill"></i>
  <ul style="margin: 0; padding-left: 20px;">
      @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
      @endforeach
  </ul>
</div>
@endif
