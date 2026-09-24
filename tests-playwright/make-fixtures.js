// tests-playwright/make-fixtures.js
// Jalankan sekali: node tests-playwright/make-fixtures.js
// Fungsi: generate file fixture PDF & PNG dummy untuk test upload
//
// CATATAN: File ini pakai ES Module (import) karena package.json
// project ini punya "type": "module"

import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

// Karena ESM tidak punya __dirname otomatis, kita bikin sendiri
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const FIXTURES_DIR = path.join(__dirname, 'fixtures');
if (!fs.existsSync(FIXTURES_DIR)) fs.mkdirSync(FIXTURES_DIR, { recursive: true });

// ── PDF minimal (1 halaman, teks "Dummy PDF") ──
const PDF_CONTENT = `%PDF-1.4
1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj
2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj
3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 612 792]/Contents 4 0 R/Resources<</Font<</F1 5 0 R>>>>>>endobj
4 0 obj<</Length 60>>stream
BT /F1 24 Tf 100 700 Td (Dummy PDF - Test Fixture) Tj ET
endstream
endobj
5 0 obj<</Type/Font/Subtype/Type1/BaseFont/Helvetica>>endobj
xref
0 6
0000000000 65535 f
0000000009 00000 n
0000000052 00000 n
0000000101 00000 n
0000000210 00000 n
0000000319 00000 n
trailer<</Size 6/Root 1 0 R>>
startxref
394
%%EOF`;

fs.writeFileSync(path.join(FIXTURES_DIR, 'surat-pernyataan.pdf'), PDF_CONTENT);
fs.writeFileSync(path.join(FIXTURES_DIR, 'proposal.pdf'), PDF_CONTENT);

// ── PNG 1x1 pixel transparan (PNG minimal valid) ──
const PNG_BASE64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';
fs.writeFileSync(
  path.join(FIXTURES_DIR, 'foto-inovasi.png'),
  Buffer.from(PNG_BASE64, 'base64')
);

console.log('✓ Fixtures created:');
console.log('  -', path.join(FIXTURES_DIR, 'surat-pernyataan.pdf'));
console.log('  -', path.join(FIXTURES_DIR, 'proposal.pdf'));
console.log('  -', path.join(FIXTURES_DIR, 'foto-inovasi.png'));
