@extends('it.layouts.app')

@section('title', 'User Manual')
@section('page_title', 'User Manual')

@section('content')
<!-- Custom styles for User Manual within Content Section -->
<style>
    .nav-pills .nav-link {
        color: var(--text, #334155);
        background: rgba(0,0,0,0.03);
        border: 1px solid var(--border);
    }
    .nav-pills .nav-link:hover:not(.active) {
        background: var(--body-bg, #f1f5f9);
        color: var(--accent, #0284c7);
    }
    .nav-pills .nav-link.active {
        background: var(--sidebar-active-bg, linear-gradient(135deg,#FFB84D 0%,#F7941D 60%,#C96800 100%)) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(247, 148, 29, 0.2);
        border-color: transparent;
    }
    .dark .nav-pills .nav-link {
        color: #cbd5e1;
        background: rgba(255,255,255,0.03);
        border-color: #334155;
    }
    .dark .nav-pills .nav-link:hover:not(.active) {
        background: #263042;
        color: #38bdf8;
    }
    .manual-content {
        line-height: 1.8;
        color: var(--text, #334155);
        font-size: 0.95rem;
    }
    .manual-content h1 {
        font-size: 1.6rem;
        font-weight: 800;
        margin-top: 0;
        margin-bottom: 1.5rem;
        border-bottom: 2px solid var(--border);
        padding-bottom: 0.75rem;
        color: var(--accent, #0284c7);
    }
    .manual-content h2 {
        font-size: 1.25rem;
        font-weight: 700;
        margin-top: 2rem;
        margin-bottom: 1rem;
        border-bottom: 1.5px solid var(--border);
        padding-bottom: 0.5rem;
        color: var(--text, #334155);
    }
    .manual-content h3 {
        font-size: 1.05rem;
        font-weight: 600;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
        color: var(--text, #334155);
    }
    .manual-content p {
        margin-bottom: 1.25rem;
        color: var(--muted, #64748b);
    }
    .dark .manual-content p {
        color: #cbd5e1;
    }
    .manual-content ul, .manual-content ol {
        margin-bottom: 1.25rem;
        padding-left: 1.5rem;
        color: var(--muted, #64748b);
    }
    .dark .manual-content ul, .dark .manual-content ol {
        color: #cbd5e1;
    }
    .manual-content li {
        margin-bottom: 0.5rem;
    }
    .manual-content strong {
        color: var(--text, #334155);
    }
    .dark .manual-content strong {
        color: #f1f5f9;
    }
    .manual-content code {
        background: var(--body-bg, #f1f5f9);
        color: #e06c75;
        padding: 0.2rem 0.4rem;
        border-radius: 4px;
        font-size: 0.85rem;
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    }
    .manual-content pre {
        background: var(--body-bg, #f1f5f9);
        padding: 1.25rem;
        border-radius: 8px;
        overflow-x: auto;
        margin-bottom: 1.25rem;
        border: 1px solid var(--border);
    }
    .manual-content pre code {
        background: transparent;
        color: inherit;
        padding: 0;
        border-radius: 0;
        font-size: 0.9rem;
    }
    .manual-content hr {
        border: 0;
        border-top: 1px solid var(--border);
        margin: 2rem 0;
    }

    /* Highlight Search Match */
    .highlight {
        background-color: #fef08a !important;
        color: #000000 !important;
        padding: 0.1rem 0.2rem;
        border-radius: 3px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .dark .highlight {
        background-color: #854d0e !important;
        color: #ffffff !important;
    }

    /* Alerts parsing styling */
    .manual-alert {
        padding: 1rem;
        border-left: 4px solid #cbd5e1;
        background: var(--body-bg, #f1f5f9);
        border-radius: 0 8px 8px 0;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
    }
    .manual-alert.alert-note {
        border-left-color: #0284c7;
        background: rgba(2, 132, 199, 0.05);
    }
    .manual-alert.alert-important {
        border-left-color: #7c3aed;
        background: rgba(124, 58, 237, 0.05);
    }
    .manual-alert.alert-warning {
        border-left-color: #e11d48;
        background: rgba(225, 29, 72, 0.05);
    }
    .manual-alert.alert-caution {
        border-left-color: #ea580c;
        background: rgba(234, 88, 12, 0.05);
    }
    .manual-alert.alert-tip {
        border-left-color: #16a34a;
        background: rgba(22, 163, 74, 0.05);
    }
</style>

<div class="row g-4">
    <!-- Top Filter Section -->
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; background: var(--surface);">
            <div class="card-body p-4">
                <div class="d-flex flex-column gap-3">
                    <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: .08em; color: var(--muted);">Documents</div>
                    
                    <!-- Tab Links -->
                    <ul class="nav nav-pills gap-2 flex-wrap" id="manualTabs">
                        @php $index = 0; @endphp
                        @foreach($manuals as $key => $manual)
                            <li class="nav-item">
                                <button class="nav-link px-3 py-2 d-flex align-items-center gap-2 {{ $index === 0 ? 'active' : '' }}" 
                                        data-tab="{{ $key }}" 
                                        onclick="switchTab('{{ $key }}')"
                                        style="border-radius: 8px; font-size: 12px; font-weight: 700; transition: all 0.2s;">
                                    {!! $manual['icon'] !!}
                                    <span>{{ $manual['title'] }}</span>
                                </button>
                            </li>
                            @php $index++; @endphp
                        @endforeach
                    </ul>

                    <!-- Search Input -->
                    <div class="input-group mt-1" style="max-width: 480px;">
                        <span class="input-group-text bg-light border-end-0 text-muted" style="border-color: var(--border);"><i class="bi bi-search"></i></span>
                        <input type="text" id="manualSearch" class="form-control bg-light border-start-0 ps-0" placeholder="Search this manual..." oninput="performSearch()" style="font-size: 13px; border-color: var(--border);">
                        <span class="input-group-text bg-light text-muted border-start-0" id="searchStats" style="display:none; font-size:11px; font-weight:700; border-color: var(--border);"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Viewer Section -->
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; background: var(--surface);">
            <div class="card-body p-4 p-md-5">
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
</div>

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
                let icon = `<i class="bi bi-info-circle-fill" style="margin-right: 6px; vertical-align: -1px;"></i>`;
                
                if (text.includes('[!IMPORTANT]')) {
                    type = 'important';
                    labelText = 'IMPORTANT';
                    icon = `<i class="bi bi-exclamation-octagon-fill" style="margin-right: 6px; vertical-align: -1px;"></i>`;
                } else if (text.includes('[!WARNING]')) {
                    type = 'warning';
                    labelText = 'WARNING';
                    icon = `<i class="bi bi-exclamation-triangle-fill" style="margin-right: 6px; vertical-align: -1px;"></i>`;
                } else if (text.includes('[!CAUTION]')) {
                    type = 'caution';
                    labelText = 'CAUTION';
                    icon = `<i class="bi bi-slash-circle-fill" style="margin-right: 6px; vertical-align: -1px;"></i>`;
                } else if (text.includes('[!TIP]')) {
                    type = 'tip';
                    labelText = 'TIP';
                    icon = `<i class="bi bi-lightbulb-fill" style="margin-right: 6px; vertical-align: -1px;"></i>`;
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
        document.querySelectorAll('#manualTabs .nav-link').forEach(btn => {
            btn.classList.remove('active');
        });
        const activeBtn = document.querySelector(`#manualTabs .nav-link[data-tab="${tabKey}"]`);
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
