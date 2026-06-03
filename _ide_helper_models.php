<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $sub_event_id
 * @property string $nama
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\SubEvent $subEvent
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bidang newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bidang newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bidang query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bidang whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bidang whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bidang whereNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bidang whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bidang whereSubEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bidang whereUpdatedAt($value)
 */
	class Bidang extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nama_event
 * @property string $jenis
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SubEvent> $subEvents
 * @property-read int|null $sub_events_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereJenis($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereNamaEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereUpdatedAt($value)
 */
	class Event extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $sub_event_id
 * @property int $nilai_makalah
 * @property int $nilai_substansi
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\SubEvent $subEvent
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormulasiTahap1 newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormulasiTahap1 newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormulasiTahap1 query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormulasiTahap1 whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormulasiTahap1 whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormulasiTahap1 whereNilaiMakalah($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormulasiTahap1 whereNilaiSubstansi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormulasiTahap1 whereSubEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormulasiTahap1 whereUpdatedAt($value)
 */
	class FormulasiTahap1 extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $sub_event_id
 * @property int $nilai_inovasi
 * @property int $nilai_peragaan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\SubEvent $subEvent
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormulasiTahap2 newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormulasiTahap2 newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormulasiTahap2 query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormulasiTahap2 whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormulasiTahap2 whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormulasiTahap2 whereNilaiInovasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormulasiTahap2 whereNilaiPeragaan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormulasiTahap2 whereSubEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormulasiTahap2 whereUpdatedAt($value)
 */
	class FormulasiTahap2 extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $sub_event_id
 * @property string $nama_indikator
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\SubEvent $subEvent
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Indikator newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Indikator newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Indikator query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Indikator whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Indikator whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Indikator whereNamaIndikator($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Indikator whereSubEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Indikator whereUpdatedAt($value)
 */
	class Indikator extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $sub_event_id
 * @property string $nama_indikator
 * @property string $jenis
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\KeteranganTahap2> $keterangans
 * @property-read int|null $keterangans_count
 * @property-read \App\Models\SubEvent $subEvent
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IndikatorTahap2 newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IndikatorTahap2 newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IndikatorTahap2 query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IndikatorTahap2 whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IndikatorTahap2 whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IndikatorTahap2 whereJenis($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IndikatorTahap2 whereNamaIndikator($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IndikatorTahap2 whereSubEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IndikatorTahap2 whereUpdatedAt($value)
 */
	class IndikatorTahap2 extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $indikator_id
 * @property string $keterangan
 * @property int $nilai_minimal
 * @property int $nilai_maksimal
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Indikator $indikator
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeteranganIndikator newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeteranganIndikator newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeteranganIndikator query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeteranganIndikator whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeteranganIndikator whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeteranganIndikator whereIndikatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeteranganIndikator whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeteranganIndikator whereNilaiMaksimal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeteranganIndikator whereNilaiMinimal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeteranganIndikator whereUpdatedAt($value)
 */
	class KeteranganIndikator extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $indikator_tahap2_id
 * @property string $keterangan
 * @property int $nilai_minimal
 * @property int $nilai_maksimal
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\IndikatorTahap2 $indikator
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeteranganTahap2 newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeteranganTahap2 newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeteranganTahap2 query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeteranganTahap2 whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeteranganTahap2 whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeteranganTahap2 whereIndikatorTahap2Id($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeteranganTahap2 whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeteranganTahap2 whereNilaiMaksimal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeteranganTahap2 whereNilaiMinimal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeteranganTahap2 whereUpdatedAt($value)
 */
	class KeteranganTahap2 extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $judul
 * @property string|null $deskripsi
 * @property string $status
 * @property string|null $file_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengumuman newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengumuman newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengumuman query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengumuman whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengumuman whereDeskripsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengumuman whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengumuman whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengumuman whereJudul($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengumuman whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengumuman whereUpdatedAt($value)
 */
	class Pengumuman extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $event_id
 * @property int $tahun
 * @property string $sub_event
 * @property string|null $kategori
 * @property \Illuminate\Support\Carbon $mulai
 * @property \Illuminate\Support\Carbon $berakhir
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Bidang> $bidangs
 * @property-read int|null $bidangs_count
 * @property-read \App\Models\Event $event
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubEvent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubEvent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubEvent query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubEvent whereBerakhir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubEvent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubEvent whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubEvent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubEvent whereKategori($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubEvent whereMulai($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubEvent whereSubEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubEvent whereTahun($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubEvent whereUpdatedAt($value)
 */
	class SubEvent extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property int|null $current_team_id
 * @property string|null $profile_photo_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $nama
 * @property string $hak_akses
 * @property string $status
 * @property string $role
 * @property int $is_active
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCurrentTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereHakAkses($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfilePhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

