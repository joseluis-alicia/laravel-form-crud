<?php

namespace App\Http\Controllers\Psi\Operasional;

use Illuminate\View\View;
use App\Models\Opd\InfoOpd;
use Illuminate\Support\Str;
use App\Models\Opd\Pimpinan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\Psi\Operasional\OperasionalRutin;
use App\Models\Psi\Operasional\Rutin\PsiRutinKasus;
use App\Models\Psi\Operasional\Rutin\PsiRutinQanun;
use App\Models\Psi\Operasional\Rutin\PsiRutinLokasi;
use App\Models\Psi\Operasional\Rutin\PsiRutinKegiatan;
use App\Models\Psi\Operasional\Rutin\PsiRutinKecamatan;


class RutinController extends Controller
{
    /**
     * Compress image to target size while maintaining quality
     */
    private function compressImage($file, $targetSizeKB = 75)
    {
        $filename = 'psi_rutin_' . time() . '_' . Str::random(10) . '.jpg';
        $path = storage_path('app/public/psi_rutin_images/' . $filename);

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $manager = new ImageManager(new Driver());
        $image = $manager->read($file);

        // Dapatkan ukuran asli
        $originalWidth = $image->width();
        $originalHeight = $image->height();

        // Simpan sementara untuk ukuran referensi
        $tempPath = tempnam(sys_get_temp_dir(), 'img_');
        $image->toJpeg(100)->save($tempPath);
        $originalSizeKB = filesize($tempPath) / 1024;

        // Jika sudah di bawah target, langsung simpan
        if ($originalSizeKB <= $targetSizeKB) {
            $image->toJpeg(85)->save($path);
            unlink($tempPath);
            return 'psi_rutin_images/' . $filename;
        }

        // Tentukan strategi berdasarkan ukuran asli
        if ($originalSizeKB > 500) {
            // Untuk file sangat besar (>500KB), perlu resize + kompresi agresif
            $result = $this->compressLargeImage($image, $targetSizeKB, $path, $originalSizeKB);
        } elseif ($originalSizeKB > 200) {
            // Untuk file sedang (200-500KB), kompresi dengan resize moderat
            $result = $this->compressMediumImage($image, $targetSizeKB, $path, $originalSizeKB);
        } else {
            // Untuk file kecil (<200KB), hanya kompresi kualitas
            $result = $this->compressSmallImage($image, $targetSizeKB, $path, $originalSizeKB);
        }

        unlink($tempPath);
        return 'psi_rutin_images/' . $filename;
    }

    private function compressLargeImage($image, $targetSizeKB, $path, $originalSizeKB)
    {
        $originalWidth = $image->width();
        $originalHeight = $image->height();

        // Hitung rasio resize yang dibutuhkan
        $sizeRatio = $targetSizeKB / $originalSizeKB;
        $dimensionRatio = sqrt($sizeRatio) * 0.8; // Faktor konservatif

        // Resize gambar
        $newWidth = max(800, (int)($originalWidth * $dimensionRatio));
        $newHeight = max(600, (int)($originalHeight * $dimensionRatio));

        $image->resize($newWidth, $newHeight);

        // Cari kualitas optimal dengan binary search
        return $this->findOptimalQuality($image, $targetSizeKB, $path, 40, 75);
    }

    private function compressMediumImage($image, $targetSizeKB, $path, $originalSizeKB)
    {
        $originalWidth = $image->width();
        $originalHeight = $image->height();

        // Resize moderat jika dimensi besar
        if ($originalWidth > 1200 || $originalHeight > 1200) {
            $image->scaleDown(1200, 1200);
        }

        // Cari kualitas optimal
        return $this->findOptimalQuality($image, $targetSizeKB, $path, 50, 80);
    }

    private function compressSmallImage($image, $targetSizeKB, $path, $originalSizeKB)
    {
        // Hanya kompresi kualitas tanpa resize
        return $this->findOptimalQuality($image, $targetSizeKB, $path, 60, 90);
    }

    private function findOptimalQuality($image, $targetSizeKB, $path, $minQuality = 40, $maxQuality = 85)
    {
        $low = $minQuality;
        $high = $maxQuality;
        $bestQuality = $maxQuality;
        $bestSizeKB = PHP_INT_MAX;

        $maxIterations = 8;
        $iteration = 0;

        while ($iteration < $maxIterations && $low <= $high) {
            $mid = (int)(($low + $high) / 2);

            // Simpan dengan kualitas saat ini
            $tempPath = tempnam(sys_get_temp_dir(), 'test_');
            $image->toJpeg($mid)->save($tempPath);
            $currentSizeKB = filesize($tempPath) / 1024;

            if ($currentSizeKB <= $targetSizeKB) {
                // Ukuran acceptable, coba kualitas lebih tinggi
                $bestQuality = $mid;
                $bestSizeKB = $currentSizeKB;
                $low = $mid + 1;
            } else {
                // Ukuran terlalu besar, coba kualitas lebih rendah
                $high = $mid - 1;
            }

            unlink($tempPath);
            $iteration++;

            // Jika sudah sangat dekat, break early
            if (abs($currentSizeKB - $targetSizeKB) < 5) {
                break;
            }
        }

        // Simpan dengan kualitas terbaik yang ditemukan
        $image->toJpeg($bestQuality, true)->save($path); // progressive JPEG

        // Jika masih terlalu besar, gunakan kompresi lebih agresif
        $finalSizeKB = filesize($path) / 1024;
        if ($finalSizeKB > $targetSizeKB * 1.2) {
            $this->applyAggressiveCompression($image, $targetSizeKB, $path);
        }

        return $bestQuality;
    }

    private function applyAggressiveCompression($image, $targetSizeKB, $path)
    {
        // Strategi fallback untuk gambar yang sangat sulit dikompresi
        $tempPath = tempnam(sys_get_temp_dir(), 'agg_');

        // Coba berbagai tingkat kompresi
        $qualities = [45, 40, 35, 30];

        foreach ($qualities as $quality) {
            $image->toJpeg($quality, true)->save($tempPath);
            $sizeKB = filesize($tempPath) / 1024;

            if ($sizeKB <= $targetSizeKB) {
                copy($tempPath, $path);
                unlink($tempPath);
                return true;
            }
        }

        // Jika masih belum mencapai target, gunakan yang terkecil
        copy($tempPath, $path);
        unlink($tempPath);
        return false;
    }

    public function index(Request $request)
    {
        $keyword = $request->input('search');
        $tanggalMulai = $request->input('start_date');
        $tanggalSelesai = $request->input('end_date');

        $query = OperasionalRutin::with(['lokasi', 'kecamatan', 'kegiatan', 'qanun', 'kasus'])
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('uraian', 'like', "%{$keyword}%");
                })
                    ->orWhereHas('kegiatan', fn($q) => $q->where('nama_kegiatan', 'like', "%{$keyword}%"))
                ;
            })
            ->when($tanggalMulai && $tanggalSelesai, function ($query) use ($tanggalMulai, $tanggalSelesai) {
                $query->whereBetween('hari', [$tanggalMulai, $tanggalSelesai]);
            })
            ->latest();

        $totalData = $query->count();

        $psirutins = $query->paginate(10)->appends($request->query());

        return view('psi.operasional.rutin.index', compact('psirutins', 'keyword', 'totalData'));
    }

    public function cetak(Request $request)
    {
        $query = OperasionalRutin::query();

        $tanggalMulai = $request->input('start_date');
        $tanggalSelesai = $request->input('end_date');

        // Filter berdasarkan tanggal (start_date dan end_date)
        if ($tanggalMulai) {
            $query->whereDate('hari', '>=', $tanggalMulai);
        }

        if ($tanggalSelesai) {
            $query->whereDate('hari', '<=', $tanggalSelesai);
        }

        // Pencarian berdasarkan kata kunci
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('uraian', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('kendala', 'LIKE', "%{$searchTerm}%");
            })
                ->orWhereHas('kegiatan', function ($q) use ($searchTerm) {
                    $q->where('nama_kegiatan', 'LIKE', "%{$searchTerm}%");
                });
        }

        // Ambil data pimpinan dari database
        $pimpinan = [
            'kepala_bidang' => Pimpinan::where('bidang', 'Penegakan Syariat Islam')->first(),
            'kepala_seksi' => Pimpinan::where('bidang', 'Pengawasan dan Pembinaan')->first(),
            'kepala_satuan' => Pimpinan::where('bidang', 'Satuan Polisi Pamong Praja dan WIlayatul Hisbah')->first(),
        ];

        $psirutins = $query->orderBy('hari', 'asc')->get();

        // Kirim tanggal mulai dan selesai ke view
        return view('psi.operasional.rutin.cetak', compact('psirutins', 'tanggalMulai', 'tanggalSelesai', 'pimpinan'));
    }

    public function create()
    {
        $lokasiList = PsiRutinLokasi::all();
        $kecamatanList = PsiRutinKecamatan::all();
        $kegiatanList = PsiRutinKegiatan::all();
        $qanunList = PsiRutinQanun::all();
        $kasusList = PsiRutinKasus::all();

        return view('psi.operasional.rutin.create', compact('lokasiList', 'kecamatanList', 'kegiatanList', 'qanunList', 'kasusList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'hari' => 'required|date',
                'waktu' => 'required|date_format:H:i',
                'uraian' => 'required',
                'lokasi_id' => 'required|exists:psi_rutin_lokasi,id',
                'kecamatan_id' => 'required|exists:psi_rutin_kecamatan,id',
                'kegiatan_id' => 'required|exists:psi_rutin_kegiatan,id',
                'qanun_id' => 'required|exists:psi_rutin_qanun,id',
                'kasus_id' => 'required|exists:psi_rutin_kasus,id',
                'kendala' => 'nullable',
                'image1' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2024',
                'image2' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2024',
                'image3' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2024',
                'image4' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2024',
            ],
            [
                'hari.required' => '* Harus diisi',
                'hari.date' => '* Format tanggal tidak valid',
                'waktu.required' => '* Harus diisi',
                'waktu.date_format' => '* Format waktu harus 00:00',
                'uraian.required' => '* Harus diisi',
                'lokasi_id.required' => '* Harus diisi',
                'kecamatan_id.required' => '* Harus diisi',
                'kegiatan_id.required' => '* Harus diisi',
                'qanun_id.required' => '* Harus diisi',
                'kasus_id.required' => '* Harus diisi',
                'image1.image' => '* File harus berupa gambar',
                'image2.image' => '* File harus berupa gambar',
                'image3.image' => '* File harus berupa gambar',
                'image4.image' => '* File harus berupa gambar',
                'image1.max' => '* Gambar maksimal 2 MB',
                'image2.max' => '* Gambar maksimal 2 MB',
                'image3.max' => '* Gambar maksimal 2 MB',
                'image4.max' => '* Gambar maksimal 2 MB',
            ]
        );

        // Handle upload dan kompresi untuk setiap gambar
        for ($i = 1; $i <= 4; $i++) {
            $fieldName = 'image' . $i;
            if ($request->hasFile($fieldName)) {
                try {
                    $validated[$fieldName] = $this->compressImage($request->file($fieldName));
                } catch (\Exception $e) {
                    return back()->withErrors([$fieldName => 'Gagal memproses gambar: ' . $e->getMessage()]);
                }
            }
        }

        OperasionalRutin::create($validated);

        Alert::success('Berhasil', 'Data Berhasil di Tambah');

        return redirect()->route('index.rutin');
    }

    public function edit($hashid): View
    {
        $psirutin = OperasionalRutin::findByHashid($hashid);

        if (!$psirutin) {
            abort(404, 'Data tidak ditemukan');
        }

        $lokasiList = PsiRutinLokasi::all();
        $kecamatanList = PsiRutinKecamatan::all();
        $kegiatanList = PsiRutinKegiatan::all();
        $qanunList = PsiRutinQanun::all();
        $kasusList = PsiRutinKasus::all();

        return view('psi.operasional.rutin.edit', compact('psirutin', 'lokasiList', 'kecamatanList', 'kegiatanList', 'qanunList', 'kasusList'));
    }

    public function update(Request $request, $hashid)
    {
        $psirutin = OperasionalRutin::findByHashid($hashid);

        if (!$psirutin) {
            abort(404, 'Data tidak ditemukan');
        }

        $validated = $request->validate(
            [
                'hari' => 'required|date',
                'waktu' => 'required|date_format:H:i',
                'uraian' => 'required',
                'lokasi_id' => 'required|exists:psi_rutin_lokasi,id',
                'kecamatan_id' => 'required|exists:psi_rutin_kecamatan,id',
                'kegiatan_id' => 'required|exists:psi_rutin_kegiatan,id',
                'qanun_id' => 'required|exists:psi_rutin_qanun,id',
                'kasus_id' => 'required|exists:psi_rutin_kasus,id',
                'kendala' => 'nullable',
                'image1' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2024',
                'image2' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2024',
                'image3' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2024',
                'image4' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2024',
            ],
            [
                'hari.required' => '* Harus diisi',
                'hari.date' => '* Format tanggal tidak valid',
                'waktu.required' => '* Harus diisi',
                'waktu.date_format' => '* Format waktu harus 00:00',
                'uraian.required' => '* Harus diisi',
                'lokasi_id.required' => '* Harus diisi',
                'kecamatan_id.required' => '* Harus diisi',
                'kegiatan_id.required' => '* Harus diisi',
                'qanun_id.required' => '* Harus diisi',
                'kasus_id.required' => '* Harus diisi',
                'image1.image' => '* File harus berupa gambar',
                'image2.image' => '* File harus berupa gambar',
                'image3.image' => '* File harus berupa gambar',
                'image4.image' => '* File harus berupa gambar',
                'image1.max' => '* Gambar maksimal 2 MB',
                'image2.max' => '* Gambar maksimal 2 MB',
                'image3.max' => '* Gambar maksimal 2 MB',
                'image4.max' => '* Gambar maksimal 2 MB',
            ]
        );

        // Handle gambar untuk setiap field
        for ($i = 1; $i <= 4; $i++) {
            $fieldName = 'image' . $i;
            $deleteField = 'hapus_gambar' . $i;

            // Jika checkbox hapus gambar dicentang
            if ($request->filled($deleteField)) {
                if (!empty($psirutin->$fieldName) && Storage::disk('public')->exists($psirutin->$fieldName)) {
                    Storage::disk('public')->delete($psirutin->$fieldName);
                }
                $validated[$fieldName] = null;
            }
            // Jika ada file gambar baru diupload
            elseif ($request->hasFile($fieldName)) {
                try {
                    // Hapus gambar lama jika ada
                    if (!empty($psirutin->$fieldName) && Storage::disk('public')->exists($psirutin->$fieldName)) {
                        Storage::disk('public')->delete($psirutin->$fieldName);
                    }

                    // Kompres dan simpan gambar baru
                    $validated[$fieldName] = $this->compressImage($request->file($fieldName));
                } catch (\Exception $e) {
                    return back()->withErrors([$fieldName => 'Gagal memproses gambar: ' . $e->getMessage()]);
                }
            }
            // Jika tidak ada perubahan pada gambar, pertahankan nilai lama
            else {
                $validated[$fieldName] = $psirutin->$fieldName;
            }
        }

        // Update data
        $psirutin->update($validated);

        Alert::success('Berhasil', 'Data Berhasil di Perbaharui');

        return redirect()->route('index.rutin');
    }

    public function show($hashid)
    {
        $psirutin = OperasionalRutin::findByHashid($hashid);

        if (!$psirutin) {
            abort(404, 'Data tidak ditemukan');
        }

        $opdPenanggungJawab = InfoOpd::where('nama_opd', 'Satuan Polisi Pamong Praja dan Wilayatul Hisbah')->first();

        return view('psi.operasional.rutin.show', [
            'psirutin' => $psirutin,
            'opd' => $opdPenanggungJawab
        ]);
    }

    // Method destroy yang diperbaiki - menggunakan hashid
    public function destroy($hashid)
    {
        $psirutin = OperasionalRutin::findByHashid($hashid);

        if (!$psirutin) {
            abort(404, 'Data tidak ditemukan');
        }

        // Hapus gambar jika ada
        for ($i = 1; $i <= 4; $i++) {
            $fieldName = 'image' . $i;
            if (!empty($psirutin->$fieldName) && Storage::disk('public')->exists($psirutin->$fieldName)) {
                Storage::disk('public')->delete($psirutin->$fieldName);
            }
        }

        $psirutin->delete();

        Alert::success('Berhasil', 'Data Berhasil di Hapus');

        return redirect()->route('index.rutin');
    }


    public function getKasusByQanun($qanunId)
    {
        $kasusList = PsiRutinKasus::where('qanun_id', $qanunId)->get();
        return response()->json($kasusList);
    }
}
