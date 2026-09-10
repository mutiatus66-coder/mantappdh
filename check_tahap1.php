<?php
// Diagnostic script: check Tahap 1 data
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SubEvent;
use App\Models\Penilai;
use App\Models\Usulan;
use App\Models\PenilaianUsulan;

$subEvents = SubEvent::all();
echo "=== DIAGNOSTIC: Tahap 1 Data ===\n";
echo "Total SubEvents: " . $subEvents->count() . "\n\n";

foreach ($subEvents as $se) {
    $penilaiIds = Penilai::where('sub_event_id', $se->id)->pluck('id')->toArray();
    $jumlahPenilai = count($penilaiIds);
    
    $usulans = Usulan::where('sub_event_id', $se->id)
        ->where('is_submitted', true)
        ->where('lolos_tahap1', false)
        ->get();
    
    $usulansLolos = Usulan::where('sub_event_id', $se->id)
        ->where('is_submitted', true)
        ->where('lolos_tahap1', true)
        ->count();
    
    echo "SubEvent #{$se->id}: {$se->sub_event} ({$se->tahun})\n";
    echo "  Penilai: {$jumlahPenilai} (IDs: " . implode(',', $penilaiIds) . ")\n";
    echo "  Usulan belum lolos: {$usulans->count()}\n";
    echo "  Usulan sudah lolos: {$usulansLolos}\n";
    
    foreach ($usulans->take(3) as $u) {
        $nilaiByPenilai = PenilaianUsulan::where('usulan_id', $u->id)
            ->whereIn('penilai_id', $penilaiIds)
            ->get()
            ->groupBy('penilai_id');
        
        $penilaiYangSudah = $nilaiByPenilai->keys()->toArray();
        $sudahSemua = ($jumlahPenilai > 0 && count(array_intersect($penilaiYangSudah, $penilaiIds)) >= $jumlahPenilai);
        
        echo "  Usulan #{$u->id} ({$u->inovator}): dinilai oleh " . count($nilaiByPenilai) . "/{$jumlahPenilai} penilai";
        echo " -> checkbox " . ($sudahSemua ? "ENABLED" : "DISABLED") . "\n";
    }
    echo "\n";
}
