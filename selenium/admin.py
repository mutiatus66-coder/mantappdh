import os
import sys
import subprocess

current_dir = os.path.dirname(os.path.abspath(__file__))
python_bin = sys.executable

tests = [
    'Test1_MasterTest.py',
    'Test2_IndikatorTest.py',
    'Test3_InovasiTest.py',
    'Test4_PenilaianTest.py'
]

print("Mulai menjalankan seluruh bagian E2E Admin (Selenium Python)...")

for test in tests:
    test_path = os.path.join(current_dir, 'Admin', test)
    print(f"\n▶️  Menjalankan: {test}...")
    res = subprocess.run([python_bin, test_path])
    if res.returncode != 0:
        print(f"\n❌ Pengujian {test} GAGAL! Menghentikan eksekusi selanjutnya.")
        sys.exit(1)

print("✅ Workflow Seluruh Bagian E2E Admin (Selenium Python) Selesai dengan Sukses!")
