import re

filepath = 'resources/views/layouts/app.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Remove the old My Requests block (lines 374-386 approx)
old_block_pattern = r'@if\(!Auth::user\(\)->isAdmin\(\) && !Auth::user\(\)->isCeo\(\)\)\s*<a href="\{\{ url\(\'/my-requests\'\) \}\}".*?</a>\s*@endif\n'
content = re.sub(old_block_pattern, '', content, flags=re.DOTALL)

# 2. Add 'my-requests' to the in_array checks for navGroupRegistry
content = content.replace("['staff', 'family', 'report', 'ir', 'archived-staff']", "['staff', 'family', 'report', 'ir', 'archived-staff', 'my-requests']")

# 3. Inject the new nav-child into the navGroupRegistry's children-inner
nav_child_html = r"""
                    @if(!Auth::user()->isAdmin() && !Auth::user()->isCeo())
                    <a href="{{ url('/my-requests') }}" class="nav-child {{ request()->is('my-requests*') ? 'active' : '' }}" style="display:flex;justify-content:space-between;align-items:center;">
                        My Requests
                        @php
                            $myPendingCount = App\Models\UpdateRequest::where('requester_id', Auth::id())->where('status', 'Pending')->count();
                            $myRequestBadge = $myPendingCount + Auth::user()->getUnreadRequestCount();
                        @endphp
                        @if ($myRequestBadge > 0)
                            <span class="badge-count" id="nav-badge-request" style="margin-left:auto;">{{ $myRequestBadge }}</span>
                        @endif
                    </a>
                    @endif
"""

registry_end_marker = r"""                    @if(Auth::user()->isAdmin() || Auth::user()->isCeo())
                    <a href="{{ url('/archived-staff') }}" class="nav-child {{ request()->is('archived-staff*') ? 'active' : '' }}">Archived</a>
                    @endif
                </div>"""

registry_replacement = r"""                    @if(Auth::user()->isAdmin() || Auth::user()->isCeo())
                    <a href="{{ url('/archived-staff') }}" class="nav-child {{ request()->is('archived-staff*') ? 'active' : '' }}">Archived</a>
                    @endif""" + nav_child_html + r"""                </div>"""

content = content.replace(registry_end_marker, registry_replacement)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(content)
