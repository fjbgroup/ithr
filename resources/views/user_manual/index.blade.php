@extends('layouts.app')

@section('title', 'User Manual')

@section('styles')
<style>
    .manual-layout {
        display: flex;
        gap: 1.75rem;
        margin-top: 1rem;
        align-items: flex-start;
    }

    .manual-sidebar {
        width: 250px;
        flex-shrink: 0;
        position: sticky;
        top: 80px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 0.75rem;
        box-shadow: var(--shadow);
    }

    .manual-sidebar-title {
        font-weight: 700;
        font-size: 0.85rem;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.75rem;
        padding-left: 0.5rem;
    }

    .manual-nav-list {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .manual-tab-link {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.65rem 0.85rem;
        border-radius: 6px;
        color: var(--text);
        text-decoration: none;
        font-weight: 500;
        font-size: 0.9rem;
        transition: all var(--transition);
        border: none;
        background: transparent;
        text-align: left;
        width: 100%;
        cursor: pointer;
    }

    .manual-tab-link svg {
        color: var(--muted);
        transition: color var(--transition);
        flex-shrink: 0;
    }

    .manual-tab-link:hover {
        background: var(--sidebar-hover-bg);
        color: var(--sidebar-text-hover);
    }

    .manual-tab-link:hover svg {
        color: var(--text);
    }

    .manual-tab-link.active {
        background: var(--sidebar-active-bg);
        color: var(--sidebar-active-text);
        font-weight: 600;
    }

    .manual-tab-link.active svg {
        color: var(--sidebar-active-text);
    }

    .manual-main {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .manual-search-box {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1rem;
        box-shadow: var(--shadow);
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .manual-search-input-wrapper {
        position: relative;
        flex: 1;
    }

    .manual-search-input-wrapper svg {
        position: absolute;
        left: 0.85rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--muted);
        pointer-events: none;
    }

    .manual-search-input {
        width: 100%;
        padding: 0.65rem 1rem 0.65rem 2.3rem;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: var(--form-input-bg);
        color: var(--form-input-color);
        font-size: 0.9rem;
        outline: none;
        transition: border-color var(--transition);
    }

    .manual-search-input:focus {
        border-color: var(--primary);
    }

    .manual-search-stats {
        font-size: 0.85rem;
        color: var(--muted);
        white-space: nowrap;
    }

    .manual-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 2.25rem;
        box-shadow: var(--shadow);
    }

    .manual-content {
        color: var(--text);
        line-height: 1.7;
        font-size: 0.95rem;
    }

    /* Markdown Styling Inside Manual */
    .manual-content h1 {
        font-size: 1.8rem;
        font-weight: 800;
        margin-bottom: 1.5rem;
        border-bottom: 2px solid var(--border);
        padding-bottom: 0.5rem;
        color: var(--navy);
    }

    .manual-content h2 {
        font-size: 1.4rem;
        font-weight: 700;
        margin-top: 2rem;
        margin-bottom: 1rem;
        color: var(--navy);
    }

    .manual-content h3 {
        font-size: 1.15rem;
        font-weight: 600;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
        color: var(--navy);
    }

    .manual-content p {
        margin-bottom: 1.15rem;
    }

    .manual-content ul, .manual-content ol {
        margin-bottom: 1.25rem;
        padding-left: 1.5rem;
    }

    .manual-content li {
        margin-bottom: 0.4rem;
    }

    .manual-content hr {
        border: 0;
        height: 1px;
        background: var(--border);
        margin: 2rem 0;
    }

    /* Alert styles (parsed blockquotes) */
    .manual-content blockquote, .manual-content .manual-alert {
        padding: 0.85rem 1.15rem;
        margin: 1.25rem 0;
        border-radius: 8px;
        font-size: 0.9rem;
        border-left: 4px solid var(--border);
        background: var(--bg);
        color: var(--text);
    }

    .manual-content .manual-alert p {
        margin-bottom: 0;
    }

    .manual-content .manual-alert.alert-note {
        border-left-color: #0284c7;
        background: rgba(2, 132, 199, 0.08);
    }
    .manual-content .manual-alert.alert-note strong {
        color: #0284c7;
    }

    .manual-content .manual-alert.alert-important {
        border-left-color: #f59e0b;
        background: rgba(245, 158, 11, 0.08);
    }
    .manual-content .manual-alert.alert-important strong {
        color: #d97706;
    }

    .manual-content .manual-alert.alert-warning {
        border-left-color: #ea580c;
        background: rgba(234, 88, 12, 0.08);
    }
    .manual-content .manual-alert.alert-warning strong {
        color: #ca8a04;
    }

    .manual-content .manual-alert.alert-caution {
        border-left-color: #dc2626;
        background: rgba(220, 38, 38, 0.08);
    }
    .manual-content .manual-alert.alert-caution strong {
        color: #b91c1c;
    }

    .manual-content .manual-alert.alert-tip {
        border-left-color: #16a34a;
        background: rgba(22, 163, 74, 0.08);
    }
    .manual-content .manual-alert.alert-tip strong {
        color: #15803d;
    }

    /* Table styles */
    .manual-content table {
        width: 100%;
        border-collapse: collapse;
        margin: 1.5rem 0;
        font-size: 0.9rem;
    }

    .manual-content th, .manual-content td {
        padding: 0.65rem 0.85rem;
        border: 1px solid var(--border);
        text-align: left;
    }

    .manual-content th {
        background: var(--table-head-bg);
        font-weight: 600;
    }

    .manual-content tr:nth-child(even) {
        background: var(--table-row-alt);
    }

    /* Highlights for search */
    mark.highlight {
        background: #fef08a;
        color: #000;
        border-radius: 2px;
        padding: 0 2px;
    }

    @media (max-width: 768px) {
        .manual-layout {
            flex-direction: column;
        }

        .manual-sidebar {
            width: 100%;
            position: relative;
            top: 0;
            margin-bottom: 1rem;
        }
        
        .manual-nav-list {
            flex-direction: row;
            overflow-x: auto;
            white-space: nowrap;
        }

        .manual-tab-link {
            width: auto;
        }
    }
</style>
@endsection

@section('content')
<div class="hd-banner" style="margin-bottom: 1.5rem;">
    <div class="hd-banner-left">
        <div class="hd-greeting">User Manual</div>
        <div class="hd-date">Help documents & operational guides tailored for your role access level</div>
    </div>
</div>

<div class="manual-layout">
    @if(count($manuals) > 1)
    <!-- Sidebar tab selector (only shown if user has access to more than 1 manual) -->
    <aside class="manual-sidebar">
        <div class="manual-sidebar-title">Select Manual</div>
        <nav class="manual-nav-list">
            @php $index = 0; @endphp
            @foreach($manuals as $key => $manual)
                <button class="manual-tab-link {{ $index === 0 ? 'active' : '' }}" 
                        onclick="switchTab('{{ $key }}')" 
                        data-tab="{{ $key }}">
                    {!! $manual['icon'] !!}
                    <span>{{ $manual['title'] }}</span>
                </button>
                @php $index++; @endphp
            @endforeach
        </nav>
    </aside>
    @endif

    <div class="manual-main">
        <!-- Live search box -->
        <div class="manual-search-box">
            <div class="manual-search-input-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="manualSearch" class="manual-search-input" placeholder="Search keywords in manual..." oninput="performSearch()">
            </div>
            <div class="manual-search-stats" id="searchStats" style="display: none;">0 matches</div>
        </div>

        <!-- Content Card -->
        <div class="manual-card">
            @php $index = 0; @endphp
            @foreach($manuals as $key => $manual)
                <div class="manual-content-wrapper" id="content-{{ $key }}" style="display: {{ $index === 0 ? 'block' : 'none' }};">
                    <div class="manual-content">
                        {!! $manual['html'] !!}
                    </div>
                </div>
                @php $index++; @endphp
            @endforeach
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let activeTab = '{{ array_key_first($manuals) }}';
    
    // Store original HTML contents to allow clearing search highlighting
    const originalContents = {};
    document.querySelectorAll('.manual-content-wrapper').forEach(wrapper => {
        const id = wrapper.id.replace('content-', '');
        originalContents[id] = wrapper.querySelector('.manual-content').innerHTML;
    });

    // Parse blockquotes and convert [!NOTE], etc. to proper alert classes
    function parseAlerts() {
        document.querySelectorAll('.manual-content blockquote').forEach(bq => {
            const text = bq.innerHTML.trim();
            if (
                text.startsWith('<p>[!NOTE]') || 
                text.startsWith('<p>[!IMPORTANT]') || 
                text.startsWith('<p>[!WARNING]') || 
                text.startsWith('<p>[!CAUTION]') || 
                text.startsWith('<p>[!TIP]')
            ) {
                let type = 'note';
                let labelText = 'NOTE';
                let icon = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 6px; vertical-align: -3px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>`;
                
                if (text.includes('[!IMPORTANT]')) {
                    type = 'important';
                    labelText = 'IMPORTANT';
                    icon = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 6px; vertical-align: -3px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>`;
                } else if (text.includes('[!WARNING]')) {
                    type = 'warning';
                    labelText = 'WARNING';
                    icon = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 6px; vertical-align: -3px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>`;
                } else if (text.includes('[!CAUTION]')) {
                    type = 'caution';
                    labelText = 'CAUTION';
                    icon = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 6px; vertical-align: -3px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>`;
                } else if (text.includes('[!TIP]')) {
                    type = 'tip';
                    labelText = 'TIP';
                    icon = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 6px; vertical-align: -3px;"><path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.808 13.066a6 6 0 1 1 7.116 0H9.663z"/></svg>`;
                }

                bq.className = `manual-alert alert-${type}`;
                
                // Replace the paragraph tag pattern
                let inner = bq.innerHTML;
                inner = inner.replace(/<p>\s*\[!(NOTE|IMPORTANT|WARNING|CAUTION|TIP)\]/gi, '<p>');
                bq.innerHTML = `<div style="margin-bottom: 4px;"><strong>${icon} ${labelText}</strong></div>` + inner;
            }
        });
        
        // Update the originalContents to include the parsed alerts
        document.querySelectorAll('.manual-content-wrapper').forEach(wrapper => {
            const id = wrapper.id.replace('content-', '');
            originalContents[id] = wrapper.querySelector('.manual-content').innerHTML;
        });
    }

    // Run on boot
    parseAlerts();

    function switchTab(tabKey) {
        activeTab = tabKey;
        
        // Hide all tabs
        document.querySelectorAll('.manual-content-wrapper').forEach(wrapper => {
            wrapper.style.display = 'none';
        });

        // Show active tab
        document.getElementById(`content-${tabKey}`).style.display = 'block';

        // Toggle button active classes
        document.querySelectorAll('.manual-tab-link').forEach(btn => {
            btn.classList.remove('active');
        });
        const activeBtn = document.querySelector(`.manual-tab-link[data-tab="${tabKey}"]`);
        if (activeBtn) activeBtn.classList.add('active');

        // Clear search or re-perform search on new tab
        performSearch();
    }

    function performSearch() {
        const query = document.getElementById('manualSearch').value.trim();
        const contentDiv = document.querySelector(`#content-${activeTab} .manual-content`);
        const statsSpan = document.getElementById('searchStats');

        if (!query) {
            contentDiv.innerHTML = originalContents[activeTab];
            statsSpan.style.display = 'none';
            return;
        }

        // Search original content to prevent searching highlighted HTML
        const sourceHtml = originalContents[activeTab];
        
        // Match only text nodes, avoiding modifying HTML tags/attributes
        // A robust client-side highlighter regex
        const regexQuery = query.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&'); // escape regex chars
        const regex = new RegExp(`(${regexQuery})`, 'gi');

        // Parse HTML to search and replace text node values
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = sourceHtml;
        
        let matchCount = 0;

        function highlightTextNodes(node) {
            if (node.nodeType === Node.TEXT_NODE) {
                const text = node.nodeValue;
                if (text.match(regex)) {
                    const span = document.createElement('span');
                    span.innerHTML = text.replace(regex, (match) => {
                        matchCount++;
                        return `<mark class="highlight">${match}</mark>`;
                    });
                    
                    // Replace text node with elements
                    node.parentNode.insertBefore(span, node);
                    node.parentNode.removeChild(node);
                }
            } else if (node.nodeType === Node.ELEMENT_NODE && node.nodeName !== 'SCRIPT' && node.nodeName !== 'STYLE') {
                for (let i = node.childNodes.length - 1; i >= 0; i--) {
                    highlightTextNodes(node.childNodes[i]);
                }
            }
        }

        highlightTextNodes(tempDiv);

        contentDiv.innerHTML = tempDiv.innerHTML;
        
        // Show count
        statsSpan.style.display = 'inline-block';
        statsSpan.textContent = `${matchCount} ${matchCount === 1 ? 'match' : 'matches'}`;
    }
</script>
@endsection
