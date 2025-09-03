@csrf
@if (isset($psirutin))
    @method('PUT')
@endif

<div class="row gx-3">
    <div class="row">
        <div class="col-md-12">
            {{-- Tombol Aksi --}}
            <div class="mb-3">                
                <a href="{{ route('index.rutin') }}" class="btn btn-secondary btn-sm text-uppercase fw-bold">Kembali</a>
            </div>
        </div>
    </div>

    {{-- Kolom Kiri --}}
    <div class="col-lg-5 col-md-5 mb-2">
        <div class="row">
            <div class="col-md-6">
                <label class="form-label">Tanggal</label>
                <div class="fs-6 text-danger fw-bold">
                    @error('hari')
                        {{ $message }}
                    @enderror
                </div>
                <input type="date" name="hari" class="form-control form-control-sm mb-2"
                    value="{{ old('hari', $psirutin->hari ?? '') }}">
            </div>

            <div class="col-md-6">
                <label class="form-label">Waktu</label>
                <div class="fs-6 text-danger fw-bold">
                    @error('waktu')
                        {{ $message }}
                    @enderror
                </div>
                <input type="text" name="waktu" class="form-control form-control-sm mb-2" maxlength="5" placeholder="12:12"
                    value="{{ old('waktu', $psirutin->waktu ?? '') }}">
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <label class="form-label">Lokasi</label>
                <div class="fs-6 text-danger fw-bold">
                    @error('lokasi_id')
                        {{ $message }}
                    @enderror
                </div>
                <select name="lokasi_id" class="form-control form-control-sm mb-2 select2" data-toggle="select2">
                    <option value="">Pilih Lokasi</option>
                    @foreach($lokasiList as $lokasi)
                         <option value="{{ $lokasi->id }}" {{ old('lokasi_id', $psirutin->lokasi_id ?? '') == $lokasi->id ? 'selected' : '' }}>
                            {{ $lokasi->nama_lokasi }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <label class="form-label">Kecamatan</label>
                <div class="fs-6 text-danger fw-bold">
                    @error('kecamatan_id')
                        {{ $message }}
                    @enderror
                </div>
                <select name="kecamatan_id" class="form-control form-control-sm mb-2 select2" data-toggle="select2">
                    <option value="">Pilih Kecamatan</option>
                    @foreach($kecamatanList as $kecamatan)
                        <option value="{{ $kecamatan->id }}" {{ old('kecamatan_id', $psirutin->kecamatan_id ?? '') == $kecamatan->id ? 'selected' : '' }}>
                            {{ $kecamatan->nama_kecamatan }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <label class="form-label">Kegiatan</label>
                <div class="fs-6 text-danger fw-bold">
                    @error('kegiatan_id')
                        {{ $message }}
                    @enderror
                </div>
                <select name="kegiatan_id" class="form-control form-control-sm mb-2 select2" data-toggle="select2">
                    <option value="">Pilih Kegiatan</option>
                    @foreach($kegiatanList as $kegiatan)
                        <option value="{{ $kegiatan->id }}" {{ old('kegiatan_id', $psirutin->kegiatan_id ?? '') == $kegiatan->id ? 'selected' : '' }}>
                            {{ $kegiatan->nama_kegiatan }}
                        </option>
                    @endforeach
                </select>
            </div>            
        </div>

        <div class="row">
            <div class="col-md-12">
                <label class="form-label">Qanun/Perda</label>
                <div class="fs-6 text-danger fw-bold">
                    @error('qanun_id')
                        {{ $message }}
                    @enderror
                </div>
                <select name="qanun_id" id="qanun_id" class="form-control form-control-sm mb-2 select2" data-toggle="select2">
                    <option value="">Pilih Qanun/Perda</option>
                    @foreach($qanunList as $qanun)
                        <option value="{{ $qanun->id }}" {{ old('qanun_id', $psirutin->qanun_id ?? '') == $qanun->id ? 'selected' : '' }}>
                            {{ $qanun->nama_qanun }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <label class="form-label">Ket Kegiatan</label>
                <div class="fs-6 text-danger fw-bold">
                    @error('kasus_id')
                        {{ $message }}
                    @enderror
                </div>
                <select name="kasus_id" id="kasus_id" class="form-control form-control-sm mb-2 select2" data-toggle="select2">
                    <option value="">Pilih Ket Kegiatan</option>
                    @php
                        $selectedQanunId = old('qanun_id', $psirutin->qanun_id ?? null);
                        $selectedKasusId = old('kasus_id', $psirutin->kasus_id ?? null);
                    @endphp

                    @if($selectedQanunId)
                        @foreach($kasusList->where('qanun_id', $selectedQanunId) as $kasus)
                            <option value="{{ $kasus->id }}" {{ $selectedKasusId == $kasus->id ? 'selected' : '' }}>
                                {{ $kasus->nama_kasus }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>            
        </div> 
        
    </div>

    
    {{-- Kolom Kanan --}}
    <div class="col-lg-7 col-md-7">
        <div class="row">
            <div class="col-md-12">
                <label class="form-label">Uraian Kegiatan</label>
                <div class="fs-6 text-danger fw-bold">
                    @error('uraian')
                        {{ $message }}
                    @enderror
                </div>
                <textarea name="uraian" class="form-control form-control-sm text-left mb-2" id="summernote">{{ old('uraian', $psirutin->uraian ?? '') }}</textarea>
            </div>
        </div>
        
        <div class="row mt-2">
            <div class="col-md-12">
                <label class="form-label">Kendala</label>
                <div class="fs-6 text-danger fw-bold">
                    @error('kendala')
                        {{ $message }}
                    @enderror
                </div>
                <textarea name="kendala" class="form-control form-control-sm text-left mb-2" rows="3" placeholder="Isi jika diperlukan">{{ old('kendala', $psirutin->kendala ?? '') }}</textarea>
            </div>
        </div>
        
        {{-- Bagian Gambar  --}}
        <div class="row">
            @for($i = 1; $i <= 4; $i++)
                @php
                    $imageField = 'image' . $i;
                    $hasImage = isset($psirutin) && $psirutin->$imageField;
                    $imageUrl = $hasImage ? Storage::url($psirutin->$imageField) : asset('/build/images/User.png');
                @endphp
                
                <div class="col-md-3 mb-3">
                    <label class="form-label">Foto {{ $i }}</label>
                    <input type="file" name="image{{ $i }}" class="form-control form-control-xs" id="gambarInput{{ $i }}" onchange="previewGambar({{ $i }})">
                    
                    <div class="position-relative mt-2" style="width: auto; height: auto;">
                        <img id="gambarPreview{{ $i }}"
                            src="{{ $imageUrl }}"
                            alt="Preview Gambar {{ $i }}"
                            class="img-thumbnail {{ $hasImage ? '' : '' }}"
                            style="width: 100%; height: 100px; object-fit: cover;">

                        <button type="button" id="btnResetGambar{{ $i }}" onclick="resetGambar({{ $i }})"
                            class="btn btn-sm btn-danger position-absolute"
                            style="top: 5px; right: 5px; border-radius: 50%; {{ $hasImage ? '' : 'display: none;' }} z-index: 10;">
                            &times;
                        </button>
                    </div>
                    
                    @if ($hasImage)
                        <div class="mt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="hapus_gambar{{ $i }}" id="hapusGambarCheck{{ $i }}" value="1">
                                <label class="form-check-label fs-6 fw-bold" for="hapusGambarCheck{{ $i }}">Hapus Foto</label>
                            </div>
                        </div>
                    @endif
                </div>
            @endfor
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            {{-- Tombol Aksi --}}
            <div class="mb-4 py-3">
                <button type="submit" class="btn btn-primary btn-sm mx-2 text-uppercase fw-bold" onclick="return confirm('Apakah Anda yakin ingin mengimpan?')">
                    {{ isset($psirutin) ? 'Update' : 'Simpan' }}
                </button>
                <a href="{{ route('index.rutin') }}" class="btn btn-secondary btn-sm text-uppercase fw-bold">Kembali</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Inisialisasi tampilan gambar saat halaman dimuat
        @for($i = 1; $i <= 4; $i++)
            @php
                $hasImage = isset($psirutin) && $psirutin->{'image' . $i};
            @endphp
            @if ($hasImage)
                document.getElementById('btnResetGambar{{ $i }}').style.display = 'none';
            @endif
        @endfor
    });

    // Fungsi preview gambar untuk masing-masing input
    function previewGambar(index) {
        const input = document.getElementById('gambarInput' + index);
        const preview = document.getElementById('gambarPreview' + index);
        const resetBtn = document.getElementById('btnResetGambar' + index);
        const defaultImage = '{{ asset('/build/images/User.png') }}';

        if (!input || !preview || !resetBtn) return;

        const file = input.files?.[0];
        if (file) {
            // Validasi ukuran file (70KB = 70 * 1024 bytes)
            if (file.size > 1024 * 2024) {
                alert('Ukuran file terlalu besar! Maksimal 2 MB');
                input.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                resetBtn.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }

    // Fungsi reset gambar untuk masing-masing input
    function resetGambar(index) {
        const input = document.getElementById('gambarInput' + index);
        const preview = document.getElementById('gambarPreview' + index);
        const resetBtn = document.getElementById('btnResetGambar' + index);
        const defaultImage = '{{ asset('/build/images/User.png') }}';

        if (!input || !preview || !resetBtn) return;

        input.value = '';
        
        // Jika ada gambar lama, tampilkan kembali
        @for($i = 1; $i <= 4; $i++)
            if (index === {{ $i }}) {
                @if (isset($psirutin) && $psirutin->{'image' . $i})
                    preview.src = '{{ Storage::url($psirutin->{'image' . $i}) }}';
                @else
                    preview.src = defaultImage;
                @endif
            }
        @endfor
        
        resetBtn.style.display = 'none';
    }

    // Select2 dinamis Qanun dan Kasus
    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi select2
        $('.select2').select2();
        
        // Fungsi untuk memuat kasus berdasarkan qanun
        function loadKasusByQanun(qanunId, selectedKasusId = null) {
            if (qanunId) {
                // Kirim permintaan AJAX untuk mendapatkan kasus berdasarkan qanun
                $.ajax({
                    url: '/get-psi-kasus-by-qanun/' + qanunId,
                    type: 'GET',
                    success: function(data) {
                        // Kosongkan dropdown kasus
                        $('#kasus_id').empty().append('<option value="">Pilih Keterangan Kegiatan</option>');
                        
                        // Isi dropdown dengan data kasus yang diterima
                        $.each(data, function(index, kasus) {
                            $('#kasus_id').append(
                                $('<option>', {
                                    value: kasus.id,
                                    text: kasus.nama_kasus,
                                    selected: (selectedKasusId == kasus.id)
                                })
                            );
                        });
                        
                        // Refresh select2
                        $('#kasus_id').trigger('change');
                    },
                    error: function() {
                        console.log('Error loading kasus data');
                    }
                });
            } else {
                // Jika tidak ada qanun yang dipilih, kosongkan dropdown kasus
                $('#kasus_id').empty().append('<option value="">Pilih Keterangan Kegiatan</option>').trigger('change');
            }
        }
        
        // Event listener untuk perubahan pada dropdown qanun
        $('#qanun_id').on('change', function() {
            var qanunId = $(this).val();
            loadKasusByQanun(qanunId);
        });
        
        // Jika dalam mode edit dan sudah ada qanun yang dipilih, muat kasus terkait
        @if(isset($psirutin) && $psirutin->qanun_id)
            var qanunId = {{ $psirutin->qanun_id }};
            var kasusId = {{ $psirutin->kasus_id ?? 'null' }};
            loadKasusByQanun(qanunId, kasusId);
        @endif
    });
    // Jika ada old('qanun_id'), jalankan loadKasusByQanun saat halaman reload
    var oldQanunId = "{{ old('qanun_id', $psirutin->qanun_id ?? '') }}";
    var oldKasusId = "{{ old('kasus_id', $psirutin->kasus_id ?? '') }}";

    if (oldQanunId) {
        loadKasusByQanun(oldQanunId, oldKasusId);
    }

    // End select
</script>

<style>
    .select2-container .select2-selection--single {
        height: 31px;
        font-size: 0.885rem;
        border: 1px solid #252525;
        color: #000000;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 29px;
        padding-left: 8px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 29px;
    }
    .note-editor{
        background: #ffffff;
    }
</style>

