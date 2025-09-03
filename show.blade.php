@extends('layouts.psi.operasional.main')
@section('content')

<div class="row mt-1">
    <div class="card">
        <div class="card-header">
            <div class="card-tittle text-center">
                <h5 class="text-uppercase">progres kegiatan</h5>
            </div>
        </div>
        <div class="card-body">
            <div class="tampil mb-4 text-uppercase d-flex">
                <a href="{{ route('index.rutin') }}" class="btn btn-secondary btn-sm fw-bold">kembali</a>
                <button class="btn btn-primary btn-sm fw-bold text-uppercase mx-2" onclick="window.print()">
                    Print
                </button>
            </div>
            <table>
                <tr class="align-top">
                    <td style="width: 12rem">Nama Kegiatan</td>
                    <td style="width: 1rem">:</td>
                    <td style="text-align:justify">{!! $psirutin->uraian !!}</td>
                </tr>
                <tr class="align-top">
                    <td class="text-capitalize">opd penaggung jawab</td>
                    <td>:</td>
                    <td>
                        @if ($opd)
                            {{ $opd->nama_opd }} {{ $opd->kota_kab }}
                        @else
                             
                        @endif
                    </td>
                </tr>
                <tr class="align-top">
                    <td class="text-capitalize">waktu pelaksanaan</td>
                    <td>:</td>
                    <td>{{ \Carbon\Carbon::parse($psirutin->hari)->locale('id')->translatedFormat('l, d F Y') }}</td>
                </tr>
                <tr class="align-top">
                    <td class="text-capitalize">tahap pelaksanaan</td>
                    <td>:</td>
                    <td>{{ $psirutin->kegiatan->nama_kegiatan }}</td>
                </tr>
                <tr class="align-top">
                    <td class="text-capitalize">output</td>
                    <td>:</td>
                    <td>Terlaksananya Penegakan Perda dan Qanun Syariat Islam</td>
                </tr>
                <tr class="align-top">
                    <td class="text-capitalize">persentase progress</td>
                    <td>:</td>
                    <td>100%</td>
                </tr>
            </table>

            {{-- Container gambar dengan flexbox 2 per baris --}}
            <div class="image-container mt-3">
                @foreach (['image1', 'image2', 'image3', 'image4'] as $img)
                    @if ($psirutin->$img)
                        <img src="{{ asset('storage/' . $psirutin->$img) }}" 
                             alt="Foto {{ $img }}" 
                             class="print-image border border-secondary-subtle"
                             data-bs-toggle="modal"
                             data-bs-target="#imageModal"
                             onclick="showImage('{{ asset('storage/' . $psirutin->$img) }}')"
                             style="cursor: pointer;">
                    @else
                        <span class="text-muted no-image">-</span>
                    @endif
                @endforeach
            </div>

            <table class="mt-3">
                <tr>
                    <td class="text-decoration-underline fw-bold">Permasalahan/Kendala</td>
                </tr>
                <tr>
                    <td style="text-align:justify;">
                        {{ $psirutin->kendala }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>

<style>
    .image-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 1rem;
        box-sizing: border-box;
    }
    .print-image, .no-image {
        width: calc(50% - 5px); /* 2 per baris dengan gap 10px */
        height: 260px;
        object-fit: cover;
        border-radius: 4px;
        box-sizing: border-box;
        vertical-align: top;
        display: block;
        margin: 0;
        padding: 0;
    }
    .print-image {
        cursor: pointer;
        border: 1px solid #adb5bd; /* border-secondary-subtle */
    }
    .no-image {
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6c757d;
        border: 1px dashed #6c757d;
        font-style: italic;
        font-size: 1rem;
        user-select: none;
    }

    @media print {
        @page {
            size: A4;
            margin: 15mm 15mm 15mm 15mm;
        }
        body {
            font-size: 12pt;
            line-height: 1.5;
            font-family: "Times New Roman", Times, serif;
        }
        .tampil {
            display: none !important;
        }
        .card, .card-header, .navbar-custom {
            border: none !important;
            box-shadow: none !important;
        }
        .image-container {
            gap: 8px;
            justify-content: flex-start;
            align-items: stretch;
        }
        .print-image, .no-image {
            width: calc(50% - 4px);
            height: 260px;
            page-break-inside: avoid;
        }
        .no-image {
            display: none;
        }
    }
</style>

@endsection
