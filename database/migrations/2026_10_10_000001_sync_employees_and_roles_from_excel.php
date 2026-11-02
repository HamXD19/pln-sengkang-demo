<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\User;
use App\Models\Role;
use App\Models\Position;
use App\Models\OrganizationUnit;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        $unitMapping = [
            "HAR MESIN" => 22,
            "HAR LISTRIK" => 20,
            "HAR KONIN" => 21,
            "SDM & UMUM" => 28,
            "INVENTORY & GUDANG" => 24,
            "LOGISTIK & PENGADAAN" => 30,
            "K3 & KEAMANAN" => 4,
            "PRODUKSI BLOK 2 B" => 16,
            "LINGKUNGAN" => 5,
            "MMRK" => 27,
            "RENDAL OPERASI" => 9,
            "KIMIA" => 10,
            "PRODUKSI BLOK 1 A" => 11,
            "PRODUKSI BLOK 1 B" => 12,
            "PRODUKSI BLOK 1 C" => 13,
            "PRODUKSI BLOK 1 D" => 14,
            "PRODUKSI BLOK 2 A" => 15,
            "PRODUKSI BLOK 2 D" => 18,
            "RENDAL PEMELIHARAAN" => 19,
            "OUTAGE MANAGEMENT" => 23,
            "KEUANGAN" => 31,
            "SO & CA" => 25,
            "CBM" => 26,
            "PRODUKSI BLOK 2 C" => 17,
            "ADMINISTRASI" => 8,
            "MUP SENGKANG" => 1,
            "ENGINEERING" => 7,
            "OPERASI" => 2,
            "PEMELIHARAAN" => 6,
            "SEKERTARIS" => 29,
        ];

        $employees = json_decode('[{"no": "1", "nama": "MOH. FURQON AKHSANI", "nid": "8108050JA", "jabatan": "MANAGER UNIT", "bidang": "MUP SENGKANG"}, {"no": "2", "nama": "YOGA KURNIAWAN", "nid": "9315231TA", "jabatan": "TEAM LEADER K3 DAN KEAMANAN", "bidang": "K3 & KEAMANAN"}, {"no": "3", "nama": "IRIANTI APRILLA", "nid": "9623022WGK", "jabatan": "JUNIOR K3 & KEAMANAN", "bidang": "K3 & KEAMANAN"}, {"no": "4", "nama": "SYAFRUDDIN", "nid": "8023020WGK", "jabatan": "TENAGA PENDUKUNG K3 & KEAMANAN", "bidang": "K3 & KEAMANAN"}, {"no": "5", "nama": "BASO RIDWAN", "nid": "8424056WGK", "jabatan": "TENAGA PENDUKUNG K3 & KEAMANAN", "bidang": "K3 & KEAMANAN"}, {"no": "6", "nama": "MUH. AL AQSHAL KHIDAYATULLAH S.", "nid": "0525096WGK", "jabatan": "TENAGA PENDUKUNG K3 & KEAMANAN", "bidang": "K3 & KEAMANAN"}, {"no": "7", "nama": "BASO MUHAMMAD AGUNG ANUGERAH", "nid": "9924076WGK", "jabatan": "JUNIOR K3 & KEAMANAN", "bidang": "K3 & KEAMANAN"}, {"no": "8", "nama": "H. ABDUL MALIK", "nid": "7223005PKW", "jabatan": "TEAM LEADER LINGKUNGAN", "bidang": "LINGKUNGAN"}, {"no": "9", "nama": "MUH YUSUF", "nid": "8123003WGK", "jabatan": "TENAGA PENDUKUNG LINGKUNGAN", "bidang": "LINGKUNGAN"}, {"no": "10", "nama": "NURUL FAHMIAH", "nid": "9624065WGK", "jabatan": "JUNIOR OFFICER LINGKUNGAN", "bidang": "LINGKUNGAN"}, {"no": "11", "nama": "BASO AWALUDDIN", "nid": "9825097WGK", "jabatan": "TENAGA PENDUKUNG LINGKUNGAN", "bidang": "LINGKUNGAN"}, {"no": "12", "nama": "ULVANI JULFIRA", "nid": "9825092WGK", "jabatan": "TENAGA PENDUKUNG LINGKUNGAN", "bidang": "LINGKUNGAN"}, {"no": "13", "nama": "FUAD ANWAR", "nid": "8609070JA", "jabatan": "ASMAN ENGINEERING & QA", "bidang": "ENGINEERING"}, {"no": "14", "nama": "HARI AKBAR SYUAIB", "nid": "9417078BL", "jabatan": "TEAM LEADER SYSTEM OWNER & COMPONENT ANALYST", "bidang": "SO & CA"}, {"no": "15", "nama": "M TAUFIK HASRIANSYAH ASIARI", "nid": "9017082BL", "jabatan": "TECHNICIAN SYSTEM OWNER", "bidang": "SO & CA"}, {"no": "16", "nama": "ANDI IMRAN MUSTIKA", "nid": "8423028PKW", "jabatan": "TECHNICIAN COMPONENT ANALYST", "bidang": "SO & CA"}, {"no": "17", "nama": "IRFAN", "nid": "9824059WGK", "jabatan": "TENAGA PENDUKUNG SYSTEM OWNER & COMPONENT ANALYST", "bidang": "SO & CA"}, {"no": "18", "nama": "ARMAN ARIFIN", "nid": "8312589KP", "jabatan": "TEAM LEADER CONDITION BASED MAINTENANCE", "bidang": "CBM"}, {"no": "19", "nama": "DAUD MALOMO", "nid": "9217026AM", "jabatan": "TECHNICIAN CONDITION BASED MAINTENANCE", "bidang": "CBM"}, {"no": "20", "nama": "NUARDI", "nid": "9923039WGK", "jabatan": "JUNIOR TECHNICIAN CONDITION BASED MAINTENANCE", "bidang": "CBM"}, {"no": "21", "nama": "ABDUL RAHIM", "nid": "8424086WGK", "jabatan": "TENAGA PENDUKUNG TEKNIK CONDITION BASED MAINTENANCE", "bidang": "CBM"}, {"no": "22", "nama": "AFFANDY HERMAWAN", "nid": "7423008PKW", "jabatan": "TEAM LEADER MANAJEMEN MUTU, RISIKO & KINERJA", "bidang": "MMRK"}, {"no": "23", "nama": "H. NASRUL MARZUKI", "nid": "7123016PKW", "jabatan": "OFFICER MANAJEMEN MUTU, RISIKO & KEPATUHAN", "bidang": "MMRK"}, {"no": "24", "nama": "TIARA SUHARYATI", "nid": "9523023WGK", "jabatan": "JUNIOR OFFICER MANAJEMEN MUTU, RISIKO & KINERJA", "bidang": "MMRK"}, {"no": "25", "nama": "HERI ANUGRAH PRATAMA", "nid": "9924077WGK", "jabatan": "JUNIOR OFFICER MANAJEMEN MUTU, RISIKO & KINERJA", "bidang": "MMRK"}, {"no": "26", "nama": "WAHYU HIDAYAT", "nid": "9424069WGK", "jabatan": "TENAGA PENDUKUNG MANAJEMEN MUTU, RISIKO & KINERJA", "bidang": "MMRK"}, {"no": "27", "nama": "H. MUHAMMAD ISMAIL", "nid": "7323002PKW", "jabatan": "ASMAN OPERASI", "bidang": "OPERASI"}, {"no": "28", "nama": "SINUNG RAHARJO", "nid": "8812558PC", "jabatan": "TEAM LEADER RENDAL OPERASI", "bidang": "RENDAL OPERASI"}, {"no": "29", "nama": "MUH. ASPAR ARIFIN", "nid": "9115083ID", "jabatan": "OFFICER RENDAL OPERASI", "bidang": "RENDAL OPERASI"}, {"no": "30", "nama": "RIYAN ADRIANSYAH AMRI", "nid": "9724066WGK", "jabatan": "JUNIOR OFFICER PERENCANAAN & PENGENDALIAN OPERASI", "bidang": "RENDAL OPERASI"}, {"no": "31", "nama": "ALGAZALI", "nid": "0424049WGK", "jabatan": "TENAGA PENDUKUNG PERENCANAAN & PENGENDALIAN OPERASI", "bidang": "RENDAL OPERASI"}, {"no": "32", "nama": "BASO SYAIFUL RUSKY", "nid": "0026101WGK", "jabatan": "TENAGA PENDUKUNG PERENCANAAN & PENGENDALIAN OPERASI", "bidang": "RENDAL OPERASI"}, {"no": "33", "nama": "ANDI ANANDA MAGFIRAH", "nid": "9923028WGK", "jabatan": "JUNIOR OFFICER KIMIA", "bidang": "KIMIA"}, {"no": "34", "nama": "BISMI RATU FORTUNA N", "nid": "9925088WGK", "jabatan": "TENAGA PENDUKUNG KIMIA", "bidang": "KIMIA"}, {"no": "35", "nama": "SITTI MUNAWARAH SYAM", "nid": "0124068WGK", "jabatan": "TENAGA PENDUKUNG KIMIA", "bidang": "KIMIA"}, {"no": "36", "nama": "MUHAMMAD ABDIL MUKMIN SYAH", "nid": "0324064WGK", "jabatan": "TENAGA PENDUKUNG KIMIA", "bidang": "KIMIA"}, {"no": "37", "nama": "ISMAIL", "nid": "7823006WGK", "jabatan": "TENAGA PENDUKUNG KIMIA", "bidang": "KIMIA"}, {"no": "38", "nama": "MURSALAM", "nid": "8915056TD", "jabatan": "TEAM LEADER PRODUKSI BLOK 1 A", "bidang": "PRODUKSI BLOK 1 A"}, {"no": "39", "nama": "ABDUL MAJID IBRAHIM", "nid": "8726102WGK", "jabatan": "JUNIOR TECHNICIAN PRODUKSI BLOK-1 A", "bidang": "PRODUKSI BLOK 1 A"}, {"no": "40", "nama": "RUDI", "nid": "9324067WGK", "jabatan": "TENAGA PENDUKUNG TEKNIK PRODUKSI BLOK 1 A", "bidang": "PRODUKSI BLOK 1 A"}, {"no": "41", "nama": "ADRIAN", "nid": "9723011WGK", "jabatan": "JUNIOR TECHNICIAN PRODUKSI BLOK-1 A", "bidang": "PRODUKSI BLOK 1 A"}, {"no": "42", "nama": "MUSRIYADI", "nid": "8817083BL", "jabatan": "TECHNICIAN CCR, DCS & ELEKTRIKAL BLOK-1 A", "bidang": "PRODUKSI BLOK 1 A"}, {"no": "43", "nama": "MEDY IVANTRY TANDI BUA", "nid": "8917074BL", "jabatan": "TEAM LEADER PRODUKSI BLOK 1 B", "bidang": "PRODUKSI BLOK 1 B"}, {"no": "44", "nama": "AHMAD BAYU PERMADI", "nid": "8515058TD", "jabatan": "TECHNICIAN CCR, DCS & ELEKTRIKAL BLOK-1 B", "bidang": "PRODUKSI BLOK 1 B"}, {"no": "45", "nama": "ONESIMUS", "nid": "9117084BL", "jabatan": "TECHNICIAN CCR, DCS & ELEKTRIKAL BLOK-1 B", "bidang": "PRODUKSI BLOK 1 B"}, {"no": "46", "nama": "MUH. ASHAR", "nid": "9923008WGK", "jabatan": "TENAGA PENDUKUNG TEKNIK PRODUKSI BLOK 1 B", "bidang": "PRODUKSI BLOK 1 B"}, {"no": "47", "nama": "MOCH. ALIF AFDILAH SALDI", "nid": "9824079WGK", "jabatan": "JUNIOR TECHNICIAN PRODUKSI BLOK-1 B", "bidang": "PRODUKSI BLOK 1 B"}, {"no": "48", "nama": "H.HERMAN JAYA.ST", "nid": "7423003PKW", "jabatan": "TEAM LEADER PRODUKSI BLOK 1 C", "bidang": "PRODUKSI BLOK 1 C"}, {"no": "49", "nama": "BERFRY", "nid": "7323020PKW", "jabatan": "TECHNICIAN CCR, DCS & ELEKTRIKAL BLOK-1 C", "bidang": "PRODUKSI BLOK 1 C"}, {"no": "50", "nama": "AYU", "nid": "9723031WGK", "jabatan": "TENAGA PENDUKUNG TEKNIK PRODUKSI BLOK 1 C", "bidang": "PRODUKSI BLOK 1 C"}, {"no": "51", "nama": "ANDI DEDI PRABOWO", "nid": "9723015WGK", "jabatan": "JUNIOR TECHNICIAN PRODUKSI BLOK 1 C", "bidang": "PRODUKSI BLOK 1 C"}, {"no": "52", "nama": "FIRMAN", "nid": "0224071WGK", "jabatan": "JUNIOR TECHNICIAN PRODUKSI BLOK 1 C", "bidang": "PRODUKSI BLOK 1 C"}, {"no": "53", "nama": "MUH NASAR D", "nid": "7223009PKW", "jabatan": "TEAM LEADER PRODUKSI BLOK 1 D", "bidang": "PRODUKSI BLOK 1 D"}, {"no": "54", "nama": "ASRIANTO", "nid": "9023025PKW", "jabatan": "TECHNICIAN CCR, DCS & ELEKTRIKAL BLOK-1 D", "bidang": "PRODUKSI BLOK 1 D"}, {"no": "55", "nama": "RUSDI ALFRIYANTO RIWALDI RAHMA", "nid": "8723023PKW", "jabatan": "TECHNICIAN CCR, DCS & ELEKTRIKAL BLOK-1 D", "bidang": "PRODUKSI BLOK 1 D"}, {"no": "56", "nama": "ANDI ALIEM ADRIAN ASRUL", "nid": "0023027WGK", "jabatan": "JUNIOR TECHNICIAN PRODUKSI BLOK 1 D", "bidang": "PRODUKSI BLOK 1 D"}, {"no": "57", "nama": "MUH. SOFYAN", "nid": "9924063WGK", "jabatan": "TENAGA PENDUKUNG TEKNIK PRODUKSI BLOK1 D", "bidang": "PRODUKSI BLOK 1 D"}, {"no": "58", "nama": "GIDION", "nid": "9215235ZJY", "jabatan": "PJS TEAM LEADER PRODUKSI BLOK-2 A", "bidang": "PRODUKSI BLOK 2 A"}, {"no": "59", "nama": "MUH. TAKDIR T", "nid": "9023039PKW", "jabatan": "TECHNICIAN CCR, DCS & ELEKTRIKAL BLOK-2 A", "bidang": "PRODUKSI BLOK 2 A"}, {"no": "60", "nama": "BAHARUDDIN", "nid": "8915057TD", "jabatan": "TECHNICIAN CCR, DCS & ELEKTRIKAL BLOK-2 A", "bidang": "PRODUKSI BLOK 2 A"}, {"no": "61", "nama": "MUHAMMAD FAJAR", "nid": "0125098WGK", "jabatan": "JUNIOR TECHNICIAN PRODUKSI BLOK 2 A", "bidang": "PRODUKSI BLOK 2 A"}, {"no": "62", "nama": "MUH. AKBAR", "nid": "0423036WGK", "jabatan": "TENAGA PENDUKUNG TEKNIK PRODUKSI BLOK 2 A", "bidang": "PRODUKSI BLOK 2 A"}, {"no": "63", "nama": "NADAR", "nid": "8223015PKW", "jabatan": "TEAM LEADER PRODUKSI BLOK 2 B", "bidang": "PRODUKSI BLOK 2 B"}, {"no": "64", "nama": "ARIFUDDIN NURDIN", "nid": "8715065KD", "jabatan": "TECHNICIAN CCR, DCS & ELEKTRIKAL BLOK-2 B", "bidang": "PRODUKSI BLOK 2 B"}, {"no": "65", "nama": "SURYA DARMAN SAPUTRA", "nid": "8923031PKW", "jabatan": "TECHNICIAN CCR, DCS & ELEKTRIKAL BLOK-2 B", "bidang": "PRODUKSI BLOK 2 B"}, {"no": "66", "nama": "MUH. HIDAYAT ARAS", "nid": "9924081WGK", "jabatan": "JUNIOR TECHNICIAN PRODUKSI BLOK 2 B", "bidang": "PRODUKSI BLOK 2 B"}, {"no": "67", "nama": "RIAHASTAMAN", "nid": "9323041WGK", "jabatan": "TENAGA PENDUKUNG TEKNIK PRODUKSI BLOK 2 B", "bidang": "PRODUKSI BLOK 2 B"}, {"no": "68", "nama": "ALIF IQBAL MAULANA", "nid": "0225089WGK", "jabatan": "TENAGA PENDUKUNG TEKNIK PRODUKSI BLOK 2 B", "bidang": "PRODUKSI BLOK 2 B"}, {"no": "69", "nama": "AGUSNAWAN", "nid": "8323018PKW", "jabatan": "TEAM LEADER PRODUKSI BLOK 2 C", "bidang": "PRODUKSI BLOK 2 C"}, {"no": "70", "nama": "ADY GUNA", "nid": "8423029PKW", "jabatan": "TECHNICIAN CCR, DCS & ELEKTRIKAL BLOK-2 C", "bidang": "PRODUKSI BLOK 2 C"}, {"no": "71", "nama": "NURJUMAEDI", "nid": "9123041PKW", "jabatan": "TECHNICIAN CCR, DCS & ELEKTRIKAL BLOK-2 C", "bidang": "PRODUKSI BLOK 2 C"}, {"no": "72", "nama": "MUKMIN", "nid": "9723038WGK", "jabatan": "JUNIOR TECHNICIAN PRODUKSI BLOK 2 C", "bidang": "PRODUKSI BLOK 2 C"}, {"no": "73", "nama": "SUNARDI SM", "nid": "8623017PKW", "jabatan": "TEAM LEADER PRODUKSI BLOK 2 D", "bidang": "PRODUKSI BLOK 2 D"}, {"no": "74", "nama": "ABD MUSAWWIR AMFAR", "nid": "8223033PKW", "jabatan": "TECHNICIAN CCR, DCS & ELEKTRIKAL BLOK-2 D", "bidang": "PRODUKSI BLOK 2 D"}, {"no": "75", "nama": "MOH FIQRY", "nid": "8223032PKW", "jabatan": "TECHNICIAN CCR, DCS & ELEKTRIKAL BLOK-2 D", "bidang": "PRODUKSI BLOK 2 D"}, {"no": "76", "nama": "ANUGRAH", "nid": "9924075WGK", "jabatan": "JUNIOR TECHNICIAN PRODUKSI BLOK 2 D", "bidang": "PRODUKSI BLOK 2 D"}, {"no": "77", "nama": "SUPRIADI", "nid": "9223044WGK", "jabatan": "TENAGA PENDUKUNG TEKNIK PRODUKSI BLOK 2 D", "bidang": "PRODUKSI BLOK 2 D"}, {"no": "78", "nama": "CAHYA PATRIA CITRA TARUNA", "nid": "8208036JA", "jabatan": "ASMAN PEMELIHARAAN", "bidang": "PEMELIHARAAN"}, {"no": "79", "nama": "YERSON", "nid": "9015144ZJY", "jabatan": "TEAM LEADER RENDAL PEMELIHARAAN", "bidang": "RENDAL PEMELIHARAAN"}, {"no": "80", "nama": "AZWAR RASYID", "nid": "9317123KD", "jabatan": "OFFICER RENDAL PEMELIHARAAN", "bidang": "RENDAL PEMELIHARAAN"}, {"no": "81", "nama": "FILLIA", "nid": "0023021WGK", "jabatan": "JUNIOR OFFICER RENDAL PEMELIHARAAN", "bidang": "RENDAL PEMELIHARAAN"}, {"no": "82", "nama": "NAFTALIA SAMPE RANDAN", "nid": "0023026WGK", "jabatan": "JUNIOR OFFICER RENDAL PEMELIHARAAN", "bidang": "RENDAL PEMELIHARAAN"}, {"no": "83", "nama": "AHMAD RESKY", "nid": "0424048WGK", "jabatan": "TENAGA PENDUKUNG TEKNIK PERENCANAAN & PENGENDALIAN PEMELIHARAAN", "bidang": "RENDAL PEMELIHARAAN"}, {"no": "84", "nama": "SUHAIMI", "nid": "9115227KP", "jabatan": "TEAM LEADER PEMELIHARAAN LISTRIK", "bidang": "HAR LISTRIK"}, {"no": "85", "nama": "SUPRIYADI LAMMA", "nid": "7323030PKW", "jabatan": "JUNIOR TECHNICIAN PEMELIHARAAN LISTRIK", "bidang": "HAR LISTRIK"}, {"no": "86", "nama": "USMAN", "nid": "8023024PKW", "jabatan": "JUNIOR TECHNICIAN PEMELIHARAAN LISTRIK", "bidang": "HAR LISTRIK"}, {"no": "87", "nama": "WAHYUDDIN", "nid": "8023017WGK", "jabatan": "TENAGA PENDUKUNG TEKNIK TECHNICIAN PEMELIHARAAN LISTRIK", "bidang": "HAR LISTRIK"}, {"no": "88", "nama": "ABDI RAHABUDDIN", "nid": "0124072WGK", "jabatan": "JUNIOR TECHNICIAN PEMELIHARAAN LISTRIK", "bidang": "HAR LISTRIK"}, {"no": "89", "nama": "ADITYA WIRATAMA PRAYOGA", "nid": "0124084WGK", "jabatan": "JUNIOR TECHNICIAN PEMELIHARAAN LISTRIK", "bidang": "HAR LISTRIK"}, {"no": "90", "nama": "SUTRISNO.BP, S.T", "nid": "0124083WGK", "jabatan": "JUNIOR TECHNICIAN PEMELIHARAAN LISTRIK", "bidang": "HAR LISTRIK"}, {"no": "91", "nama": "ANDI WAHYUDI WIJAYANTO", "nid": "9724074WGK", "jabatan": "JUNIOR TECHNICIAN PEMELIHARAAN LISTRIK", "bidang": "HAR LISTRIK"}, {"no": "92", "nama": "AGUS SALIM", "nid": "9625094WGK", "jabatan": "TENAGA PENDUKUNG TEKNIK TECHNICIAN PEMELIHARAAN LISTRIK", "bidang": "HAR LISTRIK"}, {"no": "93", "nama": "IRFANDI", "nid": "9424060WGK", "jabatan": "TENAGA PENDUKUNG TEKNIK TECHNICIAN PEMELIHARAAN LISTRIK", "bidang": "HAR LISTRIK"}, {"no": "94", "nama": "IRWANSYAH", "nid": "9723034WGK", "jabatan": "TENAGA PENDUKUNG TEKNIK TECHNICIAN PEMELIHARAAN LISTRIK", "bidang": "HAR LISTRIK"}, {"no": "95", "nama": "HENDRA GULANG", "nid": "8323007PKW", "jabatan": "TEAM LEADER PEMELIHARAAN KONIN", "bidang": "HAR KONIN"}, {"no": "96", "nama": "DWI SUPAJAR MUCHTAR SIAJENG", "nid": "8723040PKW", "jabatan": "TECHNICIAN PEMELIHARAAN KONTROL & INSTRUMEN", "bidang": "HAR KONIN"}, {"no": "97", "nama": "SATRIAWAN", "nid": "9123038PKW", "jabatan": "JUNIOR TECHNICIAN PEMELIHARAAN KONTROL & INSTRUMEN", "bidang": "HAR KONIN"}, {"no": "98", "nama": "FHARIS IRHAM MULLIAH", "nid": "9923033WGK", "jabatan": "JUNIOR TECHNICIAN PEMELIHARAAN KONTROL & INSTRUMEN", "bidang": "HAR KONIN"}, {"no": "99", "nama": "KAHARDIMAN", "nid": "9023035WGK", "jabatan": "TENAGA PENDUKUNG TEKNIK PEMELIHARAAN KONTROL & INSTRUMEN", "bidang": "HAR KONIN"}, {"no": "100", "nama": "AHMAD REZKY NASRUDDIN", "nid": "0025099WGK", "jabatan": "JUNIOR TECHNICIAN PEMELIHARAAN KONTROL & INSTRUMEN", "bidang": "HAR KONIN"}, {"no": "101", "nama": "ANDI AGIL TASLIM", "nid": "0426104WGK", "jabatan": "JUNIOR TECHNICIAN PEMELIHARAAN KONTROL & INSTRUMEN", "bidang": "HAR KONIN"}, {"no": "102", "nama": "SAHRUL KHAN", "nid": "9923043WGK", "jabatan": "TENAGA PENDUKUNG TEKNIK PEMELIHARAAN KONTROL & INSTRUMEN", "bidang": "HAR KONIN"}, {"no": "103", "nama": "JUMARDI", "nid": "8524061WGK", "jabatan": "TENAGA PENDUKUNG TEKNIK PEMELIHARAAN KONTROL & INSTRUMEN", "bidang": "HAR KONIN"}, {"no": "105", "nama": "SAINAL TAJANG", "nid": "7823006PKW", "jabatan": "TEAM LEADER PEMELIHARAAN MESIN & SIPIL", "bidang": "HAR MESIN"}, {"no": "106", "nama": "ASRIYADI", "nid": "8423037PKW", "jabatan": "JUNIOR TECHNICIAN PEMELIHARAAN MESIN & SIPIL", "bidang": "HAR MESIN"}, {"no": "107", "nama": "MELKI VANDI RANTE", "nid": "9014036ID", "jabatan": "JUNIOR TECHNICIAN PEMELIHARAAN MESIN & SIPIL", "bidang": "HAR MESIN"}, {"no": "108", "nama": "MUH. AKBAL", "nid": "8623027PKW", "jabatan": "JUNIOR TECHNICIAN PEMELIHARAAN MESIN & SIPIL", "bidang": "HAR MESIN"}, {"no": "109", "nama": "DEDI SETIADI", "nid": "9523010WGK", "jabatan": "JUNIOR TECHNICIAN PEMELIHARAAN MESIN & SIPIL", "bidang": "HAR MESIN"}, {"no": "110", "nama": "MASDAR", "nid": "9123016WGK", "jabatan": "TENAGA PENDUKUNG TEKNIK PEMELIHARAAN MESIN", "bidang": "HAR MESIN"}, {"no": "111", "nama": "MUH.ARAFA", "nid": "8923013WGK", "jabatan": "TENAGA PENDUKUNG TEKNIK PEMELIHARAAN MESIN", "bidang": "HAR MESIN"}, {"no": "112", "nama": "SADDAN HUSEN", "nid": "9123012WGK", "jabatan": "JUNIOR TECHNICIAN PEMELIHARAAN MESIN & SIPIL", "bidang": "HAR MESIN"}, {"no": "113", "nama": "SUDARTO", "nid": "8023009WGK", "jabatan": "TENAGA PENDUKUNG TEKNIK PEMELIHARAAN MESIN", "bidang": "HAR MESIN"}, {"no": "114", "nama": "ARISMAN", "nid": "9624053WGK", "jabatan": "TENAGA PENDUKUNG TEKNIK PEMELIHARAAN MESIN", "bidang": "HAR MESIN"}, {"no": "115", "nama": "YUSRAN JAYA", "nid": "9123045WGK", "jabatan": "TENAGA PENDUKUNG TEKNIK PEMELIHARAAN MESIN", "bidang": "HAR MESIN"}, {"no": "116", "nama": "MUH. ADRIANSYAH", "nid": "0124080WGK", "jabatan": "JUNIOR TECHNICIAN PEMELIHARAAN MESIN & SIPIL", "bidang": "HAR MESIN"}, {"no": "117", "nama": "A.M.ISHAQ HAQUE AB", "nid": "9424046WGK", "jabatan": "TENAGA PENDUKUNG TEKNIK PEMELIHARAAN MESIN", "bidang": "HAR MESIN"}, {"no": "118", "nama": "FERDI AGUS", "nid": "9923032WGK", "jabatan": "JUNIOR TECHNICIAN PEMELIHARAAN MESIN & SIPIL", "bidang": "INVENTORY & GUDANG"}, {"no": "119", "nama": "MUHAMMAD SIDDIQ B.", "nid": "9015132ZJY", "jabatan": "TEAM LEADER OUTAGE MANAGEMENT", "bidang": "OUTAGE MANAGEMENT"}, {"no": "120", "nama": "ASRIEL BASTIAD", "nid": "9216020KA", "jabatan": "JUNIOR OFFICER OUTAGE MANAGEMENT", "bidang": "OUTAGE MANAGEMENT"}, {"no": "121", "nama": "PUTRI FATMASARI", "nid": "0023019WGK", "jabatan": "JUNIOR OFFICER OUTAGE MANAGEMENT", "bidang": "OUTAGE MANAGEMENT"}, {"no": "122", "nama": "ANDI RYAN ADRIAN", "nid": "9623029WGK", "jabatan": "JUNIOR OFFICER OUTAGE MANAGEMENT", "bidang": "OUTAGE MANAGEMENT"}, {"no": "123", "nama": "ANDI HIDAYAT", "nid": "9624050WGK", "jabatan": "TENAGA PENDUKUNG OUTAGE MANAGEMENT", "bidang": "OUTAGE MANAGEMENT"}, {"no": "124", "nama": "NURHALIM", "nid": "8716092PC", "jabatan": "TEAM LEADER INVENTORY CONTROL & GUDANG", "bidang": "INVENTORY & GUDANG"}, {"no": "125", "nama": "MUH. ARCANG", "nid": "8523042PKW", "jabatan": "JUNIOR OFFICER INVENTORI KONTROL & GUDANG", "bidang": "INVENTORY & GUDANG"}, {"no": "126", "nama": "ANDI BASO MUNAWIR", "nid": "8623018WGK", "jabatan": "JUNIOR OFFICER INVENTORI KONTROL & GUDANG", "bidang": "INVENTORY & GUDANG"}, {"no": "127", "nama": "AFRENGKI SAPUTRA", "nid": "0024047WGK", "jabatan": "TENAGA PENDUKUNG OFFICER INVENTORY & GUDANG", "bidang": "INVENTORY & GUDANG"}, {"no": "128", "nama": "ARWAN", "nid": "0224055WGK", "jabatan": "TENAGA PENDUKUNG OFFICER INVENTORY & GUDANG", "bidang": "INVENTORY & GUDANG"}, {"no": "129", "nama": "MUH. IQBAL RAWANDI, S.KEL", "nid": "9724062WGK", "jabatan": "TENAGA PENDUKUNG OFFICER INVENTORY & GUDANG", "bidang": "INVENTORY & GUDANG"}, {"no": "130", "nama": "PRAN MOCH SOLEH", "nid": "8713108ZJY", "jabatan": "ASMAN BUSINESS SUPPORT", "bidang": "ADMINISTRASI"}, {"no": "131", "nama": "ANDI RIDWANSYAH BAHAR PUTRA", "nid": "9117374KP", "jabatan": "TEAM LEADER SDM, UMUM & CSR", "bidang": "SDM & UMUM"}, {"no": "132", "nama": "ANDI AISYAH NINGRATI ASHARIANA", "nid": "8923024WGK", "jabatan": "JUNIOR OFFICER SDM, UMUM & CSR", "bidang": "SDM & UMUM"}, {"no": "133", "nama": "MURLINA", "nid": "7823025WGK", "jabatan": "JUNIOR OFFICER SDM, UMUM & CSR", "bidang": "SDM & UMUM"}, {"no": "134", "nama": "ARUN SATRIA PUTRA", "nid": "0024054WGK", "jabatan": "TENAGA PENDUKUNG SDM & UMUM", "bidang": "SDM & UMUM"}, {"no": "135", "nama": "DIRGA FAJAR MAULANA", "nid": "9624057WGK", "jabatan": "TENAGA PENDUKUNG SDM & UMUM", "bidang": "SDM & UMUM"}, {"no": "136", "nama": "YAZID RIZAL", "nid": "8524070WGK", "jabatan": "TENAGA PENDUKUNG SDM & UMUM", "bidang": "SDM & UMUM"}, {"no": "137", "nama": "HILDA RIANYSKA", "nid": "0124058WGK", "jabatan": "SEKERTARIS", "bidang": "SEKERTARIS"}, {"no": "138", "nama": "ERNA S", "nid": "7423002WGK", "jabatan": "HOUSE KEEPER", "bidang": "SDM & UMUM"}, {"no": "139", "nama": "HERNILA", "nid": "9123001WGK", "jabatan": "HOUSE KEEPER", "bidang": "SDM & UMUM"}, {"no": "140", "nama": "NANNA TRIANA", "nid": "7923005WGK", "jabatan": "HOUSE KEEPER", "bidang": "SDM & UMUM"}, {"no": "141", "nama": "AGUS SALIM", "nid": "7823014PKW", "jabatan": "TEAM LEADER LOGISTIK & PENGADAAN", "bidang": "LOGISTIK & PENGADAAN"}, {"no": "142", "nama": "RIZAL HAMZAH", "nid": "9923042WGK", "jabatan": "JUNIOR OFFICER LOGISTIK & PENGADAAN", "bidang": "LOGISTIK & PENGADAAN"}, {"no": "143", "nama": "NURDEDI", "nid": "9223040WGK", "jabatan": "JUNIOR OFFICER LOGISTIK & PENGADAAN", "bidang": "LOGISTIK & PENGADAAN"}, {"no": "144", "nama": "ANNISA MAULIANA AKBAR", "nid": "0024051WGK", "jabatan": "JUNIOR OFFICER LOGISTIK & PENGADAAN", "bidang": "LOGISTIK & PENGADAAN"}, {"no": "145", "nama": "ANDI ALYA AJRIN ASRUL", "nid": "0425095WGK", "jabatan": "TENAGA PENDUKUNG OFFICER LOGISTIK & PENGADAAN", "bidang": "LOGISTIK & PENGADAAN"}, {"no": "146", "nama": "MUHAMMAD RAYHAN SYAKIL", "nid": "9925091WGK", "jabatan": "TENAGA PENDUKUNG OFFICER LOGISTIK & PENGADAAN", "bidang": "LOGISTIK & PENGADAAN"}, {"no": "147", "nama": "SANDRI KURNIAWAN\\\\", "nid": "0124087WGK", "jabatan": "TENAGA PENDUKUNG OFFICER LOGISTIK & PENGADAAN", "bidang": "LOGISTIK & PENGADAAN"}, {"no": "148", "nama": "ZUL QADRI A. SADIK", "nid": "8915104ZJY", "jabatan": "TEAM LEADER KEUANGAN", "bidang": "KEUANGAN"}, {"no": "149", "nama": "NURDIANSAR ARFAH", "nid": "8923019PKW", "jabatan": "JUNIOR OFFICER KEUANGAN", "bidang": "KEUANGAN"}, {"no": "150", "nama": "WARDA", "nid": "8623011PKW", "jabatan": "JUNIOR OFFICER KEUANGAN", "bidang": "KEUANGAN"}, {"no": "151", "nama": "MUHAMMAD ZAKIR KAMARUDDIN", "nid": "9923037WGK", "jabatan": "TENAGA PENDUKUNG OFFICER KEUANGAN", "bidang": "KEUANGAN"}, {"no": "152", "nama": "MUHAMMAD ARFAN SANJAYA", "nid": "9625090WGK", "jabatan": "TENAGA PENDUKUNG OFFICER KEUANGAN", "bidang": "KEUANGAN"}, {"no": "153", "nama": "ANSAR ANAS", "nid": "7523004WGK", "jabatan": "KOORDINATOR MKP", "bidang": "ADMINISTRASI"}, {"no": "154", "nama": "ANDIK YULIANTO", "nid": "8814113KP", "jabatan": "KOORDINATOR NPS", "bidang": "ADMINISTRASI"}, {"no": "155", "nama": "NELLA SUSANTY PINERASMA", "nid": "9124085WGK", "jabatan": "KOORDINATOR NPS", "bidang": "ADMINISTRASI"}]', true);

        foreach ($employees as $emp) {
            $nid = trim($emp["nid"]);
            $nama = trim($emp["nama"]);
            $jabatan = trim($emp["jabatan"]);
            $bidang = trim($emp["bidang"]);

            $pos = Position::firstOrCreate(["name" => $jabatan]);

            $roleId = 4; // STAFF default
            $upperJabatan = strtoupper($jabatan);
            if (str_contains($upperJabatan, "MANAGER UNIT")) {
                $roleId = 1; // MUP
            } elseif (str_contains($upperJabatan, "ASMAN")) {
                $roleId = 2; // ASMAN
            } elseif (str_contains($upperJabatan, "TEAM LEADER") || str_contains($upperJabatan, "PJS TEAM LEADER")) {
                $roleId = 3; // TL
            }

            $unitId = $unitMapping[$bidang] ?? null;

            $user = User::where("employee_no", $nid)->first();
            if (!$user) {
                $user = new User();
                $user->employee_no = $nid;
                $user->email = strtolower($nid) . "@pln.co.id";
                $user->password = Hash::make("123");
            }

            $user->name = $nama;
            $user->unit_id = $unitId;
            $user->role_id = $roleId;
            $user->position_id = $pos->id;
            $user->is_active = true;
            $user->save();
        }

        // Nonaktifkan akun dummy test
        User::whereIn("employee_no", ["PLN-004", "PLN-005", "PLN-006", "PLN-007", "PLN-008", "PLN-009", "0124078WGK"])
            ->update(["is_active" => false]);

        // Hubungkan struktur pelaporan hierarki
        $mup = User::whereHas("role", fn($q) => $q->where("name", "MUP"))->first();
        $asmans = User::whereHas("role", fn($q) => $q->where("name", "ASMAN"))->get();
        $tls = User::whereHas("role", fn($q) => $q->where("name", "TL"))->get();
        $units = OrganizationUnit::pluck("parent_unit_id", "id")->toArray();

        $asmanMap = [];
        foreach ($asmans as $asman) {
            $asmanMap[$asman->unit_id] = $asman->id;
            $asman->asman_id = $mup?->id ?? 1;
            $asman->save();
        }

        $tlMap = [];
        foreach ($tls as $tl) {
            $tlMap[$tl->unit_id] = $tl->id;
            $parentUnit = $units[$tl->unit_id] ?? 1;
            if ($parentUnit == 1) {
                $tl->asman_id = $mup?->id ?? 1;
            } elseif (isset($asmanMap[$parentUnit])) {
                $tl->asman_id = $asmanMap[$parentUnit];
            }
            $tl->save();
        }

        $staffs = User::whereHas("role", fn($q) => $q->where("name", "STAFF"))->get();
        foreach ($staffs as $staff) {
            $staffUnit = $staff->unit_id;
            $parentUnit = $units[$staffUnit] ?? 1;

            if (isset($tlMap[$staffUnit])) {
                $staff->tl_id = $tlMap[$staffUnit];
            }

            if ($parentUnit == 1) {
                $staff->asman_id = $mup?->id ?? 1;
            } elseif (isset($asmanMap[$parentUnit])) {
                $staff->asman_id = $asmanMap[$parentUnit];
            } elseif (isset($asmanMap[$staffUnit])) {
                $staff->asman_id = $asmanMap[$staffUnit];
            }
            $staff->save();
        }
    }

    public function down(): void
    {
        // No-op
    }
};
