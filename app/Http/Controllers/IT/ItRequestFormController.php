<?php

namespace App\Http\Controllers\IT;

use App\Models\IT\ItRequestForm;
use App\Models\Staff;
use App\Services\IT\ActivityLogService;
use App\Services\IT\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItRequestFormController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::guard('it')->user();

        if ($user->isAdmin()) {
            $search = trim($request->input('itr_search', ''));
            $type   = trim($request->input('itr_type', ''));
            $status = trim($request->input('itr_status', ''));

            $query = ItRequestForm::with('submittedBy')
                ->where('status', '!=', 'New')
                ->where('is_archived', false)
                ->orderByDesc('created_at');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('subject', 'like', "%$search%")
                      ->orWhere('user_name', 'like', "%$search%")
                      ->orWhere('user_department', 'like', "%$search%")
                      ->orWhereHas('submittedBy', fn($u) => $u->where('name', 'like', "%$search%"));
                });
            }
            if ($type)   $query->where('request_type', $type);
            if ($status) $query->where('status', $status);

            $forms         = $query->paginate(25)->withQueryString();
            $total         = ItRequestForm::where('is_archived', false)->count();
            $countNew              = ItRequestForm::where('status', 'New')->where('is_archived', false)->count();
            $countPendingIT        = ItRequestForm::where('status', 'Pending IT')->where('is_archived', false)->count();
            $countPendingValidation= ItRequestForm::where('status', 'Pending Validation')->where('is_archived', false)->count();
            $countDraft            = ItRequestForm::where('status', 'Draft')->where('is_archived', false)->count();
            $countApproved         = ItRequestForm::where('status', 'Approved')->where('is_archived', false)->count();
            $countRejected         = ItRequestForm::where('status', 'Rejected')->where('is_archived', false)->count();
            $countHw       = ItRequestForm::where('request_type', 'hardware')->where('is_archived', false)->count();
            $countSw       = ItRequestForm::where('request_type', 'software')->where('is_archived', false)->count();
            $countSys      = ItRequestForm::where('request_type', 'system')->where('is_archived', false)->count();
            $countSvc      = ItRequestForm::where('request_type', 'service')->where('is_archived', false)->count();

            $archivedForms = ItRequestForm::with('submittedBy')
                ->where('is_archived', true)
                ->orderByDesc('updated_at')
                ->paginate(5, ['*'], 'archive_page');

            return view('it.it-request-form.index', compact(
                'user', 'forms', 'total', 'countNew', 'countPendingIT', 'countPendingValidation', 'countDraft', 'countApproved', 'countRejected',
                'countHw', 'countSw', 'countSys', 'countSvc',
                'search', 'type', 'status', 'archivedForms'
            ));
        }

        if ($user->isStaff()) {
            $staffForms = ItRequestForm::where('user_email', $user->email)
                ->orderByDesc('created_at')
                ->get();
            return view('it.it-request-form.index', compact('user', 'staffForms'));
        }

        $myForms = ItRequestForm::where('submitted_by', Auth::guard('it')->id())
            ->where('cleared_by_submitter', false)
            ->orderByDesc('created_at')
            ->get();

        $staffList = Staff::where('is_active', 1)->orderBy('name')->get(['id', 'staff_no', 'name', 'position', 'department_id', 'email'])->map(function ($s) {
            return [
                'name'     => $s->name,
                'dept'     => optional($s->department)->name ?? '',
                'staff_no' => $s->staff_no ?? '',
                'email'    => $s->email ?? '',
            ];
        });

        $houList = \App\Models\IT\User::where('it_role', 'hou')->where('is_active', 1)->orderBy('name')->get()->map(function ($u) {
            return [
                'name'  => $u->full_name,
                'dept'  => $u->dept_name ?? optional($u->department)->name ?? '',
                'email' => $u->email ?? '',
            ];
        });

        $pendingApprovals = collect();
        if ($user->it_role === 'hou') {
            $pendingApprovals = ItRequestForm::with('submittedBy')
                ->where('approver_name', $user->full_name)
                ->where('status', 'New')
                ->orderByDesc('created_at')
                ->get();
        }

        $pendingValidations = collect();
        if ($user->isItValidator()) {
            $pendingValidations = ItRequestForm::with('submittedBy')
                ->where('status', 'Pending Validation')
                ->orderByDesc('created_at')
                ->get();
        }

        return view('it.it-request-form.index', ['user' => $user, 'myForms' => $myForms, 'staffList' => $staffList, 'houList' => $houList, 'pendingApprovals' => $pendingApprovals, 'pendingValidations' => $pendingValidations]);
    }

    public function savedDrafts()
    {
        $user = Auth::guard('it')->user();
        if ($user->isAdmin()) {
            return redirect()->route('it.it-request-form')->with('error', 'Admins manage all forms from the inbox.');
        }
        if ($user->isStaff()) {
            return redirect()->route('it.dashboard')->with('error', 'Access denied. IT Request Form is only available to management roles.');
        }
        $drafts = ItRequestForm::where('submitted_by', Auth::guard('it')->id())
            ->where('status', 'Draft')
            ->orderByDesc('updated_at')
            ->paginate(20);
        return view('it.it-request-form.drafts', compact('user', 'drafts'));
    }

    public function destroyDraft(int $id)
    {
        $user = Auth::guard('it')->user();
        if ($user->isStaff()) abort(403);
        $form = ItRequestForm::findOrFail($id);
        if ($user->isAdmin() || $form->submitted_by !== $user->id || $form->status !== 'Draft') {
            abort(403);
        }
        ActivityLogService::log('DELETE', 'it_request_form', $form->id, 'Deleted draft IT request: ' . ($form->subject ?? 'Untitled'));
        $form->delete();
        return redirect()->route('it.it-request-form.drafts')->with('success', 'Draft deleted successfully.');
    }

    public function show(int $id)
    {
        $user = Auth::guard('it')->user();
        if (!$user->isAdmin()) {
            return redirect()->route('it.it-request-form')->with('error', 'Access denied.');
        }
        $form = ItRequestForm::with('submittedBy')->findOrFail($id);
        if ($form->status === 'New') {
            return redirect()->route('it.it-request-form')->with('error', 'This request is still pending HOU approval and is not yet available for review.');
        }
        ActivityLogService::log('VIEW', 'it_request_form', $id, 'Viewed IT request: ' . $form->subject);
        return view('it.it-request-form.show', compact('form', 'user'));
    }

    public function staffShow(int $id)
    {
        $user = Auth::guard('it')->user();
        $form = ItRequestForm::with('submittedBy')->findOrFail($id);
        if ($user->email !== $form->user_email) abort(403);
        return view('it.it-request-form.staff-view', compact('form', 'user'));
    }

    public function store(Request $request)
    {
        $user = Auth::guard('it')->user();
        if ($user->isStaff()) {
            abort(403);
        }

        $isDraft = $request->input('action') === 'draft';
        $type    = $request->input('request_type');

        $rules = [
            'request_type' => 'required|in:hardware,software,system,service',
            'subject'      => 'nullable|string|max:200',
            'document'     => 'nullable|file|max:2048|mimes:pdf,doc,docx,jpg,jpeg,png',
        ];

        if (!$isDraft) {
            $rules += [
                'user_type'         => 'required|string',
                'exit_join_date'    => 'required|date',
                'justification'     => 'required|string',
                'req_name'          => 'required|string',
                'req_department'    => 'required|string',
                'req_staff_id'      => 'required|string',
                'req_designation'   => 'required|string',
                'req_contact'       => 'required|string',
                'approver_name'        => 'required|string',
                'approver_department'  => 'required|string',
                'approver_designation' => 'required|string',
                'approver_contact'     => 'required|string',
            ];

            if ($type === 'hardware') {
                $rules['hw_request_type'] = 'required|string';
                $rules['hw_items']        = 'required|array|min:1';
            } elseif ($type === 'software') {
                $rules['sw_request_type']   = 'required|string';
                $rules['sw_budgeted']       = 'required|string';
                $rules['sw_opex_capex']     = 'required|string';
                $rules['sw_cost_center']    = $request->input('sw_budgeted') === 'yes' ? 'required|string' : 'nullable|string';
                $rules['sw_expected_value'] = 'required|string';
            } elseif ($type === 'system') {
                $rules['sys_request_type'] = 'required|string';
                $rules['sys_items']        = 'required|array|min:1';
            } elseif ($type === 'service') {
                $rules['svc_items'] = 'required|array|min:1';
            }
        }

        $request->validate($rules);

        $docPath = null;
        if ($request->hasFile('document')) {
            $docPath = $request->file('document')->store('it-request-docs', 'public');
        }

        $form = ItRequestForm::create([
            'submitted_by'        => Auth::guard('it')->id(),
            'request_type'        => $type,
            'subject'             => $request->subject,
            'status'              => $isDraft ? 'Draft' : 'New',
            'hw_request_type'     => $request->hw_request_type,
            'hw_items'            => $request->hw_items,
            'hw_pc_laptop_no'     => $request->hw_pc_laptop_no,
            'hw_printer_no'       => $request->hw_printer_no,
            'sw_request_type'     => $request->sw_request_type,
            'sw_software_name'    => $request->sw_software_name,
            'sw_budgeted'         => $request->sw_budgeted,
            'sw_opex_capex'       => $request->sw_opex_capex,
            'sw_cost_center'      => $request->sw_cost_center,
            'sw_expected_value'   => $request->sw_expected_value,
            'sys_request_type'    => $request->sys_request_type,
            'sys_items'           => $request->sys_items,
            'svc_items'           => $request->svc_items,
            'user_type'           => $request->user_type,
            'exit_join_date'      => $request->exit_join_date ?: null,
            'justification'       => $request->justification,
            'document_path'       => $docPath,
            'user_name'           => $request->user_name,
            'user_email'          => $request->user_email,
            'user_address'        => $request->user_address,
            'user_department'     => $request->user_department,
            'user_designation'    => $request->user_designation,
            'user_staff_id'       => $request->user_staff_id,
            'user_contact'        => $request->user_contact,
            'req_name'            => $request->req_name,
            'req_department'      => $request->req_department,
            'req_staff_id'        => $request->req_staff_id,
            'req_designation'     => $request->req_designation,
            'req_contact'         => $request->req_contact,
            'req_company'         => $request->req_company,
            'approver_name'       => $request->approver_name,
            'approver_department' => $request->approver_department,
            'approver_designation'=> $request->approver_designation,
            'approver_contact'    => $request->approver_contact,
            'approver_company'    => $request->approver_company,
        ]);

        ActivityLogService::log(
            'SUBMIT',
            'it_request_form',
            $form->id,
            ($isDraft ? 'Saved draft' : 'Submitted') . ' IT request: ' . $request->subject
        );

        if (!$isDraft) {
            $houUser = \App\Models\IT\User::where('it_role', 'hou')
                ->where('is_active', 1)
                ->where('name', $request->approver_name)
                ->first();
            if ($houUser) {
                NotificationService::notifyUserWithEmail(
                    $houUser->id,
                    'it_request',
                    'New IT Request Awaiting Your Approval: ' . $request->subject,
                    Auth::guard('it')->user()->full_name . ' submitted a new ' . ucfirst($type) . ' IT request and selected you as the approver.',
                    route('it.it-request-form.hou-show', $form->id)
                );
            }

            if ($request->filled('user_email')) {
                $staffUser = \App\Models\IT\User::where('email', $request->user_email)
                    ->where('is_active', 1)
                    ->first();
                if ($staffUser) {
                    NotificationService::notifyUserWithEmail(
                        $staffUser->id,
                        'it_request',
                        'An IT Request Has Been Submitted On Your Behalf: ' . ($request->subject ?? 'IT Request'),
                        $user->full_name . ' has submitted an IT request for you. You can track the progress below.',
                        route('it.it-request-form.staff-show', $form->id)
                    );
                }
            }
        }

        $msg = $isDraft ? 'Draft saved successfully.' : 'IT request submitted successfully.';
        if ($isDraft) {
            return redirect()->route('it.it-request-form.drafts')->with('success', $msg);
        }
        return redirect()->route('it.it-request-form')->with('success', $msg);
    }

    public function edit(int $id)
    {
        $user = Auth::guard('it')->user();
        if ($user->isStaff()) abort(403);
        $form = ItRequestForm::with('submittedBy')->findOrFail($id);

        $isAdmin = $user->isAdmin();
        $isOwnerDraft = $form->submitted_by === $user->id && $form->status === 'Draft';
        if (!$isAdmin && !$isOwnerDraft) abort(403);

        return view('it.it-request-form.edit', compact('form', 'user', 'isAdmin'));
    }

    public function update(Request $request, int $id)
    {
        $user = Auth::guard('it')->user();
        if ($user->isStaff()) abort(403);
        $form = ItRequestForm::findOrFail($id);

        $isAdmin = $user->isAdmin();
        $isOwnerDraft = $form->submitted_by === $user->id && $form->status === 'Draft';
        if (!$isAdmin && !$isOwnerDraft) abort(403);

        $type = $form->request_type;
        $isDraft = !$isAdmin && $request->input('action') === 'draft';
        $req = $isDraft ? 'nullable' : 'required';

        $rules = [
            'subject'           => 'nullable|string|max:200',
            'user_type'         => "$req|string",
            'exit_join_date'    => "$req|date",
            'justification'     => "$req|string",
            'req_name'          => "$req|string",
            'req_department'    => "$req|string",
            'req_staff_id'      => "$req|string",
            'req_designation'   => "$req|string",
            'req_contact'       => "$req|string",
            'approver_name'        => "$req|string",
            'approver_department'  => "$req|string",
            'approver_designation' => "$req|string",
            'approver_contact'     => "$req|string",
            'document'          => 'nullable|file|max:2048|mimes:pdf,doc,docx,jpg,jpeg,png',
        ];

        if ($type === 'hardware') {
            $rules['hw_request_type'] = "$req|string";
            $rules['hw_items']        = $isDraft ? 'nullable|array' : 'required|array|min:1';
            $rules['hw_pc_laptop_no'] = "$req|string";
            $rules['hw_printer_no']   = "$req|string";
        } elseif ($type === 'software') {
            $rules['sw_request_type']   = "$req|string";
            $rules['sw_budgeted']       = "$req|string";
            $rules['sw_opex_capex']     = "$req|string";
            $rules['sw_cost_center']    = ($isDraft || $request->input('sw_budgeted') !== 'yes') ? 'nullable|string' : 'required|string';
            $rules['sw_expected_value'] = "$req|string";
        } elseif ($type === 'system') {
            $rules['sys_request_type'] = "$req|string";
            $rules['sys_items']        = $isDraft ? 'nullable|array' : 'required|array|min:1';
        } elseif ($type === 'service') {
            $rules['svc_items'] = $isDraft ? 'nullable|array' : 'required|array|min:1';
        }

        $request->validate($rules);

        $docPath = $form->document_path;
        if ($request->hasFile('document')) {
            $docPath = $request->file('document')->store('it-request-docs', 'public');
        }

        $updateData = [
            'subject'             => $request->subject,
            'hw_request_type'     => $request->hw_request_type,
            'hw_items'            => $request->hw_items,
            'hw_pc_laptop_no'     => $request->hw_pc_laptop_no,
            'hw_printer_no'       => $request->hw_printer_no,
            'sw_request_type'     => $request->sw_request_type,
            'sw_software_name'    => $request->sw_software_name,
            'sw_budgeted'         => $request->sw_budgeted,
            'sw_opex_capex'       => $request->sw_opex_capex,
            'sw_cost_center'      => $request->sw_cost_center,
            'sw_expected_value'   => $request->sw_expected_value,
            'sys_request_type'    => $request->sys_request_type,
            'sys_items'           => $request->sys_items,
            'svc_items'           => $request->svc_items,
            'user_type'           => $request->user_type,
            'exit_join_date'      => $request->exit_join_date ?: null,
            'justification'       => $request->justification,
            'document_path'       => $docPath,
            'user_name'           => $request->user_name,
            'user_email'          => $request->user_email,
            'user_address'        => $request->user_address,
            'user_department'     => $request->user_department,
            'user_designation'    => $request->user_designation,
            'user_staff_id'       => $request->user_staff_id,
            'user_contact'        => $request->user_contact,
            'req_name'            => $request->req_name,
            'req_department'      => $request->req_department,
            'req_staff_id'        => $request->req_staff_id,
            'req_designation'     => $request->req_designation,
            'req_contact'         => $request->req_contact,
            'req_company'         => $request->req_company,
            'approver_name'       => $request->approver_name,
            'approver_department' => $request->approver_department,
            'approver_designation'=> $request->approver_designation,
            'approver_contact'    => $request->approver_contact,
            'approver_company'    => $request->approver_company,
        ];

        if (!$isAdmin) {
            $newStatus = $isDraft ? 'Draft' : 'New';
            $updateData['status'] = $newStatus;
            $form->update($updateData);

            ActivityLogService::log(
                $isDraft ? 'DRAFT' : 'SUBMIT',
                'it_request_form',
                $form->id,
                ($isDraft ? 'Re-saved draft' : 'Submitted') . ' IT request: ' . $form->subject
            );

            if (!$isDraft) {
                $houUser = \App\Models\IT\User::where('it_role', 'hou')
                    ->where('is_active', 1)
                    ->where('name', $request->approver_name)
                    ->first();
                if ($houUser) {
                    NotificationService::notifyUserWithEmail(
                        $houUser->id,
                        'it_request',
                        'New IT Request Awaiting Your Approval: ' . $form->subject,
                        $user->full_name . ' submitted a new ' . ucfirst($type) . ' IT request and selected you as the approver.',
                        route('it.it-request-form.hou-show', $form->id)
                    );
                }
            }

            $msg = $isDraft ? 'Draft saved successfully.' : 'IT request submitted successfully.';
            if ($isDraft) {
                return redirect()->route('it.it-request-form.drafts')->with('success', $msg);
            }
            return redirect()->route('it.it-request-form')->with('success', $msg);
        }

        $form->update($updateData);
        ActivityLogService::log('UPDATE', 'it_request_form', $form->id, 'Updated IT request: ' . $form->subject);
        return redirect()->route('it.it-request-form.show', $id)->with('success', 'Request updated successfully.');
    }

    public function houShow(int $id)
    {
        $user = Auth::guard('it')->user();
        if ($user->it_role !== 'hou') abort(403);

        $form = ItRequestForm::with('submittedBy')->findOrFail($id);
        if ($form->approver_name !== $user->full_name) abort(403);

        $isHou = true;
        ActivityLogService::log('VIEW', 'it_request_form', $id, 'HOU viewed IT request: ' . $form->subject);
        return view('it.it-request-form.show', compact('form', 'user', 'isHou'));
    }

    public function houApprove(Request $request, int $id)
    {
        $user = Auth::guard('it')->user();
        if ($user->it_role !== 'hou') abort(403);

        $form = ItRequestForm::with('submittedBy')->findOrFail($id);
        if ($form->approver_name !== $user->full_name || $form->status !== 'New') {
            return back()->with('error', 'This request cannot be approved.');
        }

        $form->update([
            'status'          => 'Pending IT',
            'hou_reviewed_by' => $user->id,
            'hou_reviewed_at' => now(),
            'hou_remarks'     => $request->input('approval_remarks'),
        ]);

        NotificationService::notifyAdmins(
            'it_request',
            'IT Request Pending Admin Approval: ' . $form->subject,
            $user->full_name . ' (HOU) has approved the request from ' . ($form->submittedBy?->full_name ?? 'staff') . '. It now requires your approval.',
            route('it.it-request-form.show', $form->id)
        );

        ActivityLogService::log('APPROVE', 'it_request_form', $form->id, 'HOU approved IT request: ' . $form->subject);
        return redirect()->route('it.it-request-form')->with('success', 'Request approved and forwarded to IT Admin.');
    }

    public function houReject(Request $request, int $id)
    {
        $user = Auth::guard('it')->user();
        if ($user->it_role !== 'hou') abort(403);

        $form = ItRequestForm::with('submittedBy')->findOrFail($id);
        if ($form->approver_name !== $user->full_name || $form->status !== 'New') {
            return back()->with('error', 'This request cannot be rejected.');
        }

        $form->update([
            'status'          => 'Rejected',
            'hou_reviewed_by' => $user->id,
            'hou_reviewed_at' => now(),
            'hou_remarks'     => $request->input('approval_remarks'),
        ]);

        if ($form->submitted_by) {
            NotificationService::notifyUserWithEmail(
                $form->submitted_by,
                'it_request',
                'IT Request Rejected',
                'Your IT request "' . $form->subject . '" has been rejected by ' . $user->full_name . ' (HOU).',
                route('it.it-request-form')
            );
        }

        ActivityLogService::log('REJECT', 'it_request_form', $form->id, 'HOU rejected IT request: ' . $form->subject);
        return redirect()->route('it.it-request-form')->with('success', 'Request rejected.');
    }

    public function approve(Request $request, int $id)
    {
        $user = Auth::guard('it')->user();
        if (!$user->isAdmin()) abort(403);

        $form = ItRequestForm::with('submittedBy')->findOrFail($id);
        if ($form->status !== 'Pending IT') {
            return back()->with('error', 'This request cannot be approved at this stage.');
        }

        $form->update([
            'status'           => 'Pending Validation',
            'reviewed_by'      => $user->id,
            'reviewed_at'      => now(),
            'approval_remarks' => $request->input('approval_remarks'),
        ]);

        $validator = \App\Models\IT\User::where('is_it_validator', true)->where('is_active', 1)->first();
        if ($validator) {
            NotificationService::notifyUserWithEmail(
                $validator->id,
                'it_request',
                'IT Request Awaiting Your Validation: ' . $form->subject,
                'An IT request from ' . ($form->submittedBy?->full_name ?? 'staff') . ' has been approved by IT Admin and is now awaiting your validation.',
                route('it.it-request-form.validator-show', $form->id)
            );
        }

        ActivityLogService::log('APPROVE', 'it_request_form', $form->id, 'IT Admin approved IT request: ' . $form->subject);

        return redirect()->route('it.it-request-form.show', $id)->with('success', 'Request approved and forwarded to validator.');
    }

    public function reject(Request $request, int $id)
    {
        $user = Auth::guard('it')->user();
        if (!$user->isAdmin()) abort(403);

        $form = ItRequestForm::with('submittedBy')->findOrFail($id);
        if ($form->status !== 'Pending IT') {
            return back()->with('error', 'This request cannot be rejected at this stage.');
        }

        $form->update([
            'status'           => 'Rejected',
            'reviewed_by'      => $user->id,
            'reviewed_at'      => now(),
            'approval_remarks' => $request->input('approval_remarks'),
        ]);

        if ($form->submitted_by) {
            NotificationService::notifyUserWithEmail(
                $form->submitted_by,
                'it_request',
                'IT Request Rejected',
                'Your IT request "' . $form->subject . '" has been rejected.',
                route('it.it-request-form')
            );
        }

        ActivityLogService::log('REJECT', 'it_request_form', $form->id, 'Rejected IT request: ' . $form->subject);

        return redirect()->route('it.it-request-form.show', $id)->with('success', 'Request rejected.');
    }

    public function requestUpdate(Request $request, int $id)
    {
        $user = Auth::guard('it')->user();
        if (!$user->isAdmin()) abort(403);

        $form = ItRequestForm::with('submittedBy')->findOrFail($id);
        if ($form->status !== 'Pending IT') {
            return back()->with('error', 'This request cannot be updated at this stage.');
        }

        $form->update([
            'status'           => 'Draft',
            'reviewed_by'      => $user->id,
            'reviewed_at'      => now(),
            'approval_remarks' => $request->input('approval_remarks'),
        ]);

        if ($form->submitted_by) {
            NotificationService::notifyUser(
                $form->submitted_by,
                'it_request',
                'IT Request Needs Update',
                'Your IT request "' . $form->subject . '" requires changes before it can be approved. Please review and resubmit.',
                route('it.it-request-form')
            );
        }

        ActivityLogService::log('UPDATE_REQUESTED', 'it_request_form', $form->id, 'Requested update on IT request: ' . $form->subject);

        return redirect()->route('it.it-request-form.show', $id)->with('success', 'Update requested — the user has been notified.');
    }

    public function validatorShow(int $id)
    {
        $user = Auth::guard('it')->user();
        if (!$user->isItValidator()) abort(403);

        $form = ItRequestForm::with('submittedBy')->findOrFail($id);
        $isValidator = true;
        ActivityLogService::log('VIEW', 'it_request_form', $id, 'Validator viewed IT request: ' . $form->subject);
        return view('it.it-request-form.show', compact('form', 'user', 'isValidator'));
    }

    public function validatorApprove(Request $request, int $id)
    {
        $user = Auth::guard('it')->user();
        if (!$user->isItValidator()) abort(403);

        $form = ItRequestForm::with('submittedBy')->findOrFail($id);
        if ($form->status !== 'Pending Validation') {
            return back()->with('error', 'This request cannot be validated at this stage.');
        }

        $form->update([
            'status'            => 'Approved',
            'validated_by'      => $user->id,
            'validated_at'      => now(),
            'validator_remarks' => $request->input('validator_remarks'),
        ]);

        if ($form->submitted_by) {
            NotificationService::notifyUserWithEmail(
                $form->submitted_by,
                'it_request',
                'IT Request Approved',
                'Your IT request “' . $form->subject . '” has been fully approved.',
                route('it.it-request-form')
            );
        }

        ActivityLogService::log('VALIDATE', 'it_request_form', $form->id, 'Validator approved IT request: ' . $form->subject);
        return redirect()->route('it.it-request-form')->with('success', 'Request validated and approved successfully.');
    }

    public function validatorReject(Request $request, int $id)
    {
        $user = Auth::guard('it')->user();
        if (!$user->isItValidator()) abort(403);

        $form = ItRequestForm::with('submittedBy')->findOrFail($id);
        if ($form->status !== 'Pending Validation') {
            return back()->with('error', 'This request cannot be rejected at this stage.');
        }

        $form->update([
            'status'            => 'Rejected',
            'validated_by'      => $user->id,
            'validated_at'      => now(),
            'validator_remarks' => $request->input('validator_remarks'),
        ]);

        if ($form->submitted_by) {
            NotificationService::notifyUserWithEmail(
                $form->submitted_by,
                'it_request',
                'IT Request Rejected',
                'Your IT request “' . $form->subject . '” has been rejected during final validation.',
                route('it.it-request-form')
            );
        }

        ActivityLogService::log('REJECT', 'it_request_form', $form->id, 'Validator rejected IT request: ' . $form->subject);
        return redirect()->route('it.it-request-form')->with('success', 'Request rejected.');
    }

    public function bulkHouApprove(Request $request)
    {
        $user = Auth::guard('it')->user();
        if ($user->it_role !== 'hou') abort(403);

        $ids = array_filter((array) $request->input('ids', []), 'is_numeric');
        $remarks = $request->input('remarks');
        $count = 0;
        foreach ($ids as $id) {
            $form = ItRequestForm::find($id);
            if (!$form || $form->status !== 'New') continue;
            $form->update(['status' => 'Pending IT', 'hou_reviewed_by' => $user->id, 'hou_reviewed_at' => now(), 'hou_remarks' => $remarks]);
            NotificationService::notifyAdmins('it_request', 'IT Request Pending Admin Approval: ' . $form->subject, $user->full_name . ' approved a request from ' . ($form->submittedBy?->full_name ?? 'staff') . '.', route('it.it-request-form.show', $form->id));
            ActivityLogService::log('APPROVE', 'it_request_form', $form->id, 'HOU bulk approved IT request: ' . $form->subject);
            $count++;
        }
        return redirect()->route('it.it-request-form')->with('success', $count . ' request(s) approved successfully.');
    }

    public function bulkHouReject(Request $request)
    {
        $user = Auth::guard('it')->user();
        if ($user->it_role !== 'hou') abort(403);

        $ids = array_filter((array) $request->input('ids', []), 'is_numeric');
        $remarks = $request->input('remarks');
        $count = 0;
        foreach ($ids as $id) {
            $form = ItRequestForm::find($id);
            if (!$form || $form->status !== 'New') continue;
            $form->update(['status' => 'Rejected', 'hou_reviewed_by' => $user->id, 'hou_reviewed_at' => now(), 'hou_remarks' => $remarks]);
            if ($form->submitted_by) {
                NotificationService::notifyUserWithEmail($form->submitted_by, 'it_request', 'IT Request Rejected', 'Your IT request "' . $form->subject . '" was rejected by the HOU.', route('it.it-request-form'));
            }
            ActivityLogService::log('REJECT', 'it_request_form', $form->id, 'HOU bulk rejected IT request: ' . $form->subject);
            $count++;
        }
        return redirect()->route('it.it-request-form')->with('success', $count . ' request(s) rejected.');
    }

    public function bulkAdminApprove(Request $request)
    {
        $user = Auth::guard('it')->user();
        if (!$user->isAdmin()) abort(403);

        $ids = array_filter((array) $request->input('ids', []), 'is_numeric');
        $remarks = $request->input('remarks');
        $count = 0;
        $validator = \App\Models\IT\User::where('is_it_validator', true)->where('is_active', 1)->first();
        foreach ($ids as $id) {
            $form = ItRequestForm::find($id);
            if (!$form || $form->status !== 'Pending IT') continue;
            $form->update(['status' => 'Pending Validation', 'reviewed_by' => $user->id, 'reviewed_at' => now(), 'approval_remarks' => $remarks]);
            if ($validator) {
                NotificationService::notifyUserWithEmail($validator->id, 'it_request', 'IT Request Awaiting Your Validation: ' . $form->subject, 'An IT request has been approved by IT Admin and is now awaiting your validation.', route('it.it-request-form.validator-show', $form->id));
            }
            ActivityLogService::log('APPROVE', 'it_request_form', $form->id, 'IT Admin bulk approved IT request: ' . $form->subject);
            $count++;
        }
        return redirect()->route('it.it-request-form')->with('success', $count . ' request(s) approved and forwarded to validator.');
    }

    public function bulkAdminReject(Request $request)
    {
        $user = Auth::guard('it')->user();
        if (!$user->isAdmin()) abort(403);

        $ids = array_filter((array) $request->input('ids', []), 'is_numeric');
        $remarks = $request->input('remarks');
        $count = 0;
        foreach ($ids as $id) {
            $form = ItRequestForm::find($id);
            if (!$form || $form->status !== 'Pending IT') continue;
            $form->update(['status' => 'Rejected', 'reviewed_by' => $user->id, 'reviewed_at' => now(), 'approval_remarks' => $remarks]);
            if ($form->submitted_by) {
                NotificationService::notifyUserWithEmail($form->submitted_by, 'it_request', 'IT Request Rejected', 'Your IT request "' . $form->subject . '" has been rejected by IT Admin.', route('it.it-request-form'));
            }
            ActivityLogService::log('REJECT', 'it_request_form', $form->id, 'IT Admin bulk rejected IT request: ' . $form->subject);
            $count++;
        }
        return redirect()->route('it.it-request-form')->with('success', $count . ' request(s) rejected.');
    }

    public function bulkAdminArchive(Request $request)
    {
        $user = Auth::guard('it')->user();
        if (!$user->isAdmin()) abort(403);

        $ids = array_filter((array) $request->input('ids', []), 'is_numeric');
        $count = 0;
        foreach ($ids as $id) {
            $form = ItRequestForm::find($id);
            if (!$form || !in_array($form->status, ['Approved', 'Rejected'])) continue;
            $form->update(['is_archived' => true]);
            ActivityLogService::log('ARCHIVE', 'it_request_form', $form->id, 'Bulk archived IT request: ' . $form->subject);
            $count++;
        }
        return redirect()->route('it.it-request-form')->with('success', $count . ' request(s) archived.');
    }

    public function bulkValidatorApprove(Request $request)
    {
        $user = Auth::guard('it')->user();
        if (!$user->isItValidator()) abort(403);

        $ids = array_filter((array) $request->input('ids', []), 'is_numeric');
        $remarks = $request->input('remarks');
        $count = 0;
        foreach ($ids as $id) {
            $form = ItRequestForm::find($id);
            if (!$form || $form->status !== 'Pending Validation') continue;
            $form->update(['status' => 'Approved', 'validated_by' => $user->id, 'validated_at' => now(), 'validator_remarks' => $remarks]);
            if ($form->submitted_by) {
                NotificationService::notifyUserWithEmail($form->submitted_by, 'it_request', 'IT Request Approved', 'Your IT request "' . $form->subject . '" has been fully approved.', route('it.it-request-form'));
            }
            ActivityLogService::log('VALIDATE', 'it_request_form', $form->id, 'Validator bulk approved IT request: ' . $form->subject);
            $count++;
        }
        return redirect()->route('it.it-request-form')->with('success', $count . ' request(s) validated and approved.');
    }

    public function bulkValidatorReject(Request $request)
    {
        $user = Auth::guard('it')->user();
        if (!$user->isItValidator()) abort(403);

        $ids = array_filter((array) $request->input('ids', []), 'is_numeric');
        $remarks = $request->input('remarks');
        $count = 0;
        foreach ($ids as $id) {
            $form = ItRequestForm::find($id);
            if (!$form || $form->status !== 'Pending Validation') continue;
            $form->update(['status' => 'Rejected', 'validated_by' => $user->id, 'validated_at' => now(), 'validator_remarks' => $remarks]);
            if ($form->submitted_by) {
                NotificationService::notifyUserWithEmail($form->submitted_by, 'it_request', 'IT Request Rejected', 'Your IT request "' . $form->subject . '" has been rejected during final validation.', route('it.it-request-form'));
            }
            ActivityLogService::log('REJECT', 'it_request_form', $form->id, 'Validator bulk rejected IT request: ' . $form->subject);
            $count++;
        }
        return redirect()->route('it.it-request-form')->with('success', $count . ' request(s) rejected.');
    }

    public function archiveRequest(int $id)
    {
        $user = Auth::guard('it')->user();
        if (!$user->isAdmin()) abort(403);

        $form = ItRequestForm::findOrFail($id);
        if (!in_array($form->status, ['Approved', 'Rejected'])) {
            return back()->with('error', 'Only decided requests (Approved or Rejected) can be archived.');
        }

        $form->update(['is_archived' => true]);
        ActivityLogService::log('ARCHIVE', 'it_request_form', $form->id, 'Archived IT request: ' . $form->subject);
        return back()->with('success', 'Request archived.');
    }

    public function unarchiveRequest(int $id)
    {
        $user = Auth::guard('it')->user();
        if (!$user->isAdmin()) abort(403);

        $form = ItRequestForm::findOrFail($id);
        $form->update(['is_archived' => false]);
        ActivityLogService::log('UNARCHIVE', 'it_request_form', $form->id, 'Unarchived IT request: ' . $form->subject);
        return back()->with('success', 'Request moved back to inbox.');
    }

    public function clearAllDecided()
    {
        $user = Auth::guard('it')->user();

        ItRequestForm::where('submitted_by', $user->id)
            ->whereIn('status', ['Approved', 'Rejected'])
            ->update(['cleared_by_submitter' => true]);

        return back()->with('success', 'All decided requests have been cleared from your list.');
    }
}

