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
                'This policy is established to ensure that walkie talkies at FGV Johor Bulkers Sdn Bhd are used in an orderly, controlled manner and for official purposes only.',
                'Walkie talkies are company property and must be used for official purposes only.',
                'All requests must be submitted through the assigned supervisor using the provided system.',
                'Users are responsible for keeping the walkie talkie in good, clean, and safe condition.',
                'The equipment must not be loaned to another party without supervisor approval.',
                'Any loss, damage, or issue must be reported immediately through the provided system.',
                'The company only covers improvement or repair costs caused by normal wear and tear or manufacturing defects.',
                'If damage or loss is caused by negligence, misuse, or failure to follow usage instructions, the user is fully responsible for the repair or replacement cost.',
            ],
            'bm' => [
                'Polisi ini diwujudkan untuk memastikan penggunaan walkie talkie di FGV Johor Bulkers Sdn Bhd adalah teratur, terkawal dan digunakan bagi tujuan rasmi sahaja.',
                'Walkie talkie adalah hak milik syarikat dan hanya untuk kegunaan rasmi sahaja.',
                'Semua permohonan hendaklah dibuat melalui penyelia masing-masing menggunakan sistem yang disediakan.',
                'Pengguna bertanggungjawab memastikan walkie talkie sentiasa berada dalam keadaan baik, bersih dan selamat.',
                'Peralatan tidak boleh dipinjamkan kepada pihak lain tanpa kelulusan penyelia.',
                'Sebarang kehilangan, kerosakan atau masalah hendaklah dilaporkan segera melalui sistem yang disediakan.',
                'Syarikat hanya menanggung kos penambahbaikan bagi kerosakan akibat penggunaan biasa (wear and tear) atau kecacatan pembuatan.',
                'Sekiranya kerosakan atau kehilangan berpunca daripada kecuaian, penyalahgunaan atau kegagalan mematuhi arahan penggunaan, pengguna bertanggungjawab menanggung sepenuhnya kos pembaikan atau penggantian peralatan.',
            ],
        ];
    }
}
