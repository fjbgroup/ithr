@extends(request()->routeIs('wt.admin.*') ? 'wt.layouts.admin' : 'wt.layouts.user')

@section('title', 'Policies')

@section('content')
<div class="px-2">
<div class="mb-3">
    <h3 class="text-sm font-extrabold text-[#142b47] tracking-tight">Policies</h3>
    <p class="text-stone-400 font-medium mt-0.5 text-[9px] tracking-widest uppercase">
        Walkie Talkie Usage Policies &amp; Terms.
    </p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-[#0284c7]/10 overflow-hidden">
    <div class="p-8 text-center bg-stone-50/50">
        <i class="fas fa-file-contract text-3xl text-stone-200 mb-4"></i>
        <h4 class="text-[11px] font-black text-[#142b47] uppercase tracking-wider mb-2">Content Under Development</h4>
        <p class="text-stone-400 text-[10px] uppercase font-bold tracking-widest">This page will be updated with the latest policies soon.</p>
    </div>
</div>
</div>
@endsection

