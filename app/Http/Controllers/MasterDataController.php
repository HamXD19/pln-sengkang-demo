<?php

namespace App\Http\Controllers;

use App\Models\OrganizationHierarchy;
use App\Models\OrganizationUnit;
use App\Models\Position;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class MasterDataController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $isSuperAdmin = $user && in_array(strtoupper(optional($user->role)->name), ['SUPER_ADMIN', 'SUPER ADMIN']);
        $defaultTab = $isSuperAdmin ? 'users' : 'hierarchy';
        $activeTab = $request->get('tab', $defaultTab);

        $query = User::with(['role', 'position', 'organizationUnit'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_no', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        if ($request->filled('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }

        if ($request->filled('tl_id')) {
            $tlId = $request->tl_id;
            $tlUser = User::find($tlId);
            $query->where(function ($q) use ($tlId, $tlUser) {
                $q->where('tl_id', $tlId);
                if ($tlUser && $tlUser->unit_id) {
                    $q->orWhere(function ($sq) use ($tlUser) {
                        $sq->whereNull('tl_id')->where('unit_id', $tlUser->unit_id);
                    });
                }
            });
        }

        if ($request->filled('asman_id')) {
            $asmanId = $request->asman_id;
            $asmanUser = User::find($asmanId);
            $query->where(function ($q) use ($asmanId, $asmanUser) {
                $q->where('asman_id', $asmanId);
                if ($asmanUser && $asmanUser->unit_id) {
                    $childUnitIds = OrganizationUnit::where('parent_unit_id', $asmanUser->unit_id)->orWhere('id', $asmanUser->unit_id)->pluck('id')->toArray();
                    $q->orWhere(function ($sq) use ($childUnitIds) {
                        $sq->whereNull('asman_id')->whereIn('unit_id', $childUnitIds);
                    });
                }
            });
        }

        // Pastikan link paginasi selalu menyertakan tab=users agar saat ganti halaman tidak reset ke tab awal
        $queryParams = $request->query();
        $queryParams['tab'] = 'users';
        $users = $query->paginate(15)->appends($queryParams);
        $users->getCollection()->each->append('hierarchy_info');

        $roles = Role::all();
        $positions = Position::orderBy('name')->get();
        $units = OrganizationUnit::all();
        $hierarchies = OrganizationHierarchy::orderBy('sort_order')->get();

        // Kumpulan data user ringkas untuk pemetaan foto di Struktur Organisasi (Organogram) & Relasi Incumbent
        $organogramUsers = User::select('id', 'name', 'employee_no', 'avatar', 'role_id', 'position_id')
            ->whereNotNull('employee_no')
            ->get()
            ->makeHidden(['avatar_url'])
            ->keyBy(fn($u) => trim($u->employee_no));

        $usersByName = User::select('id', 'name', 'employee_no', 'avatar', 'role_id', 'position_id')
            ->get()
            ->makeHidden(['avatar_url'])
            ->keyBy(fn($u) => strtoupper(trim($u->name)));

        // Daftar seluruh Team Leader dan Asisten Manajer untuk dropdown penugasan hierarki
        $tls = User::select('id', 'name', 'employee_no', 'position_id', 'unit_id', 'role_id')
            ->with(['position:id,name', 'organizationUnit:id,name'])
            ->whereHas('role', fn($q) => $q->where('name', 'TL'))
            ->orderBy('name')
            ->get();

        $asmans = User::select('id', 'name', 'employee_no', 'position_id', 'unit_id', 'role_id')
            ->with(['position:id,name', 'organizationUnit:id,name'])
            ->whereHas('role', fn($q) => $q->where('name', 'ASMAN'))
            ->orderBy('name')
            ->get();

        $logoPath = public_path('assets/bumn/logo-pln.png');
        $bgPath = public_path('assets/bumn/bg-sengkang-hd.jpg');
        $branding = [
            'logo_url' => file_exists($logoPath) ? '/assets/bumn/logo-pln.png?v=' . filemtime($logoPath) : '/assets/bumn/logo-pln.png',
            'login_bg_url' => file_exists($bgPath) ? '/assets/bumn/bg-sengkang-hd.jpg?v=' . filemtime($bgPath) : '/assets/bumn/bg-sengkang-hd.jpg',
            'has_default_logo' => file_exists(public_path('assets/bumn/logo-pln.default.png')),
            'has_default_bg' => file_exists(public_path('assets/bumn/bg-sengkang-hd.default.jpg')),
        ];

        return Inertia::render('Master/Index', [
            'users' => $users,
            'roles' => $roles,
            'positions' => $positions,
            'units' => $units,
            'hierarchies' => $hierarchies,
            'tls' => $tls,
            'asmans' => $asmans,
            'organogramUsers' => $organogramUsers,
            'usersByName' => $usersByName,
            'activeTab' => $activeTab,
            'branding' => $branding,
        ]);
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'employee_no' => 'nullable|string|max:50|unique:users,employee_no',
            'password' => 'nullable|string|min:1',
            'role_id' => 'required|exists:roles,id',
            'position_id' => 'nullable|exists:positions,id',
            'unit_id' => 'nullable|exists:organization_units,id',
            'tl_id' => 'nullable|exists:users,id',
            'asman_id' => 'nullable|exists:users,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            try {
                $targetDir = public_path('storage/avatars');
                if (!is_dir($targetDir)) {
                    @mkdir($targetDir, 0755, true);
                }
                @copy(storage_path('app/public/' . $avatarPath), public_path('storage/' . $avatarPath));
            } catch (\Throwable $e) {}
        }

        $defaultPassword = !empty($validated['password']) ? $validated['password'] : '123';

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'employee_no' => $validated['employee_no'] ?? null,
            'password' => Hash::make($defaultPassword),
            'role_id' => $validated['role_id'],
            'position_id' => $validated['position_id'] ?? null,
            'unit_id' => $validated['unit_id'] ?? null,
            'tl_id' => $validated['tl_id'] ?? null,
            'asman_id' => $validated['asman_id'] ?? null,
            'avatar' => $avatarPath,
            'is_active' => true,
        ]);

        if ($request->filled('redirect_to')) {
            return redirect($request->redirect_to)->with('success', 'Pegawai / Pengguna baru berhasil ditambahkan.');
        }

        return redirect()->route('master.index', ['tab' => 'users'])->with('success', 'Pegawai / Pengguna baru berhasil ditambahkan.');
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'employee_no' => 'nullable|string|max:50|unique:users,employee_no,' . $user->id,
            'role_id' => 'required|exists:roles,id',
            'position_id' => 'nullable|exists:positions,id',
            'unit_id' => 'nullable|exists:organization_units,id',
            'tl_id' => 'nullable|exists:users,id',
            'asman_id' => 'nullable|exists:users,id',
            'is_active' => 'required|boolean',
            'password' => 'nullable|string|min:1',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $dataToUpdate = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'employee_no' => $validated['employee_no'] ?? null,
            'role_id' => $validated['role_id'],
            'position_id' => $validated['position_id'] ?? null,
            'unit_id' => $validated['unit_id'] ?? null,
            'tl_id' => $validated['tl_id'] ?? null,
            'asman_id' => $validated['asman_id'] ?? null,
            'is_active' => $validated['is_active'],
        ];

        if (!empty($validated['password'])) {
            $dataToUpdate['password'] = Hash::make($validated['password']);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
                }
                if (file_exists(public_path('storage/' . $user->avatar))) {
                    @unlink(public_path('storage/' . $user->avatar));
                }
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $dataToUpdate['avatar'] = $avatarPath;
            try {
                $targetDir = public_path('storage/avatars');
                if (!is_dir($targetDir)) {
                    @mkdir($targetDir, 0755, true);
                }
                @copy(storage_path('app/public/' . $avatarPath), public_path('storage/' . $avatarPath));
            } catch (\Throwable $e) {}
        }

        $user->update($dataToUpdate);

        if ($request->filled('redirect_to')) {
            return redirect($request->redirect_to)->with('success', "Data pegawai {$user->name} berhasil diperbarui.");
        }

        return redirect()->route('master.index', ['tab' => 'users'])->with('success', "Data pegawai {$user->name} berhasil diperbarui.");
    }

    public function destroyUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // Cek agar tidak menghapus akun sendiri
        if (auth()->id() == $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $userName = $user->name;

        // Hapus file fisik avatar pengguna dari disk hosting
        if ($user->avatar) {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }
            if (file_exists(public_path('storage/' . $user->avatar))) {
                @unlink(public_path('storage/' . $user->avatar));
            }
        }

        $user->delete();

        if ($request->filled('redirect_to')) {
            return redirect($request->redirect_to)->with('success', "Pengguna {$userName} berhasil dihapus dari sistem.");
        }

        return redirect()->route('master.index', ['tab' => 'users'])->with('success', "Pengguna {$userName} berhasil dihapus dari sistem.");
    }

    public function resetUserPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->password = Hash::make('123');
        $user->save();

        if ($request->filled('redirect_to')) {
            return redirect($request->redirect_to)->with('success', "Password pegawai {$user->name} berhasil di-reset ke default (123).");
        }

        return back()->with('success', "Password pegawai {$user->name} berhasil di-reset ke default (123).");
    }

    public function storeHierarchy(Request $request)
    {
        $validated = $request->validate([
            'incumbent' => 'required|string|max:50',
            'parent' => 'required|string|max:50',
            'incumbent_title' => 'nullable|string|max:255',
            'parent_title' => 'nullable|string|max:255',
            'person_name' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
        ]);

        OrganizationHierarchy::create($validated);

        if ($request->filled('redirect_to')) {
            return redirect($request->redirect_to)->with('success', "Relasi Incumbent {$validated['incumbent']} -> {$validated['parent']} berhasil ditambahkan.");
        }

        return redirect()->route('master.index', ['tab' => 'hierarchy'])->with('success', "Relasi Incumbent {$validated['incumbent']} -> {$validated['parent']} berhasil ditambahkan.");
    }

    public function updateHierarchy(Request $request, $id)
    {
        $hierarchy = OrganizationHierarchy::findOrFail($id);

        $validated = $request->validate([
            'incumbent' => 'required|string|max:50',
            'parent' => 'required|string|max:50',
            'incumbent_title' => 'nullable|string|max:255',
            'parent_title' => 'nullable|string|max:255',
            'person_name' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
        ]);

        $hierarchy->update($validated);

        if ($request->filled('redirect_to')) {
            return redirect($request->redirect_to)->with('success', "Relasi Incumbent {$hierarchy->incumbent} berhasil diperbarui.");
        }

        return redirect()->route('master.index', ['tab' => 'hierarchy'])->with('success', "Relasi Incumbent {$hierarchy->incumbent} berhasil diperbarui.");
    }

    public function destroyHierarchy(Request $request, $id)
    {
        $hierarchy = OrganizationHierarchy::findOrFail($id);
        $inc = $hierarchy->incumbent;
        $hierarchy->delete();

        if ($request->filled('redirect_to')) {
            return redirect($request->redirect_to)->with('success', "Relasi Incumbent {$inc} berhasil dihapus.");
        }

        return redirect()->route('master.index', ['tab' => 'hierarchy'])->with('success', "Relasi Incumbent {$inc} berhasil dihapus.");
    }

    public function daftarAkun(Request $request)
    {
        $query = User::with(['role', 'position', 'organizationUnit.parent'])
            ->where('is_active', true)
            ->orderByRaw('CASE WHEN role_id = 5 THEN 0 WHEN role_id = 1 THEN 1 WHEN role_id = 2 THEN 2 WHEN role_id = 3 THEN 3 ELSE 4 END')
            ->orderBy('unit_id')
            ->orderBy('name');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('employee_no', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('position', fn($pq) => $pq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('organizationUnit', fn($uq) => $uq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('role_id') && $request->role_id !== 'all') {
            $query->where('role_id', $request->role_id);
        }

        if ($request->filled('unit_id') && $request->unit_id !== 'all') {
            $query->where('unit_id', $request->unit_id);
        }

        $users = $query->get()->map(function ($u) {
            $parent = $u->organizationUnit?->parent;
            return [
                'id' => $u->id,
                'nid' => $u->employee_no,
                'name' => $u->name,
                'email' => $u->email,
                'role' => $u->role?->name ?? 'STAFF',
                'position' => $u->position?->name ?? '-',
                'unit' => $u->organizationUnit?->name ?? 'UP Sengkang',
                'bidang' => $parent ? $parent->name : ($u->organizationUnit?->name ?? 'UP Sengkang'),
                'password_default' => '123',
            ];
        });

        $roles = Role::all(['id', 'name']);
        $units = OrganizationUnit::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Accounts/Index', [
            'users' => $users,
            'roles' => $roles,
            'units' => $units,
            'filters' => $request->only(['search', 'role_id', 'unit_id']),
            'totalCount' => User::where('is_active', true)->count(),
        ]);
    }

    public function updateLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:png,jpg,jpeg,webp,svg|max:5120',
        ]);

        $file = $request->file('logo');
        $destDir = public_path('assets/bumn');
        if (!is_dir($destDir)) {
            @mkdir($destDir, 0755, true);
        }

        // Pastikan backup default ada sebelum ditimpa
        $defaultLogo = $destDir . '/logo-pln.default.png';
        if (!file_exists($defaultLogo) && file_exists($destDir . '/logo-pln.png')) {
            @copy($destDir . '/logo-pln.png', $defaultLogo);
        }

        $file->move($destDir, 'logo-pln.png');

        return back()->with('success', 'Logo resmi aplikasi berhasil diperbarui.');
    }

    public function updateLoginBackground(Request $request)
    {
        $request->validate([
            'background' => 'required|image|mimes:jpg,jpeg,png,webp|max:15360',
        ]);

        $file = $request->file('background');
        $destDir = public_path('assets/bumn');
        if (!is_dir($destDir)) {
            @mkdir($destDir, 0755, true);
        }

        // Pastikan backup default ada sebelum ditimpa
        $defaultBg = $destDir . '/bg-sengkang-hd.default.jpg';
        if (!file_exists($defaultBg) && file_exists($destDir . '/bg-sengkang-hd.jpg')) {
            @copy($destDir . '/bg-sengkang-hd.jpg', $defaultBg);
        }

        $file->move($destDir, 'bg-sengkang-hd.jpg');
        @copy($destDir . '/bg-sengkang-hd.jpg', $destDir . '/bg-sengkang.png');

        return back()->with('success', 'Gambar latar (background) halaman login berhasil diperbarui.');
    }

    public function resetLogo(Request $request)
    {
        $dest = public_path('assets/bumn/logo-pln.png');
        $default = public_path('assets/bumn/logo-pln.default.png');
        if (file_exists($default)) {
            @copy($default, $dest);
            return back()->with('success', 'Logo aplikasi berhasil dikembalikan ke default resmi PLN.');
        }

        return back()->with('error', 'File logo default tidak ditemukan.');
    }

    public function resetLoginBackground(Request $request)
    {
        $destHd = public_path('assets/bumn/bg-sengkang-hd.jpg');
        $destPng = public_path('assets/bumn/bg-sengkang.png');
        $default = public_path('assets/bumn/bg-sengkang-hd.default.jpg');
        if (file_exists($default)) {
            @copy($default, $destHd);
            @copy($default, $destPng);
            return back()->with('success', 'Gambar latar login berhasil dikembalikan ke default pembangkit UP Sengkang.');
        }

        return back()->with('error', 'File background default tidak ditemukan.');
    }
}
