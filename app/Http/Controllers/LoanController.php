<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Loan;
use App\Models\Division;
use App\Models\ItemDivisionStock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;


class LoanController extends Controller
{
    // === PUBLIC FORM (TANPA LOGIN) ===

    public function publicCreate()
    {
        // hanya tampilkan barang yang stoknya > 0
        $items = Item::where('can_be_loaned', true)
            ->orderBy('nama_barang')
            ->get();
        $divisions = Division::orderBy('nama')->get();

        return view('loans.public_create', compact('items', 'divisions'));
    }

    private function fetchEmployeeByNik(string $nik): array
    {
        $baseUrl = config('services.employee_api.base_url');
        $url = rtrim($baseUrl, '/') . '/employees/';

        $response = Http::timeout(5)->get($url, [
            'search' => $nik,
        ]);

        if (!$response->ok()) {
            throw ValidationException::withMessages([
                'employee_id' => 'Gagal menghubungi layanan data karyawan. Silakan coba lagi.',
            ]);
        }

        $json = $response->json();

        $data = $json['data'] ?? [];
        if (!is_array($data) || count($data) === 0) {
            throw ValidationException::withMessages([
                'employee_id' => 'NIK tidak ditemukan.',
            ]);
        }

        $employee = $data[0];

        $status = strtolower((string)($employee['status_employee'] ?? ''));
        if ($status !== 'active') {
            throw ValidationException::withMessages([
                'employee_id' => 'Karyawan tidak aktif, peminjaman tidak dapat diproses.',
            ]);
        }

        $name = trim((string)($employee['name'] ?? ''));
        $department = trim((string)($employee['department'] ?? ''));

        if ($name === '' || $department === '') {
            throw ValidationException::withMessages([
                'employee_id' => 'Data karyawan tidak lengkap (nama/departemen kosong).',
            ]);
        }

        return [
            'employee_id' => $nik,
            'name' => $name,
            'department' => $department,
        ];
    }

    public function getEmployeeByNik(string $nik)
    {
        // basic sanitize
        $nik = trim($nik);

        if ($nik === '' || strlen($nik) > 30) {
            return response()->json([
                'found' => false,
                'message' => 'NIK tidak valid.',
            ], 422);
        }

        try {
            $emp = $this->fetchEmployeeByNik($nik);

            return response()->json([
                'found' => true,
                'employee_id' => $emp['employee_id'],
                'name' => $emp['name'],
                'department' => $emp['department'],
            ]);
        } catch (ValidationException $e) {
            $msg = $e->errors()['employee_id'][0] ?? 'Data karyawan tidak valid.';

            return response()->json([
                'found' => false,
                'message' => $msg,
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'found' => false,
                'message' => 'Terjadi kesalahan saat mengambil data karyawan.',
            ], 500);
        }
    }

    public function publicStore(Request $request)
    {
        $validated = $request->validate([
            'item_id'                 => 'required|exists:items,id',
            'division_id'             => 'required|exists:divisions,id',
            'employee_id'             => 'required|string|max:30',
            'jumlah'                  => 'required|integer|min:1',
            'tanggal_pinjam'          => 'required|date',
            'tanggal_rencana_kembali' => 'nullable|date|after_or_equal:tanggal_pinjam',
            'keterangan'              => 'nullable|string|max:255',
        ]);


        $item = Item::where('id', $validated['item_id'])
            ->where('can_be_loaned', true)
            ->first();

        if (!$item->exists) {
            abort(400, 'Barang tidak valid.');
        }

        if (!$item) {
            return back()
                ->withErrors(['item_id' => 'Barang ini tidak tersedia untuk peminjaman.'])
                ->withInput();
        }

        $stock = ItemDivisionStock::where('item_id', $item->id)
            ->where('division_id', $validated['division_id'])
            ->first();

        if (!$stock) {
            return back()
                ->withErrors(['division_id' => 'Divisi ini tidak memiliki stok untuk barang tersebut.'])
                ->withInput();
        }

        if ($stock->stok_terkini < $validated['jumlah']) {
            return back()
                ->withErrors(['jumlah' => 'Stok divisi tidak mencukupi. Stok tersedia: ' . $stock->stok_terkini])
                ->withInput();
        }

        $totalStock = $item->divisionStocks->sum('stok_terkini');

        if ($totalStock <= 0) {
            return back()
                ->withErrors(['item_id' => 'Barang ini tidak tersedia di divisi manapun.'])
                ->withInput();
        }

        DB::transaction(function () use ($validated, $item) {
            $stock = ItemDivisionStock::lockForUpdate()
                ->where('item_id', $item->id)
                ->where('division_id', $validated['division_id'])
                ->first();

            if (!$stock || $stock->stok_terkini < $validated['jumlah']) {
                abort(400, 'Stok pada divisi ini tidak mencukupi untuk peminjaman.');
            }

            // kurangi stok
            $stock->decrement('stok_terkini', $validated['jumlah']);

            // generate kode peminjaman sederhana
            $kode = 'LOAN-' . now()->format('YmdHis');

            $emp = $this->fetchEmployeeByNik($validated['employee_id']);

            // user_id bisa null (public form)
            $userId = Auth::check() ? Auth::id() : null;

            // simpan peminjaman
            $loan = Loan::create([
                'kode_loan'               => $kode,
                'item_id'                 => $item->id,
                'division_id'             => $validated['division_id'],
                'user_id'                 => $userId,
                'employee_id'             => $emp['employee_id'],
                'peminjam'                => $emp['name'],
                'departemen'              => $emp['department'],
                'jumlah'                  => $validated['jumlah'],
                'tanggal_pinjam'          => $validated['tanggal_pinjam'],
                'tanggal_rencana_kembali' => $validated['tanggal_rencana_kembali'] ?? null,
                'status'                  => 'dipinjam',
                'keterangan'              => $validated['keterangan'] ?? null,
            ]);

            // catat pergerakan stok
            StockMovement::create([
                'item_id'    => $item->id,
                'division_id' => $validated['division_id'],
                'jenis'      => 'keluar',
                'jumlah'     => $validated['jumlah'],
                'tanggal'    => $validated['tanggal_pinjam'],
                'user_id'    => $userId, // boleh null
                'keterangan' => 'Peminjaman ' . $loan->kode_loan . ' oleh '
                    . $loan->peminjam . ' (' . $loan->departemen . ')',
            ]);
        });

        return redirect()
            ->route('public.loans.create')
            ->with('success', 'Permintaan peminjaman Anda telah dicatat. Silakan ambil barang di bagian ATK.');
    }

    public function getDivisionsByItem(Item $item)
    {
        // ambil stok per divisi yang stoknya > 0
        $divStocks = $item->divisionStocks()
            ->with('division')
            ->where('stok_terkini', '>', 0)
            ->get();

        // bentuk response JSON
        $result = $divStocks->map(function ($stock) {
            return [
                'id'   => $stock->division->id,
                'nama' => $stock->division->nama,
                'kode' => $stock->division->kode,
                'stok' => $stock->stok_terkini,
            ];
        });

        return response()->json($result);
    }


    // === BAGIAN INTERNAL (login) seperti index(), returnLoan() bisa kita tambah nanti ===

    // daftar peminjaman
    public function index(Request $request)
    {
        $query = Loan::with('item');

        // filter status (opsional)
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $loans = $query
            ->orderBy('status')                // dipinjam dulu, lalu dikembalikan
            ->orderBy('tanggal_pinjam', 'desc')
            ->paginate(20);

        return view('loans.index', compact('loans', 'status'));
    }

    // detail peminjaman (opsional tapi enak punya)
    public function show(Loan $loan)
    {
        $loan->load('item.divisionStocks.division', 'user', 'division');

        return view('loans.show', compact('loan'));
    }

    // tandai sudah dikembalikan
    public function returnLoan(Loan $loan)
    {
        if ($loan->status !== 'dipinjam') {
            return redirect()
                ->route('loans.show', $loan)
                ->with('error', 'Peminjaman ini sudah dikembalikan atau tidak dalam status dipinjam.');
        }

        DB::transaction(function () use ($loan) {
            $stock = ItemDivisionStock::lockForUpdate()
                ->firstOrCreate(
                    [
                        'item_id'     => $loan->item_id,
                        'division_id' => $loan->division_id,
                    ],
                    [
                        'stok_terkini' => 0,
                    ]
                );

            // tambah stok kembali
            $stock->increment('stok_terkini', $loan->jumlah);

            // user internal yang memproses pengembalian (kalau ada)
            $userId = Auth::check() ? Auth::id() : null;

            // catat barang masuk di stock_movements
            StockMovement::create([
                'item_id'    => $loan->item_id,
                'division_id' => $loan->division_id,
                'jenis'      => 'masuk',
                'jumlah'     => $loan->jumlah,
                'tanggal'    => now()->toDateString(),
                'user_id'    => $userId,
                'keterangan' => 'Pengembalian ' . $loan->kode_loan . ' oleh '
                    . $loan->peminjam . ' (' . $loan->departemen . ')',
            ]);

            // update status loan
            $loan->update([
                'status'          => 'dikembalikan',
                'tanggal_kembali' => now()->toDateString(),
            ]);
        });

        return redirect()
            ->route('loans.show', $loan)
            ->with('success', 'Peminjaman telah ditandai sudah dikembalikan dan stok divisi telah diperbarui.');
    }
}
