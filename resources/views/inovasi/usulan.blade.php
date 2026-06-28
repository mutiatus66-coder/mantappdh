@extends('index', ['dummy' => true])

@push('styles')
<link href="{{ asset('template.demo6/demo6/assets/css/CostumeStyle.css') }}" rel="stylesheet">
<style>
/* ── Stepper ───────────────────────────────────────────────────── */
.us-stepper{display:flex;align-items:flex-start;gap:0;margin-bottom:28px}
.us-step{display:flex;flex-direction:column;align-items:center;flex:1;position:relative}
.us-step:not(:last-child)::after{content:'';position:absolute;top:18px;left:60%;width:80%;height:2px;background:var(--ri-border);z-index:0}
.us-step.done:not(:last-child)::after{background:#1b84ff}
.us-step-num{width:36px;height:36px;border-radius:50%;border:2px solid var(--ri-border);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem;background:var(--ri-card-bg);color:var(--ri-text-muted);z-index:1;transition:.2s}
.us-step.active .us-step-num{border-color:#1b84ff;color:#1b84ff;background:#e8f0ff}
.us-step.done   .us-step-num{border-color:#1b84ff;background:#1b84ff;color:#fff}
.us-step-label{font-size:.72rem;margin-top:5px;color:var(--ri-text-muted);text-align:center}
.us-step.active .us-step-label,.us-step.done .us-step-label{color:#1b84ff;font-weight:600}

/* ── Panels ────────────────────────────────────────────────────── */
.us-panel{display:none}
.us-panel.active{display:block}

/* ── Form ──────────────────────────────────────────────────────── */
.us-section-title{font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--ri-text-muted);border-bottom:1px solid var(--ri-border);padding-bottom:6px;margin:20px 0 12px}
.us-label{font-size:.82rem;font-weight:600;margin-bottom:4px;display:block;color:var(--ri-text-primary)}
.us-required{color:#e53e3e;margin-left:2px}
.us-input,.us-textarea,.us-select{width:100%;padding:8px 12px;border:1px solid var(--ri-border);border-radius:8px;background:var(--ri-input-bg);color:var(--ri-text-primary);font-size:.875rem;transition:.15s}
.us-input:focus,.us-textarea:focus,.us-select:focus{outline:none;border-color:#1b84ff;box-shadow:0 0 0 3px rgba(27,132,255,.12)}
.us-input.is-invalid,.us-textarea.is-invalid,.us-select.is-invalid{border-color:#dc3545}
.us-textarea{resize:vertical;min-height:80px}
.us-error-msg{font-size:.74rem;color:#dc3545;margin-top:3px;display:none}
.us-file-wrap{border:1.5px dashed var(--ri-border);border-radius:8px;padding:10px 14px;cursor:pointer;display:flex;align-items:center;gap:10px;background:var(--ri-input-bg);transition:.15s}
.us-file-wrap:hover{border-color:#1b84ff;color:#1b84ff}
.us-file-wrap input{display:none}
.us-file-name{font-size:.76rem;color:#1b84ff;margin-top:3px}

/* ── Anggota row ───────────────────────────────────────────────── */
.anggota-row{display:flex;gap:8px;align-items:center;margin-bottom:6px}
.anggota-row .us-input{flex:1}
.btn-del-anggota{width:32px;height:32px;border:none;border-radius:6px;background:rgba(220,53,69,.1);color:#dc3545;cursor:pointer;flex-shrink:0;display:flex;align-items:center;justify-content:center}

/* ── Card usulan ───────────────────────────────────────────────── */
.u-card{border:1px solid var(--ri-border);border-radius:10px;background:var(--ri-card-bg);padding:16px 20px;margin-bottom:12px;transition:.15s}
.u-card:hover{box-shadow:0 2px 10px rgba(0,0,0,.08)}
.u-card-top{display:flex;justify-content:space-between;align-items:flex-start;gap:10px;flex-wrap:wrap}
.u-card-meta{display:flex;flex-wrap:wrap;gap:14px;margin-top:10px;font-size:.8rem;color:var(--ri-text-muted)}
.u-card-actions{display:flex;gap:6px;flex-wrap:wrap}
.badge-melengkapi{background:rgba(245,158,11,.12);color:#92400e;border:1px solid rgba(245,158,11,.3);border-radius:20px;padding:2px 10px;font-size:.74rem;font-weight:600;white-space:nowrap}
.badge-dinilai   {background:rgba(27,132,255,.12);color:#1e40af;border:1px solid rgba(27,132,255,.3);border-radius:20px;padding:2px 10px;font-size:.74rem;font-weight:600;white-space:nowrap}
.badge-selesai   {background:rgba(16,185,129,.12);color:#064e3b;border:1px solid rgba(16,185,129,.3);border-radius:20px;padding:2px 10px;font-size:.74rem;font-weight:600;white-space:nowrap}

/* ── Nav buttons ───────────────────────────────────────────────── */
.us-nav{display:flex;justify-content:space-between;margin-top:20px;padding-top:14px;border-top:1px solid var(--ri-border)}
</style>
@endpush

@section('content')
<div class="page-container">

    {{-- Header --}}
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h3 class="ec-title">Usulan Inovasi</h3>
            <p class="ec-subtitle">
                {{ $subEvent->event->nama_event ?? '' }}
                @if($subEvent->event && $subEvent->sub_event) &mdash; @endif
                {{ $subEvent->sub_event }}
                <span class="ms-2 badge bg-secondary" style="font-size:.68rem">{{ $subEvent->tahun }}</span>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ url('/inovasi/riwayat') }}" class="btn btn-dark">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
            <button class="btn btn-primary" id="btnTambah">
                <i class="bi bi-plus-lg me-1"></i> Tambah Usulan
            </button>
        </div>
    </div>

    {{-- Daftar kartu usulan --}}
    <div id="listUsulan">
        @forelse($usulans as $u)
        @php
            $bc = match($u->status) {
                'Melengkapi Data' => 'melengkapi',
                'Sedang Dinilai'  => 'dinilai',
                default           => 'selesai',
            };
        @endphp
        <div class="u-card" id="ucard-{{ $u->id }}">
            <div class="u-card-top">
                <div>
                    <span class="badge-{{ $bc }}">{{ $u->status }}</span>
                    <h6 class="mt-2 mb-0 fw-bold" style="color:var(--ri-text-primary)">{{ $u->nama_inovasi }}</h6>
                    <small class="text-muted">{{ $u->judul }}</small>
                </div>
                <div class="u-card-actions">
                    @if(!$u->is_submitted)
                    <button class="btn btn-warning btn-outline-sm btn-edit"
                            data-id="{{ $u->id }}"
                            data-u="{{ json_encode($u->load('anggotaTim')) }}">
                        <i class="bi bi-pencil"></i> Edit
                    </button>
                    <button class="btn btn-danger btn-outline-sm btn-hapus" data-id="{{ $u->id }}">
                        <i class="bi bi-trash"></i> Hapus
                    </button>
                    <button class="btn btn-sm btn-success btn-kirim" data-id="{{ $u->id }}">
                        <i class="bi bi-send"></i> Kirim
                    </button>
                    @else
                    <span class="text-muted small"><i class="bi bi-lock me-1"></i>Sudah dikirim</span>
                    @endif
                </div>
            </div>
            <div class="u-card-meta">
                <span><i class="bi bi-person me-1"></i>{{ $u->ketua_nama }}</span>
                <span><i class="bi bi-grid me-1"></i>{{ $u->bidang->nama ?? '-' }}</span>
                <span><i class="bi bi-tag me-1"></i>{{ ucfirst($u->kategori) }}</span>
                @if($u->anggotaTim->count())
                <span><i class="bi bi-people me-1"></i>{{ $u->anggotaTim->count() }} anggota</span>
                @endif
                @if($u->link_video)
                <a href="{{ $u->link_video }}" target="_blank" class="text-decoration-none text-danger">
                    <i class="bi bi-youtube me-1"></i>Video
                </a>
                @endif
            </div>
        </div>
        @empty
        <div class="text-center py-5 text-muted" id="emptyState">
            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
            <p>Belum ada usulan. Klik <strong>Tambah Usulan</strong> untuk mulai.</p>
        </div>
        @endforelse
    </div>

</div>

{{-- ═══════════════════════════════════════════════════ --}}
{{--  MODAL FORM USULAN — 3 LANGKAH                       --}}
{{-- ═══════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalUsulan" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-3 shadow-lg">

            <div class="modal-header px-5 py-4">
                <h5 class="modal-title fw-semibold" id="modalTitle">Tambah Usulan Inovasi</h5>
                <button type="button" class="btn-close" id="btnClose"></button>
            </div>

            <div class="modal-body px-5 py-4">

                {{-- Stepper --}}
                <div class="us-stepper">
                    <div class="us-step active" id="sdot-1">
                        <div class="us-step-num">1</div>
                        <div class="us-step-label">Data Tim</div>
                    </div>
                    <div class="us-step" id="sdot-2">
                        <div class="us-step-num">2</div>
                        <div class="us-step-label">Narasi Inovasi</div>
                    </div>
                    <div class="us-step" id="sdot-3">
                        <div class="us-step-num">3</div>
                        <div class="us-step-label">Dokumen & Media</div>
                    </div>
                </div>

                <form id="fUsulan" enctype="multipart/form-data" novalidate>
                    @csrf
                    <input type="hidden" name="sub_event_id" value="{{ $subEvent->id }}">
                    <input type="hidden" id="fId">

                    {{-- ══ LANGKAH 1 — DATA TIM ══ --}}
                    <div class="us-panel active" id="panel-1">

                        <div class="us-section-title">Informasi Inovasi</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="us-label">Nama Inovasi <span class="us-required">*</span></label>
                                <input type="text" class="us-input" name="nama_inovasi" id="fNamaInovasi" placeholder="Nama inovasi yang diajukan">
                                <div class="us-error-msg" id="e-nama_inovasi"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="us-label">Judul / Topik <span class="us-required">*</span></label>
                                <input type="text" class="us-input" name="judul" id="fJudul" placeholder="Judul singkat inovasi">
                                <div class="us-error-msg" id="e-judul"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="us-label">Bidang <span class="us-required">*</span></label>
                                <select class="us-select" name="bidang_id" id="fBidangId">
                                    <option value="">-- Pilih Bidang --</option>
                                    @foreach($subEvent->bidangs->where('status','aktif') as $b)
                                    <option value="{{ $b->id }}">{{ $b->nama }}</option>
                                    @endforeach
                                </select>
                                <div class="us-error-msg" id="e-bidang_id"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="us-label">Kategori Interaksi <span class="us-required">*</span></label>
                                <input type="text" class="us-input" name="interaksi" id="fInteraksi" placeholder="Cth: Teknologi Tepat Guna">
                                <div class="us-error-msg" id="e-interaksi"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="us-label">Kategori Peserta <span class="us-required">*</span></label>
                                <select class="us-select" name="kategori" id="fKategori">
                                    <option value="">-- Pilih --</option>
                                    <option value="umum">Umum</option>
                                    <option value="pelajar">Pelajar</option>
                                </select>
                                <div class="us-error-msg" id="e-kategori"></div>
                            </div>
                        </div>

                        <div class="us-section-title">Data Tim</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="us-label">Nama Tim</label>
                                <input type="text" class="us-input" name="nama_tim" id="fNamaTim" placeholder="Opsional">
                            </div>
                            <div class="col-md-6">
                                <label class="us-label">Inovator / Instansi <span class="us-required">*</span></label>
                                <input type="text" class="us-input" name="inovator" id="fInovator" placeholder="Nama instansi / lembaga">
                                <div class="us-error-msg" id="e-inovator"></div>
                            </div>
                        </div>

                        <div class="us-section-title">Data Ketua Tim</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="us-label">Nama Ketua <span class="us-required">*</span></label>
                                <input type="text" class="us-input" name="ketua_nama" id="fKetuaNama" placeholder="Nama lengkap ketua tim">
                                <div class="us-error-msg" id="e-ketua_nama"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="us-label">Email Ketua <span class="us-required">*</span></label>
                                <input type="email" class="us-input" name="ketua_email" id="fKetuaEmail" placeholder="email@contoh.com">
                                <div class="us-error-msg" id="e-ketua_email"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="us-label">No. WA / HP Ketua <span class="us-required">*</span></label>
                                <input type="text" class="us-input" name="ketua_wa" id="fKetuaWa" placeholder="08xxxxxxxxxx" maxlength="15">
                                <div class="us-error-msg" id="e-ketua_wa"></div>
                            </div>
                            <div class="col-md-8">
                                <label class="us-label">Alamat Ketua <span class="us-required">*</span></label>
                                <input type="text" class="us-input" name="alamat_ketua" id="fAlamatKetua" placeholder="Alamat lengkap">
                                <div class="us-error-msg" id="e-alamat_ketua"></div>
                            </div>
                            <div class="col-md-5">
                                <label class="us-label">No. KTP / Kartu Pelajar <span class="us-required">*</span></label>
                                <input type="text" class="us-input" name="ktp" id="fKtp" 
                                       placeholder="16 digit NIK" maxlength="16"
                                       oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                                       onblur="validateKtp()">
                                <div class="us-error-msg" id="e-ktp"></div>
                            </div>
                        </div>

                        {{-- Khusus pelajar --}}
                        <div id="secPelajar" style="display:none">
                            <div class="us-section-title">Data Sekolah</div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="us-label">Asal Sekolah <span class="us-required">*</span></label>
                                    <input type="text" class="us-input" name="asal_sekolah" id="fAsalSekolah">
                                    <div class="us-error-msg" id="e-asal_sekolah"></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="us-label">Nama Guru Pembimbing</label>
                                    <input type="text" class="us-input" name="nama_guru" id="fNamaGuru" placeholder="Opsional">
                                </div>
                            </div>
                        </div>

                        <div class="us-section-title">Anggota Tim</div>
                        <div id="anggotaList"></div>
                        <button type="button" class="btn btn-primary btn-outline-sm mt-1" id="btnAddAnggota">
                            <i class="bi bi-plus me-1"></i> Tambah Anggota
                        </button>

                    </div>{{-- /panel-1 --}}

                    {{-- ══ LANGKAH 2 — NARASI INOVASI ══ --}}
                    <div class="us-panel" id="panel-2">
                        @php
                        $narasi = [
                            ['latar_belakang',        'Latar Belakang Inovasi',       true,  'Jelaskan masalah yang melatarbelakangi inovasi ini...'],
                            ['kondisi_sebelumnya',    'Kondisi Sebelum Inovasi',      true,  'Bagaimana kondisi sebelum inovasi ini ada...'],
                            ['sasaran_tujuan',        'Sasaran & Tujuan Inovasi',     true,  'Siapa sasaran dan apa tujuan dari inovasi ini...'],
                            ['materi_inovasi',        'Materi / Spesifikasi Inovasi', false, 'Spesifikasi teknis atau materi yang digunakan (opsional)...'],
                            ['deskripsi',             'Deskripsi Inovasi',            true,  'Deskripsi singkat dan jelas tentang inovasi ini...'],
                            ['bahan_baku',            'Bahan Baku / Komponen',        false, 'Material atau komponen yang digunakan (opsional)...'],
                            ['cara_kerja',            'Cara Kerja Inovasi',           true,  'Jelaskan mekanisme atau cara kerja inovasi...'],
                            ['keunggulan',            'Keunggulan Inovasi',           true,  'Apa keunggulan dibanding solusi yang sudah ada...'],
                            ['hasil_diharapkan',      'Hasil yang Diharapkan',        true,  'Apa output atau dampak yang diharapkan...'],
                            ['manfaat',               'Manfaat bagi Masyarakat',      true,  'Manfaat langsung maupun tidak langsung...'],
                            ['rencana_berkelanjutan', 'Rencana Berkelanjutan',        true,  'Bagaimana inovasi ini akan terus berjalan ke depan...'],
                        ];
                        @endphp
                        @foreach($narasi as [$fname, $flabel, $fwajib, $fph])
                        <div class="mb-3">
                            <label class="us-label">
                                {{ $flabel }}
                                @if($fwajib)<span class="us-required">*</span>@endif
                            </label>
                            <textarea class="us-textarea"
                                      name="{{ $fname }}"
                                      id="f{{ implode('', array_map('ucfirst', explode('_', $fname))) }}"
                                      rows="3"
                                      placeholder="{{ $fph }}"></textarea>
                            <div class="us-error-msg" id="e-{{ $fname }}"></div>
                        </div>
                        @endforeach
                    </div>{{-- /panel-2 --}}

                    {{-- ══ LANGKAH 3 — DOKUMEN & MEDIA ══ --}}
                    <div class="us-panel" id="panel-3">

                        <div class="us-section-title">Upload Dokumen</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="us-label">Surat Pernyataan <small class="text-muted fw-normal">(PDF/DOC, maks 2 MB)</small></label>
                                <label class="us-file-wrap">
                                    <i class="bi bi-file-earmark-text fs-5"></i>
                                    <span>Klik untuk pilih file...</span>
                                    <input type="file" name="file_surat_pernyataan" id="fFileSurat" accept=".pdf,.doc,.docx">
                                </label>
                                <div class="us-file-name" id="nameSurat"></div>
                                <div class="us-error-msg" id="e-file_surat_pernyataan"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="us-label">Proposal / Dokumen Inovasi <small class="text-muted fw-normal">(PDF/DOC, maks 2 MB)</small></label>
                                <label class="us-file-wrap">
                                    <i class="bi bi-file-earmark-richtext fs-5"></i>
                                    <span>Klik untuk pilih file...</span>
                                    <input type="file" name="file_proposal" id="fFileProposal" accept=".pdf,.doc,.docx">
                                </label>
                                <div class="us-file-name" id="nameProposal"></div>
                                <div class="us-error-msg" id="e-file_proposal"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="us-label">Foto / Gambar Inovasi <small class="text-muted fw-normal">(JPG/PNG, maks 2 MB)</small></label>
                                <label class="us-file-wrap">
                                    <i class="bi bi-image fs-5"></i>
                                    <span>Klik untuk pilih gambar...</span>
                                    <input type="file" name="file_gambar" id="fFileGambar" accept="image/jpeg,image/png,.jpg,.jpeg,.png">
                                </label>
                                <div class="us-file-name" id="nameGambar"></div>
                                <div id="previewGambar" class="mt-2"></div>
                                <div class="us-error-msg" id="e-file_gambar"></div>
                            </div>
                        </div>

                        <div class="us-section-title">Link Video</div>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="us-label">Link Video YouTube</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background:var(--ri-input-bg);border-color:var(--ri-border)">
                                        <i class="bi bi-youtube text-danger"></i>
                                    </span>
                                    <input type="url" class="us-input" name="link_video" id="fLinkVideo"
                                           placeholder="https://www.youtube.com/watch?v=..."
                                           style="border-radius:0 8px 8px 0">
                                </div>
                                <div class="us-error-msg" id="e-link_video"></div>
                            </div>
                        </div>

                        {{-- File tersimpan (saat edit) --}}
                        <div id="existingFiles" style="display:none">
                            <div class="us-section-title">File Tersimpan</div>
                            <div id="existingFilesList" class="d-flex flex-wrap gap-2 mb-1"></div>
                            <small class="text-muted">Upload file baru hanya jika ingin mengganti.</small>
                        </div>

                    </div>{{-- /panel-3 --}}

                </form>
            </div>{{-- /modal-body --}}

            <div class="modal-footer px-5 py-3 justify-content-between">
                <div>
                    <button type="button" class="btn btn-secondary" id="btnPrev" style="display:none">
                        <i class="bi bi-arrow-left me-1"></i> Sebelumnya
                    </button>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-dark" id="btnBatal">Batal</button>
                    <button type="button" class="btn btn-success" id="btnNext">
                        Selanjutnya <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                    <button type="button" class="btn btn-success" id="btnSimpan" style="display:none">
                        <i class="bi bi-check2 me-1"></i> Simpan Usulan
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Modal Hapus --}}
<div class="modal fade" id="modalHapus" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg text-center px-4 py-4">
            <div class="mb-3"><i class="bi bi-exclamation-triangle-fill text-danger fs-1"></i></div>
            <h6 class="fw-bold mb-1">Hapus Usulan?</h6>
            <p class="text-muted small mb-3">Data tidak dapat dikembalikan.</p>
            <div class="d-flex gap-2 justify-content-center">
                <button class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-danger btn-sm" id="btnOkHapus">Hapus</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Kirim --}}
<div class="modal fade" id="modalKirim" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg text-center px-4 py-4">
            <div class="mb-3"><i class="bi bi-send-fill text-success fs-1"></i></div>
            <h6 class="fw-bold mb-1">Kirim Usulan?</h6>
            <p class="text-muted small mb-3">Setelah dikirim, usulan tidak dapat diedit atau dihapus.</p>
            <div class="d-flex gap-2 justify-content-center">
                <button class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-success btn-sm" id="btnOkKirim">Ya, Kirim</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    const CSRF   = document.querySelector('meta[name="csrf-token"]').content;
    const modal  = new bootstrap.Modal(document.getElementById('modalUsulan'));
    const mHapus = new bootstrap.Modal(document.getElementById('modalHapus'));
    const mKirim = new bootstrap.Modal(document.getElementById('modalKirim'));

    let step   = 1;
    let editId = null;

    // ── Stepper ───────────────────────────────────────────────────────
    function goStep(n) {
        step = n;
        for (let i = 1; i <= 3; i++) {
            const d = document.getElementById(`sdot-${i}`);
            const p = document.getElementById(`panel-${i}`);
            d.classList.remove('active','done');
            p.classList.remove('active');
            if (i < n)  d.classList.add('done');
            if (i === n) { d.classList.add('active'); p.classList.add('active'); }
        }
        document.getElementById('btnPrev').style.display  = n > 1 ? '' : 'none';
        document.getElementById('btnNext').style.display  = n < 3 ? '' : 'none';
        document.getElementById('btnSimpan').style.display = n === 3 ? '' : 'none';
    }

    document.getElementById('btnNext').onclick = () => {
        if (step === 1 && !validateStep1()) {
            toast('Mohon lengkapi semua field wajib di langkah ini.', false);
            return;
        }
        if (step < 3) goStep(step + 1);
    };
    document.getElementById('btnPrev').onclick = () => { if (step > 1) goStep(step - 1); };

    // ── Toast — muncul di bawah navbar, kanan atas (atas tombol Tambah) ──
    let _toastTimer = null;
    function toast(msg, ok = true) {
        // Hapus toast lama jika masih ada
        document.getElementById('ri-toast')?.remove();
        clearTimeout(_toastTimer);

        const el = document.createElement('div');
        el.id = 'ri-toast';
        el.style.cssText = [
            'position:fixed',
            'bottom:80px',       /* di atas footer */
            'right:20px',
            'z-index:9999',
            'min-width:280px',
            'max-width:360px',
            'padding:10px 14px',
            'border-radius:10px',
            'font-size:.85rem',
            'font-weight:500',
            'display:flex',
            'align-items:center',
            'gap:8px',
            'box-shadow:0 4px 16px rgba(0,0,0,.12)',
            'animation:toastIn .2s ease',
            `background:${ok ? 'rgba(16,185,129,.92)' : 'rgba(220,53,69,.90)'}`,
            `border:1px solid ${ok ? 'rgba(16,185,129,1)' : 'rgba(220,53,69,1)'}`,
            `color:${ok ? '#fff' : '#fff'}`,
        ].join(';');
        el.innerHTML = `<i class="bi bi-${ok ? 'check-circle-fill' : 'x-circle-fill'}" style="font-size:1rem;flex-shrink:0"></i>
            <span style="flex:1">${msg}</span>
            <button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;padding:0;margin-left:4px;opacity:.6;font-size:1rem;line-height:1;color:inherit">&times;</button>`;
        document.body.appendChild(el);
        _toastTimer = setTimeout(() => el?.remove(), 5000);
    }

    // Inject animasi toast sekali saja
    if (!document.getElementById('ri-toast-style')) {
        const s = document.createElement('style');
        s.id = 'ri-toast-style';
        s.textContent = `@keyframes toastIn{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}`;
        document.head.appendChild(s);
    }

    // ── Validasi email & nomor telepon (client-side) ──────────────────
    function isValidEmail(v) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v.trim());
    }
    function isValidPhone(v) {
        // Format: diawali 08, 628, atau +628; panjang 9–14 digit
        return /^(\+62|62|0)[0-9]{8,13}$/.test(v.trim().replace(/[\s\-]/g, ''));
    }

    function validateStep1() {
        let ok = true;
        clearErrors();

        const required1 = [
            'fNamaInovasi','fJudul','fBidangId','fInteraksi','fKategori',
            'fInovator','fKetuaNama','fAlamatKetua'
        ];
        required1.forEach(id => {
            const el = document.getElementById(id);
            if (!el) return;
            if (!el.value.trim()) {
                el.classList.add('is-invalid');
                const errEl = document.getElementById('e-' + el.name);
                if (errEl) { errEl.textContent = 'Field ini wajib diisi.'; errEl.style.display = 'block'; }
                ok = false;
            }
        });

        // Validasi KTP 16 digit
        const ktpEl = document.getElementById('fKtp');
        if (ktpEl) {
            const ktpVal = ktpEl.value.trim();
            const ktpErr = document.getElementById('e-ktp');
            if (!ktpVal) {
                ktpEl.classList.add('is-invalid');
                if (ktpErr) { ktpErr.textContent = 'No. KTP / Kartu Pelajar wajib diisi.'; ktpErr.style.display = 'block'; }
                ok = false;
            } else if (ktpVal.length !== 16) {
                ktpEl.classList.add('is-invalid');
                if (ktpErr) { ktpErr.textContent = 'NIK harus tepat 16 digit (saat ini ' + ktpVal.length + ' digit).'; ktpErr.style.display = 'block'; }
                ok = false;
            } else {
                ktpEl.classList.remove('is-invalid');
                ktpEl.classList.add('is-valid');
                if (ktpErr) { ktpErr.textContent = ''; ktpErr.style.display = 'none'; }
            }
        }

        // Validasi email
        const emailEl = document.getElementById('fKetuaEmail');
        if (emailEl) {
            if (!emailEl.value.trim()) {
                emailEl.classList.add('is-invalid');
                const e = document.getElementById('e-ketua_email');
                if (e) { e.textContent = 'Email wajib diisi.'; e.style.display = 'block'; }
                ok = false;
            } else if (!isValidEmail(emailEl.value)) {
                emailEl.classList.add('is-invalid');
                const e = document.getElementById('e-ketua_email');
                if (e) { e.textContent = 'Format email tidak valid. Contoh: nama@email.com'; e.style.display = 'block'; }
                ok = false;
            }
        }

        // Validasi no. WA
        const waEl = document.getElementById('fKetuaWa');
        if (waEl) {
            if (!waEl.value.trim()) {
                waEl.classList.add('is-invalid');
                const e = document.getElementById('e-ketua_wa');
                if (e) { e.textContent = 'No. WA / HP wajib diisi.'; e.style.display = 'block'; }
                ok = false;
            } else if (!isValidPhone(waEl.value)) {
                waEl.classList.add('is-invalid');
                const e = document.getElementById('e-ketua_wa');
                if (e) { e.textContent = 'Format tidak valid. Gunakan format: 08xxxxxxxxxx atau +628xxxxxxxxx'; e.style.display = 'block'; }
                ok = false;
            }
        }

        // Jika pelajar, asal_sekolah wajib
        if (document.getElementById('fKategori')?.value === 'pelajar') {
            const sekolahEl = document.getElementById('fAsalSekolah');
            if (sekolahEl && !sekolahEl.value.trim()) {
                sekolahEl.classList.add('is-invalid');
                const e = document.getElementById('e-asal_sekolah');
                if (e) { e.textContent = 'Asal sekolah wajib diisi untuk kategori pelajar.'; e.style.display = 'block'; }
                ok = false;
            }
        }

        return ok;
    }

    // ── Error helpers ─────────────────────────────────────────────────
    function clearErrors() {
        document.querySelectorAll('.us-error-msg').forEach(e => { e.style.display='none'; e.textContent=''; });
        document.querySelectorAll('.us-input,.us-textarea,.us-select').forEach(e => e.classList.remove('is-invalid'));
    }
    function showErrors(errs) {
        Object.entries(errs).forEach(([k, msgs]) => {
            const e = document.getElementById(`e-${k}`);
            const i = document.querySelector(`[name="${k}"]`);
            if (e) { e.textContent = msgs[0]; e.style.display = 'block'; }
            if (i) i.classList.add('is-invalid');
        });
    }

    // ── Kategori toggle ───────────────────────────────────────────────
    document.getElementById('fKategori').addEventListener('change', function () {
        document.getElementById('secPelajar').style.display = this.value === 'pelajar' ? '' : 'none';
    });

    // ── Anggota tim ───────────────────────────────────────────────────
    function addAnggota(val = '') {
        const row = document.createElement('div');
        row.className = 'anggota-row';
        row.innerHTML = `<input type="text" class="us-input" name="anggota[]" placeholder="Nama anggota" value="${val}">
                         <button type="button" class="btn-del-anggota" title="Hapus"><i class="bi bi-x-lg"></i></button>`;
        row.querySelector('.btn-del-anggota').onclick = () => row.remove();
        document.getElementById('anggotaList').appendChild(row);
    }
    document.getElementById('btnAddAnggota').onclick = () => addAnggota();

    // ── Aturan validasi file ───────────────────────────────────────────
    const FILE_RULES = {
        fFileSurat:    { maxMB: 2, mimes: ['application/pdf','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document'], exts: ['.pdf','.doc','.docx'], errId: 'e-file_surat_pernyataan' },
        fFileProposal: { maxMB: 2, mimes: ['application/pdf','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document'], exts: ['.pdf','.doc','.docx'], errId: 'e-file_proposal' },
        fFileGambar:   { maxMB: 2, mimes: ['image/jpeg','image/png'], exts: ['.jpg','.jpeg','.png'], errId: 'e-file_gambar' },
    };

    // Magic bytes tiap format
    const MAGIC = {
        '.pdf':  [0x25,0x50,0x44,0x46],
        '.doc':  [0xD0,0xCF,0x11,0xE0],
        '.docx': [0x50,0x4B,0x03,0x04],
        '.jpg':  [0xFF,0xD8,0xFF],
        '.jpeg': [0xFF,0xD8,0xFF],
        '.png':  [0x89,0x50,0x4E,0x47,0x0D,0x0A,0x1A,0x0A],
    };

    function showFileErr(errId, msg) {
        const el = document.getElementById(errId);
        if (el) { el.textContent = msg; el.style.display = 'block'; }
    }
    function clearFileErr(errId) {
        const el = document.getElementById(errId);
        if (el) { el.textContent = ''; el.style.display = 'none'; }
    }

    // ── File inputs dengan validasi 4 lapis ──────────────────────────
    function wireFile(inputId, nameId, previewId) {
        document.getElementById(inputId).addEventListener('change', function () {
            const f    = this.files[0];
            const rule = FILE_RULES[inputId];
            const self = this;

            if (rule) clearFileErr(rule.errId);
            document.getElementById(nameId).textContent = '';
            if (previewId) document.getElementById(previewId).innerHTML = '';

            if (!f) return;

            if (rule) {
                // Lapis 1: ekstensi
                const ext = '.' + f.name.split('.').pop().toLowerCase();
                if (!rule.exts.includes(ext)) {
                    showFileErr(rule.errId, `Format tidak valid. Gunakan: ${rule.exts.join(', ')}`);
                    self.value = ''; return;
                }

                // Lapis 2: MIME type browser
                if (f.type && !rule.mimes.includes(f.type)) {
                    showFileErr(rule.errId, `Tipe file tidak diizinkan (${f.type}).`);
                    self.value = ''; return;
                }

                // Lapis 3: ukuran
                if (f.size > rule.maxMB * 1024 * 1024) {
                    showFileErr(rule.errId, `Ukuran melebihi ${rule.maxMB} MB. File Anda: ${(f.size/1024/1024).toFixed(1)} MB`);
                    self.value = ''; return;
                }

                // Lapis 4: magic bytes (baca isi asli file)
                const sig = MAGIC[ext];
                if (sig) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const bytes = new Uint8Array(e.target.result);
                        const valid = sig.every((b, i) => bytes[i] === b);
                        if (!valid) {
                            showFileErr(rule.errId, `File terdeteksi tidak valid atau berbahaya. Pastikan file benar-benar ${ext}.`);
                            self.value = '';
                            document.getElementById(nameId).textContent = '';
                            if (previewId) document.getElementById(previewId).innerHTML = '';
                            return;
                        }
                        // Semua valid
                        document.getElementById(nameId).textContent = `✓ ${f.name} (${(f.size/1024).toFixed(0)} KB)`;
                        if (previewId) {
                            document.getElementById(previewId).innerHTML =
                                `<img src="${URL.createObjectURL(f)}" style="max-height:90px;border-radius:6px;border:1px solid var(--ri-border);margin-top:4px">`;
                        }
                    };
                    reader.readAsArrayBuffer(f.slice(0, 8));
                    return; // tunggu async
                }
            }

            document.getElementById(nameId).textContent = `✓ ${f.name}`;
        });
    }
    wireFile('fFileSurat',    'nameSurat',    null);
    wireFile('fFileProposal', 'nameProposal', null);
    wireFile('fFileGambar',   'nameGambar',   'previewGambar');

    // ── Reset modal ───────────────────────────────────────────────────
    function resetModal() {
        editId = null;
        document.getElementById('fUsulan').reset();
        document.getElementById('fId').value = '';
        document.getElementById('anggotaList').innerHTML = '';
        document.getElementById('secPelajar').style.display = 'none';
        document.getElementById('existingFiles').style.display = 'none';
        document.getElementById('existingFilesList').innerHTML = '';
        document.getElementById('previewGambar').innerHTML = '';
        ['nameSurat','nameProposal','nameGambar'].forEach(id => {
            document.getElementById(id).textContent = '';
        });
        clearErrors();
        goStep(1);
        document.getElementById('modalTitle').textContent = 'Tambah Usulan Inovasi';
    }

    // ── Buka tambah ───────────────────────────────────────────────────
    document.getElementById('btnTambah').onclick = () => { resetModal(); modal.show(); };

    // ── Buka edit ─────────────────────────────────────────────────────
    document.addEventListener('click', e => {
        const btn = e.target.closest('.btn-edit');
        if (!btn) return;
        resetModal();
        editId = btn.dataset.id;
        const u = JSON.parse(btn.dataset.u);
        document.getElementById('fId').value = u.id;
        document.getElementById('modalTitle').textContent = 'Edit Usulan Inovasi';

        // Halaman 1
        const map = {
            fNamaInovasi:'nama_inovasi', fJudul:'judul', fBidangId:'bidang_id',
            fInteraksi:'interaksi', fKategori:'kategori', fNamaTim:'nama_tim',
            fInovator:'inovator', fKetuaNama:'ketua_nama', fKetuaEmail:'ketua_email',
            fKetuaWa:'ketua_wa', fAlamatKetua:'alamat_ketua', fKtp:'ktp',
            fAsalSekolah:'asal_sekolah', fNamaGuru:'nama_guru',
        };
        Object.entries(map).forEach(([elId, key]) => {
            const el = document.getElementById(elId);
            if (el && u[key] != null) el.value = u[key];
        });
        if (u.kategori === 'pelajar') document.getElementById('secPelajar').style.display = '';

        // Halaman 2 — nama ID = fLaterBelakang, fKondisiSebelumnya, dst.
        const h2 = ['latar_belakang','kondisi_sebelumnya','sasaran_tujuan','materi_inovasi',
                     'deskripsi','bahan_baku','cara_kerja','keunggulan','hasil_diharapkan',
                     'manfaat','rencana_berkelanjutan'];
        h2.forEach(k => {
            const elId = 'f' + k.split('_').map(w => w.charAt(0).toUpperCase()+w.slice(1)).join('');
            const el = document.getElementById(elId);
            if (el && u[k]) el.value = u[k];
        });

        // Halaman 3
        if (u.link_video) document.getElementById('fLinkVideo').value = u.link_video;

        // File tersimpan
        const fmap = { file_surat_pernyataan:'Surat Pernyataan', file_proposal:'Proposal', file_gambar:'Gambar' };
        const list = document.getElementById('existingFilesList');
        let hasFile = false;
        Object.entries(fmap).forEach(([k, label]) => {
            if (u[k]) {
                hasFile = true;
                list.innerHTML += `<a href="/storage/${u[k]}" target="_blank"
                    class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-file-earmark me-1"></i>${label}</a>`;
            }
        });
        if (hasFile) document.getElementById('existingFiles').style.display = '';

        // Anggota tim
        (u.anggota_tim || []).forEach(a => addAnggota(a.nama_anggota));

        modal.show();
    });

    // ── Simpan ────────────────────────────────────────────────────────
    document.getElementById('btnSimpan').onclick = async () => {
        clearErrors();
        const btn = document.getElementById('btnSimpan');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...';

        const fd  = new FormData(document.getElementById('fUsulan'));
        const url = editId ? `/inovasi/${editId}` : '{{ route("inovasi.store") }}';
        if (editId) fd.append('_method', 'PUT');

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: fd
            });

            const data = await res.json();

            if (!res.ok || !data.success) {
                // Tampilkan error validasi Laravel (422)
                if (data.errors) showErrors(data.errors);
                if (data.errors) {
                const msgs = Object.values(data.errors).flat();
                toast('Gagal menyimpan: ' + msgs.join(' '), false);
            } else {
                toast(data.message || 'Gagal menyimpan. Periksa kembali isian Anda.', false);
            }
                return;
            }

            toast(data.message);
            modal.hide();
            // Reload halaman agar card baru/editan muncul
            setTimeout(() => location.reload(), 500);

        } catch (err) {
            console.error(err);
            toast('Terjadi kesalahan. Coba lagi.', false);
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check2 me-1"></i> Simpan Usulan';
        }
};

    // ── Tutup modal ───────────────────────────────────────────────────
    document.getElementById('btnBatal').onclick = () => modal.hide();
    document.getElementById('btnClose').onclick  = () => modal.hide();
    document.getElementById('modalUsulan').addEventListener('hidden.bs.modal', resetModal);

    // ── Hapus ─────────────────────────────────────────────────────────
    let hapusId = null;
    document.addEventListener('click', e => {
        const btn = e.target.closest('.btn-hapus');
        if (!btn) return;
        hapusId = btn.dataset.id;
        mHapus.show();
    });
    document.getElementById('btnOkHapus').onclick = async () => {
        mHapus.hide();
        try {
            const res  = await fetch(`/inovasi/${hapusId}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json',
                           'Content-Type': 'application/x-www-form-urlencoded' },
                body: '_method=DELETE',
            });
            const data = await res.json();
            if (data.success) {
                document.getElementById(`ucard-${hapusId}`)?.remove();
                toast(data.message);
            } else toast(data.message, false);
        } catch { toast('Gagal menghapus.', false); }
    };

    // ── Kirim ─────────────────────────────────────────────────────────
    let kirimId = null;
    document.addEventListener('click', e => {
        const btn = e.target.closest('.btn-kirim');
        if (!btn) return;
        kirimId = btn.dataset.id;
        mKirim.show();
    });
    document.getElementById('btnOkKirim').onclick = async () => {
        mKirim.hide();
        try {
            const res  = await fetch(`/inovasi/${kirimId}/kirim`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            });
            const data = await res.json();
            if (data.success) { toast(data.message); setTimeout(() => location.reload(), 700); }
            else toast(data.message, false);
        } catch { toast('Gagal mengirim.', false); }
    };

})();
</script>
@endpush