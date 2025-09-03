@extends('layouts.maincetak')
@section('cetak')
    <div class="header">
        <span style="font-size:1rem;font-weight: bold;">
            Laporan Rutin Pengawasan dan Penegakan Syariat Islam
        </span>
        <br>
        @if($tanggalMulai && $tanggalSelesai)
            <span style="font-size:0.9rem;">
                Periode: {{ \Carbon\Carbon::parse($tanggalMulai)->locale('id')->translatedFormat('d F Y') }} - 
                {{ \Carbon\Carbon::parse($tanggalSelesai)->locale('id')->translatedFormat('d F Y') }}
            </span>
        @elseif($tanggalMulai)
            <span style="font-size:0.9rem;">
                Mulai Tanggal: {{ \Carbon\Carbon::parse($tanggalMulai)->locale('id')->translatedFormat('d F Y') }}
            </span>
        @elseif($tanggalSelesai)
            <span style="font-size:0.9rem;">
                Sampai Tanggal: {{ \Carbon\Carbon::parse($tanggalSelesai)->locale('id')->translatedFormat('d F Y') }}
            </span>
        @endif
    </div>
    <div class="table-sm">
        <table>
            <thead>
                <tr style="height: 40px" class="text-center fw-bold text-uppercase">
                    <th>No</th>
                    <th>Hari</th>
                    <th>Waktu</th>
                    <th>Uraian Kegiatan</th>
                    <th>Keterangan</th>
                    <th>Foto</th>
                    <th>Foto</th>
                    <th>Foto</th>
                </tr>
            </thead>
            <tbody style="font-size: 0.9rem;color:black;font-family: Times New Roman, Times, serif;">
                @forelse ($psirutins as $no => $psirutin)
                    <tr>
                        <td class="text-center">{{ $no + 1 }}</td>
                        <td class="text-center">
                            {{ \Carbon\Carbon::parse($psirutin->hari)->locale('id')->translatedFormat('l, d F Y') }}</td>
                        <td class="text-center">{{ $psirutin->waktu }} wib</td>
                        <td style="text-align:justify">{!! $psirutin->uraian !!}</td>
                        <td class="text-center">{{ $psirutin->kegiatan->nama_kegiatan }}, {{ $psirutin->kasus->nama_kasus }}</td>
                        <td class="text-center align-middle" style="width:150px;">
                            @if ($psirutin->image1)
                                <img src="{{ asset('storage/' . $psirutin->image1) }}" width="150" height="100"
                                    class="border border-secondary-subtle">
                            @endif
                        </td>
                        <td class="text-center align-middle" style="width:150px;">
                            @if ($psirutin->image2)
                                <img src="{{ asset('storage/' . $psirutin->image2) }}" width="150" height="100"
                                    class="border border-secondary-subtle">
                            @endif
                        </td>
                        <td class="text-center align-middle" style="width:150px;">
                            @if ($psirutin->image3)
                                <img src="{{ asset('storage/' . $psirutin->image3) }}" width="150" height="100"
                                    class="border border-secondary-subtle">
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada data ditemukan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($psirutins->count() > 0)
        <div class="summary">
            <strong>Ringkasan:</strong>
            Total Data: {{ $psirutins->count() }}
            @if($tanggalMulai && $tanggalSelesai)
                <br>Periode: {{ \Carbon\Carbon::parse($tanggalMulai)->locale('id')->translatedFormat('d F Y') }} - 
                {{ \Carbon\Carbon::parse($tanggalSelesai)->locale('id')->translatedFormat('d F Y') }}
            @endif
        </div>
    @endif

    <div class="signature-container">
        <!-- Baris pertama: Kepala Bidang dan Kepala Seksi sejajar -->
        <div class="signature-row">
            <div class="signature-box mt" style="margin-top: 40px">
                @if ($pimpinan['kepala_bidang'])
                    <p class="signature-details">{{ $pimpinan['kepala_bidang']->jabatan }} {{ $pimpinan['kepala_bidang']->bidang }}</p>
                @else
                    <p class="signature-details">Kepala Bidang Penegakan Syariat Islam</p>
                @endif
                <div class="signature-space "></div>
                @if ($pimpinan['kepala_bidang'])
                    <p class="signature-name">{{ $pimpinan['kepala_bidang']->nama }}</p>
                    <p class="signature-details">{{ $pimpinan['kepala_bidang']->pangkat }} / Nip. {{ $pimpinan['kepala_bidang']->nip }}</ps=>
                @else
                    <p class="signature-name">Nama Kepala Bidang</plass=>
                    <p class="signature-details">Pangkat / Nip. -</pass=>
                @endif
            </div>
            
            <div class="signature-box">
                <p class="signature-details">Banda Aceh, {{ now()->timezone('Asia/Jakarta')->locale('id')->translatedFormat('d F Y') }}</p>
                @if ($pimpinan['kepala_seksi'])
                    <p class="signature-details">{{ $pimpinan['kepala_seksi']->jabatan }} {{ $pimpinan['kepala_seksi']->bidang }}</p>
                @else
                    <p class="signature-details">Kepala Seksi Pembinaan dan Pengawasan</p>
                @endif
                <p class="signature-details">Penegakan Syariat Islam</p>
                <div class="signature-space"></div>
                @if ($pimpinan['kepala_seksi'])
                    <p class="signature-name">{{ $pimpinan['kepala_seksi']->nama }}</p>
                    <p class="signature-details">{{ $pimpinan['kepala_seksi']->pangkat }} / Nip. {{ $pimpinan['kepala_seksi']->nip }}</p>
                @else
                    <p class="signature-name">Nama Kepala Seksi</p>
                    <p class="signature-details">Pangkat / Nip. -</p>
                @endif
            </div>
        </div>
        
        <!-- Baris kedua: Kepala Satuan di tengah bawah -->
        <div class="signature-row mt-3">
            <div class="signature-box-center">
                <p class="signature-details">Mengetahui,</p>
                @if ($pimpinan['kepala_satuan'])
                    <p class="signature-details">{{ $pimpinan['kepala_satuan']->jabatan }}
                    Satuan Polisi Pamong Praja </p>
                    <p class="signature-details">dan Wilayatul Hisbah Kota Banda Aceh</p>
                @else
                    <p>Kepala Satuan Polisi Pamong Praja</p>
                    <p>dan Wilayatul Hisbah Kota Banda Aceh</p>
                @endif
                <div class="signature-space"></div>
                @if ($pimpinan['kepala_satuan'])
                    <p class="signature-name">{{ $pimpinan['kepala_satuan']->nama }}</p>
                    <p class="signature-details">{{ $pimpinan['kepala_satuan']->pangkat }} / Nip. {{ $pimpinan['kepala_satuan']->nip }}</p>
                @else
                    <p class="signature-name">Nama Kepala Satuan</p>
                    <p class="signature-details">Pangkat / Nip. -</p>
                @endif
            </div>
        </div>
    </div>

    <div class="footers">
        {{-- isi --}}
    </div>

    <script>
        window.print();
    </script>
    <style>
        .summary {
            margin-top: 10px;
            padding: 5px;
            background-color: #e9ecef;
            border-radius: 4px;
            text-align: center;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .signature-container {
            margin-top: 10px;
            width: 100%;
        }
        
        .signature-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0px;
        }
        
        .signature-box {
            flex: 1;
            text-align: center;
            padding: 0 0px;
        }
        
        .signature-box-center {
            flex: 1;
            text-align: center;
            margin: 0 auto;
            max-width: 50%;
        }
        
        .signature-space {
            height: 21px;
            margin-bottom: 0px;
        }
        
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
        }
        
        .signature-details {
            margin: 0;
            /* font-size: 11pt; */
        }

        body {
                font-size: 0.9rem;
                line-height: 1.2;
                font-family: 'Times New Roman', Times, serif !important;
            }
        
        @media print {
            @page {
                size: 330mm 210mm;
                margin: 20mm;
            }
            
            body {
                font-size: 0.9rem;
                line-height: 1.2;
                font-family: 'Times New Roman', Times, serif !important;
            }
            
            .signature-space {
                height: 40px;
            }
        }
    </style>
@endsection
