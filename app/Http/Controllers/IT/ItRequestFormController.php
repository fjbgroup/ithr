<?php

namespace App\Http\Controllers\IT;

use App\Models\IT\ItRequestForm;
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

            $query = ItRequestForm::with('submittedBy')->orderByDesc('created_at');

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
            $total         = ItRequestForm::count();
            $countNew      = ItRequestForm::where('status', 'New')->count();
            $countDraft    = ItRequestForm::where('status', 'Draft')->count();
            $countApproved = ItRequestForm::where('status', 'Approved')->count();
            $countRejected = ItRequestForm::where('status', 'Rejected')->count();
            $countHw       = ItRequestForm::where('request_type', 'hardware')->count();
            $countSw       = ItRequestForm::where('request_type', 'software')->count();
            $countSys      = ItRequestForm::where('request_type', 'system')->count();
            $countSvc      = ItRequestForm::where('request_type', 'service')->count();

            return view('it.it-request-form.index', compact(
                'user', 'forms', 'total', 'countNew', 'countDraft', 'countApproved', 'countRejected',
                'countHw', 'countSw', 'countSys', 'countSvc',
                'search', 'type', 'status'
            ));
        }

        $myForms = ItRequestForm::where('submitted_by', Auth::guard('it')->id())
            ->orderByDesc('created_at')
            ->get();

        return view('it.it-request-form.index', ['user' => $user, 'myForms' => $myForms]);
    }

    public function savedDrafts()
    {
        $user = Auth::guard('it')->user();
        if ($user->isAdmin()) {
            return redirect()->route('it.it-request-form')->with('error', 'Admins manage all forms from the inbox.');
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
        ActivityLogService::log('VIEW', 'it_request_form', $id, 'Viewed IT request: ' . $form->subject);
        return view('it.it-request-form.show', compact('form', 'user'));
    }

    public function store(Request $request)
    {
        $isDraft = $request->input('action') === 'draft';
        $type    = $request->input('request_type');

        $rules = [
            'request_type' => 'required|in:hardware,software,system,service',
            'subject'      => $isDraft ? 'nullable|string|max:200' : 'required|string|max:200',
            'document'     => 'nullable|file|max:2048|mimes:pdf,doc,docx,jpg,jpeg,png',
        ];

        if (!$isDraft) {
            $rules += [
                'user_type'         => 'required|string',
                'exit_join_date'    => 'required|date',
                'justification'     => 'required|string',
                'user_name'         => 'required|string',
                'user_email'        => 'required|email',
                'user_address'      => 'required|string',
                'user_department'   => 'required|string',
                'user_designation'  => 'required|string',
                'user_staff_id'     => 'required|string',
                'user_contact'      => 'required|string',
                'req_name'          => 'required|string',
                'req_department'    => 'required|string',
                'req_staff_id'      => 'required|string',
                'req_designation'   => 'required|string',
                'req_contact'       => 'required|string',
                'req_company'       => 'required|string',
                'approver_name'        => 'required|string',
                'approver_department'  => 'required|string',
                'approver_designation' => 'required|string',
                'approver_contact'     => 'required|string',
                'approver_company'     => 'required|string',
            ];

            if ($type === 'hardware') {
                $rules['hw_request_type'] = 'required|string';
                $rules['hw_items']        = 'required|array|min:1';
                $rules['hw_pc_laptop_no'] = 'required|string';
                $rules['hw_printer_no']   = 'required|string';
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
            NotificationService::notifyAdmins(
                'it_request',
                'New IT Request: ' . $request->subject,
                Auth::guard('it')->user()->full_name . ' (' . Auth::guard('it')->user()->roleName() . ') submitted a new ' . ucfirst($type) . ' request.',
                route('it.it-request-form.show', $form->id)
            );
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
        $form = ItRequestForm::with('submittedBy')->findOrFail($id);

        $isAdmin = $user->isAdmin();
        $isOwnerDraft = $form->submitted_by === $user->id && $form->status === 'Draft';
        if (!$isAdmin && !$isOwnerDraft) abort(403);

        return view('it.it-request-form.edit', compact('form', 'user', 'isAdmin'));
    }

    public function update(Request $request, int $id)
    {
        $user = Auth::guard('it')->user();
        $form = ItRequestForm::findOrFail($id);

        $isAdmin = $user->isAdmin();
        $isOwnerDraft = $form->submitted_by === $user->id && $form->status === 'Draft';
        if (!$isAdmin && !$isOwnerDraft) abort(403);

        $type = $form->request_type;
        $isDraft = !$isAdmin && $request->input('action') === 'draft';
        $req = $isDraft ? 'nullable' : 'required';

        $rules = [
            'subject'           => "$req|string|max:200",
            'user_type'         => "$req|string",
            'exit_join_date'    => "$req|date",
            'justification'     => "$req|string",
            'user_name'         => "$req|string",
            'user_email'        => $isDraft ? 'nullable|email' : 'required|email',
            'user_address'      => "$req|string",
            'user_department'   => "$req|string",
            'user_designation'  => "$req|string",
            'user_staff_id'     => "$req|string",
            'user_contact'      => "$req|string",
            'req_name'          => "$req|string",
            'req_department'    => "$req|string",
            'req_staff_id'      => "$req|string",
            'req_designation'   => "$req|string",
            'req_contact'       => "$req|string",
            'req_company'       => "$req|string",
            'approver_name'        => "$req|string",
            'approver_department'  => "$req|string",
            'approver_designation' => "$req|string",
            'approver_contact'     => "$req|string",
            'approver_company'     => "$req|string",
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
                NotificationService::notifyAdmins(
                    'it_request',
                    'New IT Request: ' . $form->subject,
                    $user->full_name . ' (' . $user->roleName() . ') submitted a new ' . ucfirst($type) . ' request.',
                    route('it.it-request-form.show', $form->id)
                );
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

    public function approve(Request $request, int $id)
    {
        $user = Auth::guard('it')->user();
        if (!$user->isAdmin()) abort(403);

        $form = ItRequestForm::with('submittedBy')->findOrFail($id);
        if ($form->status !== 'New') {
            return back()->with('error', 'This request has already been reviewed.');
        }

        $form->update([
            'status'           => 'Approved',
            'reviewed_by'      => $user->id,
            'reviewed_at'      => now(),
            'approval_remarks' => $request->input('approval_remarks'),
        ]);

        if ($form->submitted_by) {
            NotificationService::notifyUser(
                $form->submitted_by,
                'it_request',
                'IT Request Approved',
                'Your IT request "' . $form->subject . '" has been approved.',
                route('it.it-request-form')
            );
        }

        ActivityLogService::log('APPROVE', 'it_request_form', $form->id, 'Approved IT request: ' . $form->subject);

        return redirect()->route('it.it-request-form.show', $id)->with('success', 'Request approved successfully.');
    }

    public function reject(Request $request, int $id)
    {
        $user = Auth::guard('it')->user();
        if (!$user->isAdmin()) abort(403);

        $form = ItRequestForm::with('submittedBy')->findOrFail($id);
        if ($form->status !== 'New') {
            return back()->with('error', 'This request has already been reviewed.');
        }

        $form->update([
            'status'           => 'Rejected',
            'reviewed_by'      => $user->id,
            'reviewed_at'      => now(),
            'approval_remarks' => $request->input('approval_remarks'),
        ]);

        if ($form->submitted_by) {
            NotificationService::notifyUser(
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
        if ($form->status !== 'New') {
            return back()->with('error', 'This request has already been reviewed.');
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

        return redirect()->route('it.it-request-form.show', $id)->with('success', 'Update requested â€” the user has been notified.');
    }
}

