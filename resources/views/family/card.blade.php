@php
    $initials = strtoupper(substr($r->display_name, 0, 1));
    $relColors = ['spouse'=>'#8b5cf6','child'=>'#0ea5e9','parent'=>'#f59e0b','sibling'=>'#10b981'];
    $relKey = strtolower($r->relationship);
    $relColor = $relColors[$relKey] ?? '#64748b';
    $indentStyle = $isIndented ? 'margin-left: 2.5rem; position: relative; margin-top: 0.5rem;' : 'margin-bottom: 0.5rem;';       
@endphp

<div class="fc-card" style="{{ $indentStyle }} background: var(--surface); border: 1px solid var(--border); border-radius: 8px; padding: 1rem; display: flex; gap: 1rem; align-items: flex-start;">
    @if($isIndented)
    <div style="position: absolute; left: -1.5rem; top: 1.5rem; width: 1.5rem; border-top: 2px dashed #cbd5e1;"></div>
    <div style="position: absolute; left: -1.5rem; top: -1rem; height: 2.5rem; border-left: 2px dashed #cbd5e1;"></div>
    @endif
    @canwrite
    <input type="checkbox" class="fc-checkbox" value="{{ $r->id }}" data-staff="{{ $r->staff_id }}" onclick="event.stopPropagation()" onchange="fcOnCheck(this)" style="width:16px;height:16px;flex-shrink:0;margin-top:11px;cursor:pointer;accent-color:#6366f1;">
    @endcanwrite
    <div class="fc-avatar" style="background:{{ $relColor }}1a;color:{{ $relColor }}; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0;">{{ $initials }}</div>
    <div class="fc-body" style="flex: 1;">
        <div class="fc-top" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <div class="fc-name" style="font-weight: 600; color: #0f172a;">{{ $r->display_name }}</div>
            <span class="rel-badge rel-{{ $relKey }}" style="font-size: 0.7rem; padding: 0.2rem 0.5rem; border-radius: 4px; background: {{ $relColor }}1a; color: {{ $relColor }}; font-weight: 600;">{{ $r->relationship }}</span>
        </div>
        <div class="fc-fields" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 0.5rem;">
            @if($r->date_of_birth)
            <div class="fc-field">
                <span class="fc-lbl" style="font-size: 0.7rem; color: #64748b; display: block;">Date of Birth</span>
                <span class="fc-val" style="font-size: 0.85rem; color: #334155;">{{ \Carbon\Carbon::parse($r->date_of_birth)->format('d M Y') }}</span>
            </div>
            @endif
            @if($r->phone_number)
            <div class="fc-field">
                <span class="fc-lbl" style="font-size: 0.7rem; color: #64748b; display: block;">Phone</span>
                <span class="fc-val" style="font-size: 0.85rem; color: #334155;">{{ $r->phone_number }}</span>
            </div>
            @endif
            <div class="fc-field">
                <span class="fc-lbl" style="font-size: 0.7rem; color: #64748b; display: block;">Emergency Contact</span>      
                @if($r->emergency_contact === 'Yes')
                <span class="badge-yes" style="font-size: 0.85rem; color: #16a34a; font-weight: 600;">✓ Yes</span>
                @else
                <span class="fc-val muted" style="font-size: 0.85rem; color: #94a3b8;">No</span>
                @endif
            </div>
        </div>
    </div>
    @canwrite
    <div class="fc-actions" style="display: flex; gap: 0.4rem;">
        <button type="button" class="btn btn-sm btn-outline" onclick='event.preventDefault(); event.stopPropagation(); editFamily({!! json_encode($r) !!})'>Edit</button>
        <button type="button" class="btn btn-sm btn-danger" onclick="event.preventDefault(); event.stopPropagation(); confirmFamilyDelete({{ $r->id }})">Delete</button>
    </div>
    @endcanwrite
</div>
