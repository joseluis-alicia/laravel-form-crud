@extends('layouts.psi.operasional.main')
@section('content')
    

    <div class="row mt-1">
        <div class="card ribbon-box">
            <div class="card-header">
                <div class="card-title text-uppercase fw-bold text-center my-1">
                    laporan rutin pengawasan dan penegakan syariat islam
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <a href="{{ route('create.rutin') }}"
                                class="btn btn-primary btn-sm text-uppercase fw-bold">Tambah</a>
                        </div>
                    </div>
                </div>
                <div class="ribbon-two ribbon-two-success"><span class="text-black fw-bold">P S I</span></div>

                {{-- Filter Form (gabung tanggal dan pencarian) --}}
                <form action="{{ route('index.rutin') }}" method="GET" class="mb-2">
                    <div class="row align-items-end">
                        <div class="col-md-2">
                            <label class="form-label">Awal</label>
                            <input type="date" name="start_date" class="form-control form-control-sm"
                                value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Akhir</label>
                            <input type="date" name="end_date" class="form-control form-control-sm"
                                value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Cari</label>
                            <input type="text" name="search" class="form-control form-control-sm"
                                placeholder="Cari laporan..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-dark btn-sm fw-bold me-2 mt-1">Cari</button>
                            <a href="{{ route('index.rutin') }}"
                                class="btn btn-danger btn-sm fw-bold me-2">Reset</a>
                            <button type="button" class="btn btn-primary btn-sm fw-bold" id="btn-cetak">Cetak</button>
                        </div>
                    </div>
                </form>

                {{-- Data Table --}}
                <div class="table-responsive">
                    <table id="basic-datatable" class="table table-bordered table-sm border-dark"
                        style="font-size: 0.8rem;color:black">
                        <thead class="align-middle">
                            <tr style="height: 40px" class="text-center fw-bold text-uppercase fs-6">
                                <th>No</th>
                                <th>Hari</th>
                                <th>Waktu</th>
                                <th>Uraian Kegiatan</th>
                                <th>Keterangan</th>
                                <th>Foto</th>
                                <th>Foto</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="align-middle">
                            @forelse ($psirutins as $index => $psirutin)
                                <tr>
                                    {{-- Numbering yang konsisten across pagination --}}
                                    <td style="text-align:right">
                                        {{ ($psirutins->currentPage() - 1) * $psirutins->perPage() + $index + 1 }}.
                                    </td>
                                    <td style="text-align:center">
                                        {{ \Carbon\Carbon::parse($psirutin->hari ?? '')->locale('id')->translatedFormat('d F Y') }}
                                    </td>
                                    <td style="text-align:center">{{ $psirutin->waktu ?? '' }} wib</td>
                                    <td style="text-align:left">{!! $psirutin->uraian ?? '' !!}</td>
                                    <td style="text-align:center">{{ $psirutin->kegiatan?->nama_kegiatan ?? '' }}, {{ $psirutin->kasus?->nama_kasus ?? '' }}</td>
                                    <td class="text-center align-middle" style="width:80px;">
                                        @if ($psirutin->image1)
                                            <img src="{{ asset('storage/' . $psirutin->image1) }}" width="80"
                                                height="50" class="border border-secondary-subtle" data-bs-toggle="modal"
                                                data-bs-target="#imageModal"
                                                onclick="showImage('{{ asset('storage/' . $psirutin->image1) }}')"
                                                style="cursor: pointer;">
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle" style="width:80px;">
                                        @if ($psirutin->image2)
                                            <img src="{{ asset('storage/' . $psirutin->image2) }}" width="80"
                                                height="50" class="border border-secondary-subtle" data-bs-toggle="modal"
                                                data-bs-target="#imageModal"
                                                onclick="showImage('{{ asset('storage/' . $psirutin->image2) }}')"
                                                style="cursor: pointer;">
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="align-middle" style="width:100px">
                                        <div class="d-flex justify-content-center">
                                            <a href="{{ route('edit.rutin', $psirutin->hashid) }}"
                                                class="btn btn-info btn-sm me-1" data-bs-toggle="tooltip"
                                                data-bs-placement="bottom" data-bs-title="EDIT"
                                                onclick="return confirm('Apakah Anda yakin ingin memperbaharui?')">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                            <a href="{{ route('show.rutin', $psirutin->hashid) }}"
                                                class="btn btn-secondary btn-sm me-1" data-bs-toggle="tooltip"
                                                data-bs-placement="bottom" data-bs-title="LIHAT">
                                                <i class="mdi mdi-eye"></i>
                                            </a>
                                            <form action="{{ route('destroy.rutin', $psirutin->hashid) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" data-bs-toggle="tooltip"
                                                    data-bs-placement="bottom" data-bs-title="HAPUS"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Data Akan di Hapus Permanen, Apakah Anda Ingin Menghapus Data Ini ?')">
                                                    <i class="mdi mdi-delete-variant"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-danger">

                                        Tidak ada data ditemukan
                                        @if (request('search') || request('start_date') || request('end_date'))
                                            untuk kriteria pencarian yang diberikan.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Quick Stats --}}
                @if ($psirutins->count() > 0)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="bg-light p-2 border rounded">
                                <small class="text-black">
                                    <i class="mdi mdi-information-outline"></i>
                                    <strong>Ringkasan:</strong> |
                                    Total keseluruhan: {{ \app\Models\Psi\Operasional\OperasionalRutin::count() }} data
                                </small>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal untuk preview gambar --}}
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Preview Gambar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" class="img-fluid" alt="Preview">
                </div>
            </div>
        </div>
    </div>

    <script>
        // Fungsi untuk cetak laporan
        document.getElementById('btn-cetak').addEventListener('click', function() {
            // Ambil parameter filter dari URL
            const urlParams = new URLSearchParams(window.location.search);
            const startDate = urlParams.get('start_date') || '';
            const endDate = urlParams.get('end_date') || '';
            const search = urlParams.get('search') || '';

            // Redirect ke halaman cetak dengan parameter yang sama
            window.open('{{ route('cetak.rutin') }}?start_date=' + startDate + '&end_date=' + endDate +
                '&search=' + search, '_blank');
        });

        // Fungsi untuk menampilkan gambar di modal
        function showImage(src) {
            document.getElementById('modalImage').src = src;
        }

        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
        
    </script>
@endsection
