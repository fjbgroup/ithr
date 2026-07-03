<?php

namespace App\Http\Controllers\WT;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserDashboardController extends Controller
{
    public function index()
    {
        return view('wt.user.dashboard');
    }

    public function profile()
    {
        return view('wt.user.profile');
    }

    public function updateProfile(Request $request)
    {
        $user = auth('wt')->user();
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'phone_no' => 'nullable|string|max:50',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $validated['full_name'] = Str::upper(trim($validated['full_name']));
        $validated['department'] = Str::upper(trim($validated['department']));
        $validated['position'] = Str::upper(trim($validated['position']));
        $validated['phone_no'] = trim((string) ($validated['phone_no'] ?? '')) ?: null;

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updateSignature(Request $request)
    {
        $user = auth('wt')->user();

        if ($request->hasFile('signature_file')) {
            $request->validate(['signature_file' => 'required|image|max:2048']);

            if ($user->signature_img && Storage::disk('public')->exists($user->signature_img)) {
                Storage::disk('public')->delete($user->signature_img);
            }

            $user->update([
                'signature_img' => $request->file('signature_file')->store('signatures', 'public'),
            ]);

            return back()->with('success', 'Signature saved successfully.');
        }

        if ($request->filled('sig_canvas_data')) {
            $raw = (string) $request->input('sig_canvas_data');

            if (! preg_match('/^data:image\/(png|jpeg|gif|webp);base64,/', $raw, $matches)) {
                return back()->with('error', 'Invalid signature format.');
            }

            $imageData = base64_decode(substr($raw, strpos($raw, ',') + 1));
            if ($imageData === false || strlen($imageData) > 2 * 1024 * 1024) {
                return back()->with('error', 'Invalid or oversized signature data.');
            }

            $extension = $matches[1] === 'jpeg' ? 'jpg' : $matches[1];
            $filename = 'sig_' . $user->id . '_' . time() . '.' . $extension;

            Storage::disk('public')->makeDirectory('signatures');

            if ($user->signature_img && Storage::disk('public')->exists($user->signature_img)) {
                Storage::disk('public')->delete($user->signature_img);
            }

            Storage::disk('public')->put('signatures/' . $filename, $imageData);
            $user->update(['signature_img' => 'signatures/' . $filename]);

            return back()->with('success', 'Signature saved successfully.');
        }

        return back()->with('error', 'No signature provided.');
    }

    public function clearSignature()
    {
        $user = auth('wt')->user();

        if ($user->signature_img && Storage::disk('public')->exists($user->signature_img)) {
            Storage::disk('public')->delete($user->signature_img);
        }

        $user->update(['signature_img' => null]);

        return back()->with('success', 'Signature cleared.');
    }

    public function serveSignature()
    {
        $user = auth('wt')->user();

        if (! $user->signature_img || ! Storage::disk('public')->exists($user->signature_img)) {
            abort(404);
        }

        return Storage::disk('public')->response($user->signature_img);
    }

    public function policies()
    {
        return view('wt.user.policies', [
            'policyContent' => $this->loadPolicyContent(),
        ]);
    }

    public function manual()
    {
        return view('wt.user.manual');
    }

    public function updatePolicies(Request $request)
    {
        abort_unless(auth('wt')->user()?->wt_role === 'admin_it', 403);

        $validated = $request->validate([
            'policy_en' => 'required|string|max:5000',
            'policy_bm' => 'required|string|max:5000',
        ]);

        $policy = [
            'en' => $this->normalisePolicyText($validated['policy_en']),
            'bm' => $this->normalisePolicyText($validated['policy_bm']),
        ];

        File::ensureDirectoryExists(dirname($this->policyStoragePath()));
        File::put($this->policyStoragePath(), json_encode($policy, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return back()->with('success', 'Walkie talkie policy updated successfully.');
    }

    private function loadPolicyContent(): array
    {
        $default = $this->defaultPolicyContent();
        $path = $this->policyStoragePath();

        if (! File::exists($path)) {
            return $default;
        }

        $stored = json_decode((string) File::get($path), true);

        return [
            'en' => is_array($stored['en'] ?? null) ? array_values(array_filter($stored['en'])) : $default['en'],
            'bm' => is_array($stored['bm'] ?? null) ? array_values(array_filter($stored['bm'])) : $default['bm'],
        ];
    }

    private function normalisePolicyText(string $value): array
    {
        return collect(preg_split('/\R+/', trim($value)) ?: [])
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }

    private function policyStoragePath(): string
    {
        return storage_path('app/wt/policies.json');
    }

    private function defaultPolicyContent(): array
    {
        return [
            'en' => [
                'The following are the terms and conditions that must be adhered to:-',
                'a. Officers must be responsible to ensure that each walkie-talkie and additional equipment (accessories) provided are used carefully and maintained as well as possible to prevent any damage.',
                'b. If it is found that damage or loss occurs due to :-<br><span style="padding-left:18px; display:inline-block;">&bull; Willful negligence</span><br><span style="padding-left:18px; display:inline-block;">&bull; Misuse of the walkie-talkie</span><br><span style="padding-left:18px; display:inline-block;">&bull; Intentional loss</span><br><span style="padding-left:18px; display:inline-block;">&bull; Intentional damage</span>',
                'c. The staff member concerned will be held responsible to bear the repair and replacement cost of a new walkie-talkie if necessary.',
                'd. However, repair costs for damage caused by "manufacturing defect" and usage exceeding the lifespan will be borne by the company.',
                'Thank you, please be informed.'
            ],
            'bm' => [
                'Berikut adalah syarat-syarat yang perlu dipatuhi:-',
                'a. Petugas perlu bertanggungjawab untuk memastikan setiap walkie-talkie dan kelengkapan tambahan (aksesori) yang dibekalkan digunakan dengan cermat dan dijaga sebaik mungkin bagi mengelakkan berlakunya sebarang kerosakan.',
                'b. Jika didapati berlaku kerosakan atau kehilangan yang disebabkan :-<br><span style="padding-left:18px; display:inline-block;">&bull; Kecuaian yang disengajakan</span><br><span style="padding-left:18px; display:inline-block;">&bull; Penyalahgunaan walkie Talkie</span><br><span style="padding-left:18px; display:inline-block;">&bull; Kehilangan yang disengajakan</span><br><span style="padding-left:18px; display:inline-block;">&bull; Kerosakan yang disengajakan</span>',
                'c. Petugas yang berkenaan akan dipertanggungjawabkan untuk menanggung kos baik pulih dan penggantian walkie-talkie yang baru sekiranya perlu.',
                'd. Bagaimanapun, kos baik pulih terhadap kerosakan yang disebabkan oleh "manufacturing defeat" dan penggunaan yang melebihi jangka hayat akan ditanggung oleh pihak syarikat.',
                'Sekian, harap maklum.'
            ]
        ];
    }
}
