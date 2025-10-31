<?php
return [
    'landing'   => [
        [
            'slug'        => 'startup-launch',
            'title'       => \__( 'Startup Launch', 'sofir' ),
            'description' => \__( 'Hero besar dengan CTA, daftar fitur, testimoni, dan pricing table.', 'sofir' ),
            'path'        => __DIR__ . '/landing/startup-launch.html',
            'category'    => 'landing',
            'context'     => [ 'page', 'template' ],
        ],
        [
            'slug'        => 'agency-spotlight',
            'title'       => \__( 'Agency Spotlight', 'sofir' ),
            'description' => \__( 'Layout elegan untuk agensi dengan layanan, proses kerja, dan form kontak.', 'sofir' ),
            'path'        => __DIR__ . '/landing/agency-spotlight.html',
            'category'    => 'landing',
            'context'     => [ 'page' ],
        ],
    ],
    'directory' => [
        [
            'slug'        => 'city-directory',
            'title'       => \__( 'City Directory', 'sofir' ),
            'description' => \__( 'Listing grid dengan filter lokasi, rating, dan kategori.', 'sofir' ),
            'path'        => __DIR__ . '/directory/city-directory.html',
            'category'    => 'directory',
            'context'     => [ 'page' ],
        ],
        [
            'slug'        => 'healthcare-network',
            'title'       => \__( 'Healthcare Network', 'sofir' ),
            'description' => \__( 'Direktori dokter dengan pencarian cepat dan highlight layanan.', 'sofir' ),
            'path'        => __DIR__ . '/directory/healthcare-network.html',
            'category'    => 'directory',
            'context'     => [ 'page' ],
        ],
    ],
    'blog'      => [
        [
            'slug'        => 'modern-magazine',
            'title'       => \__( 'Modern Magazine', 'sofir' ),
            'description' => \__( 'Portal berita dengan layout hero carousel, kategori, dan highlight editorial.', 'sofir' ),
            'path'        => __DIR__ . '/blog/modern-magazine.html',
            'category'    => 'blog',
            'context'     => [ 'page', 'template' ],
        ],
    ],
    'profile'   => [
        [
            'slug'        => 'business-profile',
            'title'       => \__( 'Business Profile', 'sofir' ),
            'description' => \__( 'Profil perusahaan dengan hero, layanan, statistik, dan CTA.', 'sofir' ),
            'path'        => __DIR__ . '/profile/business-profile.html',
            'category'    => 'profile',
            'context'     => [ 'page' ],
        ],
    ],
];
