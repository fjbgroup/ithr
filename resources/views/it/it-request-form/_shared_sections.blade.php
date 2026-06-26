{{-- Shared form sections: User & Justification, Requester Details, Approver Details --}}
{{-- Included by software, system, and service forms. $formPrefix = 'sw'|'sys'|'svc' --}}

<div class="itr-section">
  <div class="itr-section-head"><div class="itr-section-num">2</div><div class="itr-section-title">Type of User &amp; Justification</div></div>
  <div class="itr-section-body">
    <div class="g2 fg">
      <div>
        <div class="fg">
          <div class="itr-label">Type of User <span class="itr-req">*</span></div>
          <select class="itr-input{{ $errors->has('user_type') ? ' is-error' : '' }}" name="user_type" required>
            <option value="">-- Select --</option>
            <option {{ old('user_type') === 'New Hire'  ? 'selected' : '' }}>New Hire</option>
            <option {{ old('user_type') === 'Intern'    ? 'selected' : '' }}>Intern</option>
            <option {{ old('user_type') === 'Resign'    ? 'selected' : '' }}>Resign</option>
            <option {{ old('user_type') === 'Existing'  ? 'selected' : '' }}>Existing</option>
            <option {{ old('user_type') === 'Vendor'    ? 'selected' : '' }}>Vendor</option>
          </select>
          @error('user_type')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
        </div>
        <div class="fg">
          <div class="itr-label">Date <span class="itr-req">*</span></div>
          <input class="itr-input" type="date" name="exit_join_date" value="{{ old('exit_join_date') }}" style="max-width:200px" required/>
          <div class="itr-hint">MM/DD/YYYY</div>
        </div>
      </div>
      <div>
        <div class="itr-label">Justification <span class="itr-req">*</span></div>
        <textarea class="itr-input{{ $errors->has('justification') ? ' is-error' : '' }}" name="justification" placeholder="Describe why this request is needed…" required>{{ old('justification') }}</textarea>
        @error('justification')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
      </div>
    </div>
    <div class="itr-label" style="margin-bottom:10px">Supporting Document <span style="font-weight:400;color:var(--muted)">(Optional)</span></div>
    <div class="itr-upload-zone">
      <div class="itr-notice info"><i class="bi bi-info-circle-fill"></i><div>Sila pastikan nama lampiran tidak mengandungi simbol berikut: &amp; @ # $ % ^ * ( ) { } [ ] \ / : ' " dan saiz lampiran tidak melebihi <strong>2MB</strong></div></div>
      <div class="itr-upload-row">
        <div class="itr-filename" id="{{ $formPrefix }}-fname">No file chosen</div>
        <input type="file" id="{{ $formPrefix }}-file" name="document" onchange="setFilename('{{ $formPrefix }}-file','{{ $formPrefix }}-fname')"/>
        <label for="{{ $formPrefix }}-file" class="itr-browse-btn"><i class="bi bi-paperclip"></i> Browse</label>
      </div>
    </div>
  </div>
</div>

<div class="itr-section">
  <div class="itr-section-head"><div class="itr-section-num">3</div><div class="itr-section-title">Requester Details</div></div>
  <div class="itr-section-body">
    <div class="g3 fg">
      <div class="fg">
        <div class="itr-label">Name <span class="itr-req">*</span></div>
        <input type="hidden" name="req_name" id="{{ $formPrefix }}_req_name_val" value="{{ old('req_name', $user->full_name) }}">
        <div class="itr-name-dd{{ $errors->has('req_name') ? ' is-error' : '' }}" id="{{ $formPrefix }}_req_name_wrap">
          <div class="itr-name-trigger" id="{{ $formPrefix }}_req_name_trigger" onclick="toggleStaffDD('{{ $formPrefix }}_req_name')">
            <span class="itr-name-display{{ old('req_name', $user->full_name) ? '' : ' is-placeholder' }}" id="{{ $formPrefix }}_req_name_display">{{ old('req_name', $user->full_name) ?: '-- Select staff name --' }}</span>
            <i class="bi bi-chevron-down itr-name-arrow"></i>
          </div>
          <div class="itr-name-panel" id="{{ $formPrefix }}_req_name_panel" style="display:none">
            <input class="itr-name-search-input" type="text" id="{{ $formPrefix }}_req_name_search" placeholder="Filter by name…" oninput="filterStaffDD('{{ $formPrefix }}_req_name',this.value)" autocomplete="off">
            <div class="itr-name-list" id="{{ $formPrefix }}_req_name_list"></div>
          </div>
        </div>
        @error('req_name')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
      </div>
      <div class="fg">
        <div class="itr-label">Department <span class="itr-req">*</span></div>
        <input class="itr-input{{ $errors->has('req_department') ? ' is-error' : '' }}" type="text" id="{{ $formPrefix }}_req_department" name="req_department" value="{{ old('req_department', $user->dept_name ?? '') }}" required/>
        @error('req_department')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
      </div>
      <div class="fg">
        <div class="itr-label">Staff ID <span class="itr-req">*</span></div>
        <input class="itr-input{{ $errors->has('req_staff_id') ? ' is-error' : '' }}" type="text" id="{{ $formPrefix }}_req_staff_id" name="req_staff_id" value="{{ old('req_staff_id', $user->department ?? '') }}" required/>
        @error('req_staff_id')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
      </div>
    </div>
    <div class="g2">
      <div class="fg">
        <div class="itr-label">Designation <span class="itr-req">*</span></div>
        <input class="itr-input{{ $errors->has('req_designation') ? ' is-error' : '' }}" type="text" id="{{ $formPrefix }}_req_designation" name="req_designation" value="{{ old('req_designation', $user->position ?? '') }}" required/>
        @error('req_designation')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
      </div>
      <div class="fg">
        <div class="itr-label">Contact <span class="itr-req">*</span></div>
        <input class="itr-input{{ $errors->has('req_contact') ? ' is-error' : '' }}" type="text" name="req_contact" value="{{ old('req_contact', $user->email ?? '') }}" required/>
        @error('req_contact')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
      </div>
    </div>
  </div>
</div>

<div class="itr-section">
  <div class="itr-section-head"><div class="itr-section-num">4</div><div class="itr-section-title">Approver Details</div></div>
  <div class="itr-section-body">
    <div class="fg">
      <div class="itr-label">Name <span class="itr-req">*</span></div>
      <input type="hidden" name="approver_name" id="{{ $formPrefix }}_approver_name_val" value="{{ old('approver_name') }}">
      <div class="itr-name-dd{{ $errors->has('approver_name') ? ' is-error' : '' }}" id="{{ $formPrefix }}_approver_name_wrap">
        <div class="itr-name-trigger" id="{{ $formPrefix }}_approver_name_trigger" onclick="toggleHouDD('{{ $formPrefix }}_approver_name')">
          <span class="itr-name-display{{ old('approver_name') ? '' : ' is-placeholder' }}" id="{{ $formPrefix }}_approver_name_display">{{ old('approver_name') ?: '-- Select approver name --' }}</span>
          <i class="bi bi-chevron-down itr-name-arrow"></i>
        </div>
        <div class="itr-name-panel" id="{{ $formPrefix }}_approver_name_panel" style="display:none">
          <input class="itr-name-search-input" type="text" id="{{ $formPrefix }}_approver_name_search" placeholder="Filter by name…" oninput="filterHouDD('{{ $formPrefix }}_approver_name',this.value)" autocomplete="off">
          <div class="itr-name-list" id="{{ $formPrefix }}_approver_name_list"></div>
        </div>
      </div>
      @error('approver_name')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
    </div>
    <div class="g3">
      <div class="fg">
        <div class="itr-label">Department <span class="itr-req">*</span></div>
        <input class="itr-input{{ $errors->has('approver_department') ? ' is-error' : '' }}" id="{{ $formPrefix }}_approver_department" type="text" name="approver_department" value="{{ old('approver_department') }}" required/>
        @error('approver_department')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
      </div>
      <div class="fg">
        <div class="itr-label">Designation <span class="itr-req">*</span></div>
        <input class="itr-input{{ $errors->has('approver_designation') ? ' is-error' : '' }}" type="text" name="approver_designation" value="{{ old('approver_designation') }}" required/>
        @error('approver_designation')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
      </div>
      <div class="fg">
        <div class="itr-label">Contact <span class="itr-req">*</span></div>
        <input class="itr-input{{ $errors->has('approver_contact') ? ' is-error' : '' }}" id="{{ $formPrefix }}_approver_contact" type="text" name="approver_contact" value="{{ old('approver_contact') }}" required/>
        @error('approver_contact')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
      </div>
    </div>
  </div>
</div>

<div class="itr-action-bar">
  <button type="submit" name="action" value="submit" class="itr-btn-submit"><i class="bi bi-send-fill"></i> Submit Request</button>
  <button type="submit" name="action" value="draft" class="itr-btn-draft"
    onclick="collectChips('{{ $formPrefix }}-items-grid','{{ $formPrefix }}_items');sessionStorage.removeItem('itr_form_state')">
    <i class="bi bi-floppy"></i> Save as Draft
  </button>
</div>
