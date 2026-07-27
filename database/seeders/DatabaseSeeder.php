<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\Package;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Sesuai dengan spesifikasi 01-DESIGN.md & 02-PRD.md.
     */
    public function run(): void
    {
        // =========================================================================
        // 1. Users
        // =========================================================================
        
        // Admin
        $admin = User::create([
            'name' => 'Boutique Owner (Admin)',
            'email' => 'admin@weddingkita.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '081234567890',
        ]);

        // Member
        $member = User::create([
            'name' => 'Calon Pengantin (Member)',
            'email' => 'member@example.com',
            'password' => Hash::make('password'),
            'role' => 'member',
            'phone' => '089876543210',
        ]);

        // =========================================================================
        // 2. Kategori
        // =========================================================================
        $catGaunKlasik = Category::create(['name' => 'Gaun Klasik', 'slug' => 'gaun-klasik']);
        $catGaunModern = Category::create(['name' => 'Gaun Modern', 'slug' => 'gaun-modern']);
        $catJasPria    = Category::create(['name' => 'Jas Pria',    'slug' => 'jas-pria']);
        $catAksesoris  = Category::create(['name' => 'Aksesoris',   'slug' => 'aksesoris']);
        $catPaket      = Category::create(['name' => 'Paket Lengkap', 'slug' => 'paket-lengkap']);

        // =========================================================================
        // 3. Services
        // =========================================================================
        $srvMUA = Service::create([
            'name' => 'Jasa MUA & Hairdo',
            'description' => 'Jasa make-up artist profesional dan tata rambut di hari acara.',
            'price' => 1500000.00,
            'is_active' => true,
        ]);

        $srvDekor = Service::create([
            'name' => 'Dekorasi Backdrop Mini',
            'description' => 'Backdrop bunga minimalis dan pencahayaan untuk lamaran atau akad.',
            'price' => 2000000.00,
            'is_active' => true,
        ]);

        $srvAsuransi = Service::create([
            'name' => 'Asuransi Proteksi Minor',
            'description' => 'Melindungi dari denda noda makanan kecil atau jahitan lepas ringan.',
            'price' => 150000.00,
            'is_active' => true,
        ]);

        $srvCuci = Service::create([
            'name' => 'Layanan Dry Cleaning Kilat',
            'description' => 'Cuci kering secepatnya pasca pemakaian tanpa cemas.',
            'price' => 250000.00,
            'is_active' => true,
        ]);

        // =========================================================================
        // 4. Items (Unit Fisik Gaun / Jas / Aksesoris)
        // =========================================================================
        
        // Gaun Klasik
        $gaunAline = Item::create([
            'category_id' => $catGaunKlasik->id,
            'name' => 'Gaun A-Line Brokat Klasik',
            'description' => 'Gaun A-line putih bersih dengan brokat bunga halus bermotif klasik, kerah tinggi, lengan panjang transparan.',
            'call_code' => 'WK-0142',
            'size_label' => 'M / LD 88cm - LP 70cm',
            'base_price' => 1800000.00,
            'deposit_amount' => 0.00, // Tidak ada jaminan deposit (PRD §11)
            'status' => 'aktif',
        ]);

        $gaunSatin = Item::create([
            'category_id' => $catGaunKlasik->id,
            'name' => 'Gaun Ballgown Satin Velvet',
            'description' => 'Satin premium yang tebal dan jatuh anggun, punggung berdesain tali pengikat silang (corset back) agar fleksibel.',
            'call_code' => 'WK-0288',
            'size_label' => 'L / LD 94cm - LP 76cm',
            'base_price' => 2200000.00,
            'deposit_amount' => 0.00,
            'status' => 'aktif',
        ]);

        // Gaun Modern
        $gaunMermaid = Item::create([
            'category_id' => $catGaunModern->id,
            'name' => 'Gaun Mermaid Lace Contemporary',
            'description' => 'Potongan mermaid yang pas di badan dengan ornamen renda bunga kontemporer 3D, ekor gaun panjang 1.5 meter.',
            'call_code' => 'WK-0312',
            'size_label' => 'S / LD 84cm - LP 66cm',
            'base_price' => 2500000.00,
            'deposit_amount' => 0.00,
            'status' => 'aktif',
        ]);

        $gaunMinimalis = Item::create([
            'category_id' => $catGaunModern->id,
            'name' => 'Silk Slip Gown Minimalist',
            'description' => 'Gaun sutra minimalis bergaya slip dress modern, punggung terbuka dramatis, siluet ramping elegan.',
            'call_code' => 'WK-0401',
            'size_label' => 'M / LD 88cm - LP 72cm',
            'base_price' => 1950000.00,
            'deposit_amount' => 0.00,
            'status' => 'aktif',
        ]);

        // Jas Pria
        $jasTuxedo = Item::create([
            'category_id' => $catJasPria->id,
            'name' => 'Tuxedo Hitam Wool Klasik',
            'description' => 'Tuxedo wool 100% hitam pekat dengan lapel satin mengkilap, celana panjang hitam, kemeja putih kerah lipat.',
            'call_code' => 'WK-1002',
            'size_label' => 'L / Bahu 46cm - Dada 100cm',
            'base_price' => 1200000.00,
            'deposit_amount' => 0.00,
            'status' => 'aktif',
        ]);

        $jasSage = Item::create([
            'category_id' => $catJasPria->id,
            'name' => 'Double Breasted Suit Sage Green',
            'description' => 'Setelan jas kancing ganda berwarna hijau sage pastel modern, sangat cocok untuk tema pernikahan garden party.',
            'call_code' => 'WK-1105',
            'size_label' => 'XL / Bahu 48cm - Dada 106cm',
            'base_price' => 1350000.00,
            'deposit_amount' => 0.00,
            'status' => 'aktif',
        ]);

        // Aksesoris
        $veilMutiara = Item::create([
            'category_id' => $catAksesoris->id,
            'name' => 'Veil Wedding Mutiara Panjang',
            'description' => 'Veil berbahan tile premium dengan taburan mutiara air tawar buatan di sepanjang tepi kain, panjang 2.5 meter.',
            'call_code' => 'WK-2022',
            'size_label' => 'One Size / Panjang 250cm',
            'base_price' => 300000.00,
            'deposit_amount' => 0.00,
            'status' => 'aktif',
        ]);

        $crownTiara = Item::create([
            'category_id' => $catAksesoris->id,
            'name' => 'Tiara Mahkota Kristal Swarovski',
            'description' => 'Mahkota tiara tinggi dengan taburan swarovski berkilau untuk mempercantik sanggul pengantin.',
            'call_code' => 'WK-2110',
            'size_label' => 'Standard Crown Size',
            'base_price' => 450000.00,
            'deposit_amount' => 0.00,
            'status' => 'aktif',
        ]);

        // =========================================================================
        // 5. Packages (Paket Lengkap)
        // =========================================================================
        
        // Paket Lengkap 1: Klasik Agung
        $paketKlasik = Package::create([
            'name' => 'Paket Pernikahan Klasik Agung',
            'slug' => 'paket-pernikahan-klasik-agung',
            'description' => 'Paket sewa lengkap siap pakai mencakup Gaun A-Line Brokat Klasik, Tuxedo Hitam Wool Klasik, Veil Mutiara, serta Jasa MUA profesional di hari acara.',
            'category_id' => $catPaket->id,
            'base_price' => 4200000.00,
            'deposit_amount' => 0.00,
        ]);
        // Hubungkan items
        $paketKlasik->items()->attach([$gaunAline->id, $jasTuxedo->id, $veilMutiara->id]);
        // Hubungkan services
        $paketKlasik->services()->attach([
            $srvMUA->id => ['is_default' => true],
            $srvAsuransi->id => ['is_default' => false],
            $srvCuci->id => ['is_default' => true],
        ]);

        // Paket Lengkap 2: Modern Forest
        $paketModern = Package::create([
            'name' => 'Paket Garden Party Modern Forest',
            'slug' => 'paket-garden-party-modern-forest',
            'description' => 'Paket sewa elegan untuk pernikahan luar ruangan. Mencakup Gaun Minimalis Silk Slip, Jas Double Breasted Sage Green, Crown Tiara, serta Jasa MUA dan Asuransi minor.',
            'category_id' => $catPaket->id,
            'base_price' => 4500000.00,
            'deposit_amount' => 0.00,
        ]);
        $paketModern->items()->attach([$gaunMinimalis->id, $jasSage->id, $crownTiara->id]);
        $paketModern->services()->attach([
            $srvMUA->id => ['is_default' => true],
            $srvAsuransi->id => ['is_default' => true],
            $srvDekor->id => ['is_default' => false],
        ]);
    }
}
