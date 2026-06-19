@extends('wt.layouts.admin') {{-- Ganti 'layouts.admin' dengan nama fail layout utama anda --}}

@section('title', 'Admin General')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-stone-200 overflow-hidden">
    <div class="p-6 border-b border-stone-100 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-bold text-[#3D2B1F]">Admin General Management</h3>
            <p class="text-xs text-stone-500">Manage information in the Walkie Talkie system here.</p>
        </div>
        <button class="bg-[#A67B5B] hover:bg-[#8d684d] text-white px-4 py-2 rounded-lg text-xs font-bold transition">
            <i class="fas fa-plus mr-2"></i> ADD DATA
        </button>
    </div>

    <div class="p-6">
        <div class="border-2 border-dashed border-stone-200 rounded-xl p-12 text-center">
            <div class="bg-stone-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-folder-open text-stone-400 text-xl"></i>
            </div>
            <h4 class="text-stone-600 font-semibold">Data not found</h4>
            <p class="text-stone-400 text-xs mt-1">Please add new information to view the list here.</p>
        </div>
    </div>
</div>
@endsection
