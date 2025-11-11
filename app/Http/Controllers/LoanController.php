<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Loan;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class LoanController extends Controller
{
    // === PUBLIC FORM (TANPA LOGIN) ===

    public function publicCreate()
    {
        // hanya tampilkan barang yang stoknya > 0
        $items = Item::orderBy('nama_barang')->get();

        return view('loans.public_create', compact('items'));
    }

    public function publicStore(Request $request)
    {
        $validated = $request->validate([
            'item_id'                 => 'required|exists:items,id',
            'peminjam'                => 'required|string|max:100',
            'departemen'              => 'required|string|max:100',
            'jumlah'                  => 'required|integer|min:1',
            'tanggal_pinjam'          => 'required|date',
            'tanggal_rencana_kembali' => 'nullable|date|after_or_equal:tanggal_pinjam',
            'keterangan'              => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($validated) {
            // kunci item selama proses
            $item = Item::lockForUpdate()->findOrFail($validated['item_id']);

            if ($item->stok_terkini < $validated['jumlah']) {
                abort(400, 'Stok tidak mencukupi untuk dipinjam.');
            }

            // kurangi stok
            $item->decrement('stok_terkini', $validated['jumlah']);

            // generate kode peminjaman sederhana
            $kode = 'LOAN-' . now()->format('YmdHis');

            // user_id bisa null (public form)
            $userId = Auth::check() ? Auth::id() : null;

            // simpan peminjaman
            $loan = Loan::create([
                'kode_loan'               => $kode,
                'item_id'                 => $item->id,
                'user_id'                 => $userId,
                'peminjam'                => $validated['peminjam'],
                'departemen'              => $validated['departemen'],
                'jumlah'                  => $validated['jumlah'],
                'tanggal_pinjam'          => $validated['tanggal_pinjam'],
                'tanggal_rencana_kembali' => $validated['tanggal_rencana_kembali'] ?? null,
                'status'                  => 'dipinjam',
                'keterangan'              => $validated['keterangan'] ?? null,
            ]);

            // catat pergerakan stok
            StockMovement::create([
                'item_id'    => $item->id,
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
        $loan->load('item', 'user');

        return view('loans.show', compact('loan'));
    }

    // tandai sudah dikembalikan
    public function returnLoan(Loan $loan)
    {
        if ($loan->status === 'dikembalikan') {
            return redirect()->route('loans.index')
                ->with('success', 'Peminjaman sudah dikembalikan sebelumnya.');
        }

        DB::transaction(function () use ($loan) {
            $item = Item::lockForUpdate()->findOrFail($loan->item_id);

            // tambahkan stok
            $item->increment('stok_terkini', $loan->jumlah);

            // update status loan
            $loan->update([
                'status'          => 'dikembalikan',
                'tanggal_kembali' => now()->toDateString(),
            ]);

            // user internal yang memproses pengembalian (kalau ada)
            $userId = Auth::check() ? Auth::id() : null;

            // catat barang masuk di stock_movements
            StockMovement::create([
                'item_id'    => $item->id,
                'jenis'      => 'masuk',
                'jumlah'     => $loan->jumlah,
                'tanggal'    => now()->toDateString(),
                'user_id'    => $userId,
                'keterangan' => 'Pengembalian ' . $loan->kode_loan . ' oleh '
                    . $loan->peminjam . ' (' . $loan->departemen . ')',
            ]);
        });

        return redirect()->route('loans.index')
            ->with('success', 'Peminjaman berhasil ditandai sudah dikembalikan.');
    }
}
