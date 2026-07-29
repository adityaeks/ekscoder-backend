<?php

namespace Database\Seeders;

use App\Models\ProjectOrder;
use Illuminate\Database\Seeder;

class ProjectOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = [
            [
                'client_name' => 'Siti Rahma (Fashion Studio)',
                'client_contact' => '0812-3456-7890',
                'title' => 'E-Commerce Website & Payment Gateway',
                'description' => 'Toko online baju wanita dengan opsi katalog interaktif, integrasi Midtrans, dan ongkir otomatis.',
                'budget' => 18000000,
                'paid_amount' => 0,
                'status' => 'requirement',
                'priority' => 'high',
                'deadline' => now()->addDays(20)->format('Y-m-d'),
                'order' => 1,
            ],
            [
                'client_name' => 'PT Maju Bersama (Budi)',
                'client_contact' => 'budi@majubersama.com',
                'title' => 'Corporate Company Profile & CMS',
                'description' => 'Website profil perusahaan profesional lengkap dengan CMS custom untuk manajemen artikel dan layanan.',
                'budget' => 12000000,
                'paid_amount' => 6000000,
                'status' => 'requirement',
                'priority' => 'medium',
                'deadline' => now()->addDays(14)->format('Y-m-d'),
                'order' => 1,
            ],
            [
                'client_name' => 'Klinik Sehat Medika',
                'client_contact' => '0856-7890-1234',
                'title' => 'Internal Inventory & Patient System',
                'description' => 'Sistem stok obat dan rekam medis pasien internal klinik dengan peran dokter dan kasir.',
                'budget' => 25000000,
                'paid_amount' => 12500000,
                'status' => 'in_progress',
                'priority' => 'high',
                'deadline' => now()->addDays(10)->format('Y-m-d'),
                'order' => 1,
            ],
            [
                'client_name' => 'Hotel Grand Resort',
                'client_contact' => 'manager@grandresort.id',
                'title' => 'Mobile Booking Engine API',
                'description' => 'Backend REST API untuk pemesanan kamar hotel, integrasi kalender, dan notifikasi email.',
                'budget' => 35000000,
                'paid_amount' => 20000000,
                'status' => 'review',
                'priority' => 'high',
                'deadline' => now()->addDays(4)->format('Y-m-d'),
                'order' => 1,
            ],
            [
                'client_name' => 'CV Bintang Lima',
                'client_contact' => '0890-1234-5678',
                'title' => 'Modern Landing Page',
                'description' => 'Landing page modern 3D & GSAP animasi untuk promosi produk baru.',
                'budget' => 8500000,
                'paid_amount' => 8500000,
                'status' => 'completed',
                'priority' => 'low',
                'deadline' => now()->subDays(2)->format('Y-m-d'),
                'order' => 1,
            ],
        ];

        foreach ($orders as $orderData) {
            ProjectOrder::create($orderData);
        }
    }
}
