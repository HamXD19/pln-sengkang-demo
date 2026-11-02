const fs = require('fs');
const path = require('path');
const ExcelJS = require('/home/irhmxyz/plnsengkang/node_modules/exceljs');

const users = JSON.parse(fs.readFileSync('/tmp/users_data.json', 'utf8'));

async function createWorkbook() {
  const wb = new ExcelJS.Workbook();
  wb.creator = 'devATP & PLN UP Sengkang';
  wb.created = new Date();

  // Set Sheet 1 as the default active tab
  wb.views = [
    {
      x: 0, y: 0, width: 10000, height: 20000,
      firstSheet: 0, activeTab: 0, visibility: 'visible'
    }
  ];

  const brandNavy = 'FF002B49';
  const brandSky = 'FF0284C7';
  const lightGray = 'FFF8FAFC';
  const borderGray = 'FFE2E8F0';

  // ==========================================
  // SHEET 1: DAFTAR AKUN PEGAWAI (TAB UTAMA)
  // ==========================================
  const ws = wb.addWorksheet('Daftar Akun Pegawai', {
    views: [
      {
        state: 'frozen',
        ySplit: 5,
        showGridLines: true,
        activeCell: 'B6'
      }
    ]
  });

  ws.columns = [
    { key: 'no', width: 6 },
    { key: 'nid', width: 18 },
    { key: 'password', width: 18 },
    { key: 'nama', width: 34 },
    { key: 'role', width: 16 },
    { key: 'jabatan', width: 36 },
    { key: 'bidang', width: 30 },
    { key: 'seksi', width: 32 },
    { key: 'email', width: 32 },
    { key: 'status', width: 14 }
  ];

  // Title Banner
  ws.mergeCells('A1:J1');
  const t1 = ws.getCell('A1');
  t1.value = 'PT PLN NUSANTARA POWER — UNIT PELAKSANA PENGENDALIAN PEMBANGKITAN SENGKANG';
  t1.font = { name: 'Arial', size: 14, bold: true, color: { argb: 'FFFFFFFF' } };
  t1.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: brandNavy } };
  t1.alignment = { horizontal: 'center', vertical: 'middle' };
  ws.getRow(1).height = 30;

  ws.mergeCells('A2:J2');
  const t2 = ws.getCell('A2');
  t2.value = 'DIREKTORI RESMI KREDENSIAL AKUN LOGIN PEGAWAI & SUPER ADMINISTRATOR';
  t2.font = { name: 'Arial', size: 11, bold: true, color: { argb: 'FFFFFFFF' } };
  t2.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: brandSky } };
  t2.alignment = { horizontal: 'center', vertical: 'middle' };
  ws.getRow(2).height = 22;

  // Panduan Kredensial Bar
  ws.mergeCells('A3:J3');
  const t3 = ws.getCell('A3');
  t3.value = `Login ke sistem menggunakan NID (Username) dengan Kata Sandi Default: 123. Total Akun Aktif: ${users.length} Akun (Termasuk Super Admin)`;
  t3.font = { name: 'Arial', size: 9, bold: true, italic: true, color: { argb: 'FF0F172A' } };
  t3.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFE2E8F0' } };
  t3.alignment = { horizontal: 'center', vertical: 'middle' };
  ws.getRow(3).height = 20;

  // Spacing
  ws.getRow(4).height = 8;

  // Header Kolom Tabel (Baris 5)
  const headerRow = ws.getRow(5);
  headerRow.values = [
    'NO',
    'NID (LOGIN)',
    'PASSWORD',
    'NAMA LENGKAP',
    'ROLE BSD',
    'JABATAN STRUKTURAL',
    'BIDANG UTAMA',
    'UNIT KERJA / SEKSI',
    'EMAIL KORPORAT',
    'STATUS'
  ];
  headerRow.height = 26;
  headerRow.eachCell((cell) => {
    cell.font = { name: 'Arial', size: 9, bold: true, color: { argb: 'FFFFFFFF' } };
    cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: brandNavy } };
    cell.alignment = { horizontal: 'center', vertical: 'middle', wrapText: true };
    cell.border = {
      top: { style: 'medium', color: { argb: 'FF0F172A' } },
      bottom: { style: 'medium', color: { argb: 'FF0F172A' } },
      left: { style: 'thin', color: { argb: 'FF334155' } },
      right: { style: 'thin', color: { argb: 'FF334155' } }
    };
  });

  // Data Rows
  users.forEach((u, idx) => {
    const rowNum = 6 + idx;
    const row = ws.getRow(rowNum);
    row.height = 22;

    row.values = [
      idx + 1,
      u.nid || '-',
      '123',
      u.nama,
      u.role,
      u.jabatan || '-',
      u.bidang || 'UP Sengkang',
      u.seksi || 'UP Sengkang',
      u.email || '-',
      u.status || 'Aktif'
    ];

    const isEven = idx % 2 === 0;
    const bgRowColor = isEven ? 'FFFFFFFF' : lightGray;

    row.eachCell((cell, colNumber) => {
      cell.font = { name: 'Arial', size: 9, color: { argb: 'FF1E293B' } };
      cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: bgRowColor } };
      cell.border = {
        top: { style: 'thin', color: { argb: borderGray } },
        bottom: { style: 'thin', color: { argb: borderGray } },
        left: { style: 'thin', color: { argb: borderGray } },
        right: { style: 'thin', color: { argb: borderGray } }
      };

      // Alignment per col
      if (colNumber === 1 || colNumber === 10) {
        cell.alignment = { horizontal: 'center', vertical: 'middle' };
      } else if (colNumber === 2 || colNumber === 3) {
        cell.alignment = { horizontal: 'center', vertical: 'middle' };
        cell.font = { name: 'Consolas', size: 9, bold: true, color: { argb: 'FF0F172A' } };
      } else if (colNumber === 5) {
        cell.alignment = { horizontal: 'center', vertical: 'middle' };
        if (u.role === 'SUPER_ADMIN') {
          cell.font = { name: 'Arial', size: 9, bold: true, color: { argb: 'FFE11D48' } };
          cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFFFE4E6' } };
        } else if (u.role === 'MUP') {
          cell.font = { name: 'Arial', size: 9, bold: true, color: { argb: 'FF7C3AED' } };
        } else if (u.role === 'ASMAN') {
          cell.font = { name: 'Arial', size: 9, bold: true, color: { argb: 'FF2563EB' } };
        } else if (u.role === 'TL') {
          cell.font = { name: 'Arial', size: 9, bold: true, color: { argb: 'FF0D9488' } };
        }
      } else {
        cell.alignment = { horizontal: 'left', vertical: 'middle' };
      }

      if (colNumber === 4) {
        cell.font = { name: 'Arial', size: 9, bold: true, color: { argb: 'FF0F172A' } };
      }
    });
  });

  // Write files
  const destPaths = [
    '/home/irhmxyz/plnsengkang/DAFTAR_AKUN_PEGAWAI_PLN_SENGKANG.xlsx',
    '/home/irhmxyz/plnsengkang/public/downloads/DAFTAR_AKUN_PEGAWAI_PLN_SENGKANG.xlsx',
    '/home/irhmxyz/Documents/bahan PLN sengkang/DAFTAR_AKUN_PEGAWAI_PLN_SENGKANG.xlsx'
  ];

  for (const p of destPaths) {
    try {
      const dir = path.dirname(p);
      if (!fs.existsSync(dir)) {
        fs.mkdirSync(dir, { recursive: true });
      }
      await wb.xlsx.writeFile(p);
      console.log(`Saved successfully to: ${p}`);
    } catch (err) {
      console.error(`Failed to save to ${p}:`, err.message);
    }
  }

  console.log('ALL EXCEL FILES GENERATED SUCCESSFULLY!');
}

createWorkbook().catch(err => {
  console.error(err);
  process.exit(1);
});
