<?php

namespace App\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

final class WebsiteSettings
{
    private const STORAGE_PATH = 'website_settings.json';

    /** @var array<string, mixed>|null */
    private static ?array $cache = null;

    /**
     * @return array<string, mixed>
     */
    public static function all(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        $data = self::loadRaw();
        if (empty($data)) {
            $data = self::defaults();
            self::save($data);
        }

        // Ensure defaults exist (non-destructive merge)
        $merged = array_replace_recursive(self::defaults(), $data);
        if ($merged !== $data) {
            self::save($merged);
        }

        self::$cache = $merged;
        return self::$cache;
    }

    public static function get(string $path, mixed $default = null): mixed
    {
        return data_get(self::all(), $path, $default);
    }

    public static function set(string $path, mixed $value): void
    {
        $data = self::all();
        data_set($data, $path, $value);
        self::save($data);
    }

    /**
     * @param array<string, mixed> $incoming
     */
    public static function replace(array $incoming): void
    {
        $merged = array_replace_recursive(self::defaults(), $incoming);
        self::save($merged);
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function save(array $data): void
    {
        $payload = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        Storage::disk('local')->put(self::STORAGE_PATH, $payload === false ? '{}' : $payload);

        self::$cache = $data;
    }

    /**
     * @return array<string, mixed>
     */
    private static function loadRaw(): array
    {
        if (!Storage::disk('local')->exists(self::STORAGE_PATH)) {
            return [];
        }

        $raw = Storage::disk('local')->get(self::STORAGE_PATH);
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * @return array<string, mixed>
     */
    private static function defaults(): array
    {
        return [
            'brand' => [
                'favicon' => 'assets/img/favicon.png',
                'logo' => 'assets/img/logo.png',
                'logo_white' => 'assets/img/logo_wh.svg',
                'logo_svg' => 'assets/img/logo.svg',
                'logo_min' => 'assets/img/logo-min.svg',
            ],
            'footer' => [
                'bg_image' => 'assets/img/footer/main_footer_bg.jpg',
                'logo' => 'assets/img/logo_wh.svg',
            ],
            'contact' => [
                'phone_display' => '+62 (021) 89830313 / 0314',
                'phone_tel' => '02189830313',
                'phone_display_alt' => '+62 (021) 89830314',
                'phone_tel_alt' => '02189830314',
                'email' => 'market.ilsamindonesia@yahoo.com',
                'form_recipient_email' => 'market.ilsamindonesia@yahoo.com',
                'map_url' => 'https://maps.app.goo.gl/reUj3juAoQ8NrGLE6',
                'map_embed_src' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3965.0320185769133!2d107.23779097590075!3d-6.389870062501263!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e699f000ca996c1%3A0x713bcc5addd9fa22!2sPT.%20ILSAM%20GLOBAL%20INDONESIA%20(IG-103)!5e0!3m2!1sid!2sid!4v1768213302522!5m2!1sid!2sid',
                'address_text' => "Jl. Trans Heksa Artha Industrial Hill Area Block E No.13 Wanajaya Village,\nDistrict Telukjambe Barat, Karawang Regency, West Java, 41361",
                'opening_hours' => 'Monday - Friday : 08:00 - 17:00',
                'page' => [
                    'breadcrumb_bg' => 'assets/img/img14.jpg',
                    'lets_talk_bg' => 'assets/img/img8.jpeg',
                ],
            ],
            'top' => [
                'website_url' => 'https://www.ilsam.com/',
                'website_label' => 'www.ilsam.com',
            ],
            'offcanvas' => [
                'website_url' => 'https://www.ilsam.com/',
                'email' => 'market.ilsamindonesia@yahoo.com',
                'location_url' => 'https://maps.app.goo.gl/reUj3juAoQ8NrGLE6',
            ],
            'downloads' => [
                'company_profile_url' => 'https://drive.google.com/uc?export=download&id=1G4sEtK56mxtXtg71gsx7zyvDG2CSVqVX',
            ],
            'seo' => [
                'home' => [
                    'meta_description' => [
                        'en' => 'PT ILSAM GLOBAL INDONESIA (Ilsam) supplies chemical colorants and coating solutions for PU/PVC synthetic leather and footwear manufacturing. Based in Karawang, West Java—serving Cikarang, Bekasi, Karawang, Jakarta, and customers across Java & Indonesia.',
                        'id' => 'PT ILSAM GLOBAL INDONESIA (Ilsam) menyediakan chemical colorants dan solusi coating untuk PU/PVC synthetic leather dan industri footwear. Berbasis di Karawang, Jawa Barat—melayani Cikarang, Bekasi, Karawang, Jakarta, serta pelanggan di seluruh Jawa & Indonesia.',
                        'ko' => 'PT ILSAM GLOBAL INDONESIA (Ilsam) supplies chemical colorants and coating solutions for PU/PVC synthetic leather and footwear manufacturing. Based in Karawang, West Java—serving customers across Indonesia.',
                    ],
                    'meta_image' => 'assets/img/img1.jpg',
                ],
                'contact' => [
                    'meta_description' => [
                        'en' => 'Contact PT ILSAM GLOBAL INDONESIA in Karawang, West Java for chemical colorants and coating solutions (PU/PVC synthetic leather & footwear manufacturing). Serving Cikarang, Bekasi, Karawang, Jakarta, and across Java & Indonesia.',
                        'id' => 'Hubungi PT ILSAM GLOBAL INDONESIA di Karawang, Jawa Barat untuk kebutuhan chemical colorants dan solusi coating (PU/PVC synthetic leather & footwear). Melayani Cikarang, Bekasi, Karawang, Jakarta, dan seluruh Jawa & Indonesia.',
                        'ko' => '서자바 주 카라왕에 위치한 PT ILSAM GLOBAL INDONESIA에 문의하세요. PU/PVC 합성가죽 및 신발 제조용 컬러런트/코팅 솔루션을 제공합니다. 치카랑, 베카시, 카라왕, 자카르타 및 인도네시아 전역에 대응합니다.',
                    ],
                    'meta_image' => 'assets/img/img14.jpg',
                ],
                'about' => [
                    'company' => [
                        'meta_description' => [
                            'en' => 'Learn about PT ILSAM GLOBAL INDONESIA: our company overview, values, and journey. Explore our profile and milestones.',
                            'id' => 'Pelajari tentang PT ILSAM GLOBAL INDONESIA: profil perusahaan, nilai, dan perjalanan kami. Lihat ringkasan dan milestones.',
                            'ko' => 'PT ILSAM GLOBAL INDONESIA 회사 소개, 가치, 그리고 연혁을 확인하세요.',
                        ],
                        'meta_image' => 'assets/img/img8.jpeg',
                    ],
                    'ceo' => [
                        'meta_description' => [
                            'en' => 'Read the CEO message from PT ILSAM GLOBAL INDONESIA—our commitment, direction, and vision for sustainable growth.',
                            'id' => 'Baca pesan CEO PT ILSAM GLOBAL INDONESIA—komitmen, arah, dan visi untuk pertumbuhan berkelanjutan.',
                            'ko' => 'PT ILSAM GLOBAL INDONESIA CEO 메시지와 비전, 방향성을 확인하세요.',
                        ],
                        'meta_image' => 'assets/img/img6.jpg',
                    ],
                    'philosophy' => [
                        'meta_description' => [
                            'en' => 'Discover the philosophy of PT ILSAM GLOBAL INDONESIA: our principles, quality focus, and long-term commitment to customers and partners.',
                            'id' => 'Kenali filosofi PT ILSAM GLOBAL INDONESIA: prinsip, fokus kualitas, dan komitmen jangka panjang untuk pelanggan dan partner.',
                            'ko' => 'PT ILSAM GLOBAL INDONESIA의 철학과 원칙, 품질에 대한 약속을 확인하세요.',
                        ],
                        'meta_image' => 'assets/img/img3.jpg',
                    ],
                ],
                'career' => [
                    'meta_description' => [
                        'en' => 'Explore career opportunities at PT ILSAM GLOBAL INDONESIA. Browse open positions, filter by department and location, and apply online.',
                        'id' => 'Jelajahi peluang karier di PT ILSAM GLOBAL INDONESIA. Lihat posisi yang tersedia, filter berdasarkan departemen dan lokasi, lalu apply online.',
                        'ko' => 'PT ILSAM GLOBAL INDONESIA 채용 기회를 확인하고 지원하세요.',
                    ],
                    'meta_image' => 'assets/img/img9.jpg',
                ],
                'technology' => [
                    'meta_description' => [
                        'en' => 'Explore the technology and R&D focus of PT ILSAM GLOBAL INDONESIA, including our domains, standards, and innovation approach.',
                        'id' => 'Jelajahi teknologi dan fokus R&D PT ILSAM GLOBAL INDONESIA, termasuk domain, standar, dan pendekatan inovasi kami.',
                        'ko' => 'PT ILSAM GLOBAL INDONESIA의 기술 및 R&D 방향을 확인하세요.',
                    ],
                    'meta_image' => 'assets/img/img15.jpg',
                ],
                'technology_certification_status' => [
                    'meta_description' => [
                        'en' => 'Certification status list for PT ILSAM GLOBAL INDONESIA: view active, expiring, and expired certifications for supplied chemicals and materials.',
                        'id' => 'Daftar status sertifikasi PT ILSAM GLOBAL INDONESIA: lihat sertifikasi aktif, akan habis, dan telah habis untuk chemical/material yang disuplai.',
                        'ko' => 'PT ILSAM GLOBAL INDONESIA 인증 현황을 확인하세요.',
                    ],
                    'meta_image' => 'assets/img/img15.jpg',
                ],
            ],
            'about' => [
                'company' => [
                    'image' => 'assets/img/img8.jpeg',
                ],
                'ceo' => [
                    'portrait_image' => 'assets/img/aboutus/ceo.jpg',
                ],
                'philosophy' => [
                    'hero_bg' => 'assets/img/aboutus/img12.jpg',
                ],
            ],
            'technology' => [
                'page' => [
                    'hero_bg' => 'assets/img/img15.jpg',
                    'workflow_bg' => 'assets/img/img4.jpg',
                    'breadcrumb_bg' => 'assets/img/img4.jpg',
                ],
            ],
            'products' => [
                'page' => [
                    'breadcrumb_bg' => 'assets/img/img4.jpg',
                ],
            ],
            'privacy_policy' => [
                'page' => [
                    'breadcrumb_bg' => 'assets/img/aboutus/img11.jpg',
                ],
            ],
            'home' => [
                'hero_slides' => [
                    'assets/img/img1.jpg',
                    'assets/img/img4.jpg',
                    'assets/img/img3.jpg',
                    'assets/img/img10.jpg',
                    'assets/img/img9.jpg',
                ],
                'decorations' => [
                    'banner_shape_2' => 'assets/img/style2/banner/shape-2.png',
                    'banner_shape_3' => 'assets/img/style2/banner/shape-3.png',
                    'products_shape_2' => 'assets/img/style2/what-we-do-2/shape-2.png',
                    'products_shape_3' => 'assets/img/style2/what-we-do-2/shape-3.png',
                    'products_shape_4' => 'assets/img/style2/what-we-do-2/shape-4.png',
                ],
                'sections' => [
                    'about_image' => 'assets/img/main_who_triangle.png',
                    'experience_bg' => 'assets/img/img6.jpg',
                    'products_cards' => [
                        'colorants_bg' => 'assets/img/img4.jpg',
                        'surface_coating_agents_bg' => 'assets/img/img6.jpg',
                        'additive_coating_bg' => 'assets/img/img3.jpg',
                        'pu_resin_bg' => 'assets/img/img1.jpg',
                    ],
                ],
                'text_slider_companies' => [
                    'PT. KINDO MAKMUR JAYA',
                    'PT. SINYOUNG ABADI',
                    'PT. KONES TAEYA INDUSTRY',
                    'PT. BINTANG FAMILY INDONESIA',
                    'PT. SINAR CONTINENTAL',
                    'PT. SUN LEE JAYA',
                    'PT. SEMPURNAINDAH MULTINUSANTARA',
                    'PT. BEN TECH ABADI',
                    'PT. BAIKSAN INDONESIA',
                    'PT. DAEHWA LEATHER LESTARI',
                    'PT. CIPTA HARMONI JAYA',
                    'PT. YOUNGIL LEATHER INDONESIA',
                    'PT. JOIL INNI INDONESIA',
                    'PT. DAEWON ECO INDONESIA',
                ],
            ],
        ];
    }
}
