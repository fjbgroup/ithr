@extends('lms.layout.app')
@section('title', 'Manage Courses')
@section('content')
<style>
    .modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; padding: 20px; }
    .modal.show { display: flex; }
    .modal-box { background: var(--body-bg); border-radius: 12px; width: 100%; max-width: 520px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); display: flex; flex-direction: column; overflow: hidden; }
    .modal-header { padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); }
    .modal-header h3 { color: #fff; margin: 0; font-size: 16px; font-family: 'Inter', sans-serif; font-weight: 600; }
    .modal-close { background: none; border: none; color: rgba(255,255,255,0.7); cursor: pointer; font-size: 20px; padding: 0; }
    .modal-close:hover { color: #fff; }
    .modal-body { padding: 20px; flex: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 16px; overflow: visible; }
    .modal-footer { padding: 16px 20px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 12px; background: var(--table-hover); }

    /* Live Search CSS */
    .live-search-wrap { position: relative; width: 100%; font-family: 'Inter', sans-serif; }
    .live-search-input-wrap { display: flex; align-items: center; border: 1.5px solid var(--border); border-radius: 8px; background: var(--body-bg); padding: 0 12px; transition: all 0.2s; }
    .live-search-input-wrap:focus-within { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1); }
    .live-search-icon { color: var(--muted); margin-right: 8px; flex-shrink: 0; }
    .live-search-input { flex: 1; border: none; outline: none; background: transparent; padding: 10px 0; color: var(--text); font-size: 0.9rem; }
    .live-search-clear { background: none; border: none; cursor: pointer; font-size: 1.2rem; color: var(--muted); padding: 0; line-height: 1; margin-left: 8px; }
    .live-search-clear:hover { color: var(--danger); }
    .live-search-results { position: absolute; top: calc(100% + 4px); left: 0; right: 0; background: var(--surface); border: 1.5px solid var(--border); border-radius: 8px; max-height: 200px; overflow-y: auto; z-index: 1010; display: none; box-shadow: 0 4px 16px rgba(0,0,0,0.1); }
    .live-search-results.open { display: block; }
    .live-search-item { padding: 10px 12px; cursor: pointer; font-size: 0.85rem; display: flex; flex-direction: column; border-bottom: 1px solid var(--border); }
    .live-search-item:last-child { border-bottom: none; }
    .live-search-item:hover { background: var(--bg); }
    .live-search-item .ls-main { font-weight: 600; color: var(--text); }
    .live-search-item .ls-sub { font-size: 0.75rem; color: var(--muted); }
    .live-search-item .ls-highlight { color: var(--primary); }
    .live-search-empty { padding: 12px; font-size: 0.85rem; color: var(--muted); text-align: center; }
    .live-search-selected { margin-top: 8px; padding: 8px 12px; background: #eef2ff; border-radius: 6px; font-size: 0.85rem; color: var(--primary); font-weight: 500; display: flex; align-items: center; gap: 8px; }
    .live-search-selected::before { content: '✓'; font-size: 0.9rem; }
</style>

<div class="page-container" style="padding: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h2 style="font-family: 'Inter', sans-serif; font-size: 20px; font-weight: 700; color: var(--text); margin: 0;">Manage Online Courses</h2>
        <button onclick="openModal('course-modal')" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            <span class="btn-label">New Course</span>
        </button>
    </div>

    @if(session('success'))
    <div style="background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.2); color: #15803d; padding: 12px 16px; border-radius: 8px; margin-bottom: 24px; font-size: 14px; display: flex; align-items: center; gap: 8px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        {{ session('success') }}
    </div>
    @endif

    <div class="table-card" style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left;">Code</th>
                    <th style="text-align: left;">Title</th>
                    <th style="text-align: left;">PIC</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($courses as $course)
                <tr>
                    <td style="font-weight: 600;">{{ $course->code }}</td>
                    <td>{{ $course->title }}</td>
                    <td>{{ $course->pic ? $course->pic->name : '-' }}</td>
                    <td style="text-align: right;">
                        <a href="{{ route('lms.courses.show', $course->id) }}" class="btn btn-outline btn-sm" style="display: inline-flex; align-items: center; gap: 6px; margin-right: 8px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            <span>Manage Materials</span>
                        </a>
                        <button onclick="openEditCourseModal({{ $course->id }})" class="btn btn-outline btn-sm" style="display: inline-flex; align-items: center; gap: 6px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            <span>Edit</span>
                        </button>
                    </td>
                </tr>
                @endforeach
                @if($courses->isEmpty())
                <tr>
                    <td colspan="4" style="text-align: center; padding: 32px; color: var(--muted);">
                        No online courses found. Click 'New Course' to create one.
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<!-- Add Course Modal -->
<div class="modal" id="course-modal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Add New Online Course</h3>
            <button class="modal-close" onclick="closeModal('course-modal')">&times;</button>
        </div>
        <form action="{{ route('lms.courses.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div>
                    <label class="form-label" style="display: block; margin-bottom: 8px;">Course Title <span style="color:red;">*</span></label>
                    <input type="text" name="title" required class="form-control" style="width: 100%;" placeholder="e.g. Introduction to Safety">
                </div>
                <div>
                    <label class="form-label" style="display: block; margin-bottom: 8px;">Training Type <span style="color:red;">*</span></label>
                    <select name="training_type" required class="form-select" style="width: 100%;">
                        <option value="Internal">Internal</option>
                        <option value="External">External</option>
                    </select>
                </div>
                <div>
                    <label class="form-label" style="display: block; margin-bottom: 8px;">Company</label>
                    <select name="company" class="form-select" style="width: 100%;">
                        <option value="">-- Optional --</option>
                        @foreach($companies as $c)
                            <option value="{{ $c->name }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label" style="display: block; margin-bottom: 8px;">Department</label>
                    <select name="department" class="form-select" style="width: 100%;">
                        <option value="">-- Optional --</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->name }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display: flex; gap: 16px;">
                    <div style="flex: 1;">
                        <label class="form-label" style="display: block; margin-bottom: 8px;">Start Date</label>
                        <input type="date" name="start_date" class="form-control" style="width: 100%;">
                    </div>
                    <div style="flex: 1;">
                        <label class="form-label" style="display: block; margin-bottom: 8px;">End Date</label>
                        <input type="date" name="end_date" class="form-control" style="width: 100%;">
                    </div>
                </div>
                <div>
                    <label class="form-label" style="display: block; margin-bottom: 8px;">Duration</label>
                    <input type="text" name="duration" class="form-control" style="width: 100%;" placeholder="e.g. 2 Hours">
                </div>
                <div>
                    <label class="form-label" style="display: block; margin-bottom: 8px;">Person In Charge (PIC)</label>
                    <input type="hidden" name="pic_id" id="create-pic-id">
                    <div class="live-search-wrap" id="create-pic-search-wrap">
                        <div class="live-search-input-wrap">
                            <svg class="live-search-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                            <input type="text" id="create-pic-search" class="live-search-input" placeholder="Search by name…" autocomplete="off">
                            <button type="button" class="live-search-clear" id="create-pic-clear" onclick="clearCreatePicSearch()" style="display:none;">×</button>
                        </div>
                        <div class="live-search-results" id="create-pic-results"></div>
                    </div>
                    <div id="create-pic-selected" class="live-search-selected" style="display:none;"></div>
                    <p style="font-size: 11px; color: var(--muted); margin: 6px 0 0 0;">PICs can manage this course.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('course-modal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Course</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Course Modal -->
<div class="modal" id="edit-course-modal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Edit Online Course</h3>
            <button class="modal-close" onclick="closeModal('edit-course-modal')">&times;</button>
        </div>
        <form id="edit-course-form" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div>
                    <label class="form-label" style="display: block; margin-bottom: 8px;">Course Title <span style="color:red;">*</span></label>
                    <input type="text" name="title" id="ec-title" required class="form-control" style="width: 100%;">
                </div>
                <div>
                    <label class="form-label" style="display: block; margin-bottom: 8px;">Training Type <span style="color:red;">*</span></label>
                    <select name="training_type" id="ec-type" required class="form-select" style="width: 100%;">
                        <option value="Internal">Internal</option>
                        <option value="External">External</option>
                    </select>
                </div>
                <div>
                    <label class="form-label" style="display: block; margin-bottom: 8px;">Company</label>
                    <select name="company" id="ec-company" class="form-select" style="width: 100%;">
                        <option value="">-- Optional --</option>
                        @foreach($companies as $c)
                            <option value="{{ $c->name }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label" style="display: block; margin-bottom: 8px;">Department</label>
                    <select name="department" id="ec-department" class="form-select" style="width: 100%;">
                        <option value="">-- Optional --</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->name }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display: flex; gap: 16px;">
                    <div style="flex: 1;">
                        <label class="form-label" style="display: block; margin-bottom: 8px;">Start Date</label>
                        <input type="date" name="start_date" id="ec-start-date" class="form-control" style="width: 100%;">
                    </div>
                    <div style="flex: 1;">
                        <label class="form-label" style="display: block; margin-bottom: 8px;">End Date</label>
                        <input type="date" name="end_date" id="ec-end-date" class="form-control" style="width: 100%;">
                    </div>
                </div>
                <div>
                    <label class="form-label" style="display: block; margin-bottom: 8px;">Duration</label>
                    <input type="text" name="duration" id="ec-duration" class="form-control" style="width: 100%;">
                </div>
                <div>
                    <label class="form-label" style="display: block; margin-bottom: 8px;">Person In Charge (PIC)</label>
                    <input type="hidden" name="pic_id" id="edit-pic-id">
                    <div class="live-search-wrap" id="edit-pic-search-wrap">
                        <div class="live-search-input-wrap">
                            <svg class="live-search-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                            <input type="text" id="edit-pic-search" class="live-search-input" placeholder="Search by name…" autocomplete="off">
                            <button type="button" class="live-search-clear" id="edit-pic-clear" onclick="clearEditPicSearch()" style="display:none;">×</button>
                        </div>
                        <div class="live-search-results" id="edit-pic-results"></div>
                    </div>
                    <div id="edit-pic-selected" class="live-search-selected" style="display:none;"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('edit-course-modal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Course</button>
            </div>
        </form>
    </div>
</div>

<script>
    const courseData = {
        @foreach($courses as $c)
        {{ $c->id }}: {
            title: @json($c->title),
            type: @json($c->training_type),
            company: @json($c->company ?? ''),
            department: @json($c->department ?? ''),
            start_date: @json($c->start_date ? \Carbon\Carbon::parse($c->start_date)->format('Y-m-d') : ''),
            end_date: @json($c->end_date ? \Carbon\Carbon::parse($c->end_date)->format('Y-m-d') : ''),
            duration: @json($c->duration ?? ''),
            pic_id: @json($c->pic_id ?? '')
        },
        @endforeach
    };

    function openModal(id) {
        document.getElementById(id).classList.add('show');
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('show');
    }

    function openEditCourseModal(id) {
        const d = courseData[id];
        if (!d) return;
        document.getElementById('edit-course-form').action = '/lms/courses/' + id;
        document.getElementById('ec-title').value = d.title;
        document.getElementById('ec-type').value = d.type;
        document.getElementById('ec-company').value = d.company;
        document.getElementById('ec-department').value = d.department;
        document.getElementById('ec-start-date').value = d.start_date;
        document.getElementById('ec-end-date').value = d.end_date;
        document.getElementById('ec-duration').value = d.duration;
        document.getElementById('edit-pic-id').value = d.pic_id || '';
        
        if (d.pic_id && allUsersData) {
            const pic = allUsersData.find(u => u.id == d.pic_id);
            if (pic) {
                document.getElementById('edit-pic-search').value = '';
                document.getElementById('edit-pic-clear').style.display = 'none';
                document.getElementById('edit-pic-selected').textContent = pic.name;
                document.getElementById('edit-pic-selected').style.display = 'flex';
            } else {
                if (clearEditPicSearch) clearEditPicSearch();
            }
        } else {
            if (clearEditPicSearch) clearEditPicSearch();
        }
        
        openModal('edit-course-modal');
    }

    const allUsersData = @json($users->map(fn($u) => ['id' => $u->id, 'name' => $u->name]));

    function highlight(text, q) {
        if (!q) return text;
        const i = text.toLowerCase().indexOf(q.toLowerCase());
        if (i === -1) return text;
        return text.slice(0, i) + '<span class="ls-highlight">' + text.slice(i, i + q.length) + '</span>' + text.slice(i + q.length);
    }

    function buildSearchFn(data, inputId, resultsId, hiddenId, selectedId, clearId, mainFn, subFn, labelFn) {
        const input   = document.getElementById(inputId);
        const results = document.getElementById(resultsId);
        const hidden  = document.getElementById(hiddenId);
        const selected= document.getElementById(selectedId);
        const clearBtn= document.getElementById(clearId);

        function select(item) {
            hidden.value = item.id;
            input.value  = '';
            clearBtn.style.display = 'none';
            results.classList.remove('open');
            selected.textContent = labelFn(item);
            selected.style.display = 'flex';
        }

        function clear() {
            hidden.value = '';
            input.value  = '';
            clearBtn.style.display = 'none';
            results.classList.remove('open');
            selected.style.display = 'none';
        }

        input.addEventListener('input', function () {
            const q = this.value.trim();
            clearBtn.style.display = q ? '' : 'none';
            if (!q) { results.classList.remove('open'); return; }
            const matches = data.filter(d =>
                mainFn(d).toLowerCase().includes(q.toLowerCase())
            ).slice(0, 20);
            if (matches.length === 0) {
                results.innerHTML = '<div class="live-search-empty">No results found</div>';
            } else {
                results.innerHTML = matches.map(d =>
                    `<div class="live-search-item" data-id="${d.id}" onclick="void(0)">
                        <span class="ls-main">${highlight(mainFn(d), q)}</span>
                    </div>`
                ).join('');
                results.querySelectorAll('.live-search-item').forEach(el => {
                    el.addEventListener('mousedown', function (e) {
                        e.preventDefault();
                        const id = parseInt(this.dataset.id);
                        const item = data.find(d => d.id === id);
                        if (item) select(item);
                    });
                });
            }
            results.classList.add('open');
            if (hidden.value) { selected.style.display = 'none'; hidden.value = ''; }
        });

        input.addEventListener('blur', function () {
            setTimeout(() => results.classList.remove('open'), 150);
        });

        return clear;
    }

    let clearCreatePicSearch, clearEditPicSearch;

    document.addEventListener('DOMContentLoaded', function () {
        clearCreatePicSearch = buildSearchFn(
            allUsersData,
            'create-pic-search', 'create-pic-results', 'create-pic-id', 'create-pic-selected', 'create-pic-clear',
            d => d.name, d => '', d => d.name
        );
        clearEditPicSearch = buildSearchFn(
            allUsersData,
            'edit-pic-search', 'edit-pic-results', 'edit-pic-id', 'edit-pic-selected', 'edit-pic-clear',
            d => d.name, d => '', d => d.name
        );
    });
</script>
@endsection