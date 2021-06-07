<?php
$session      = $this->session->all_userdata();
$username     = ucwords($session['username']);
$level        = $session['level'];
$level_native = $session['level_native'];
$user_id      = $session['user_id'];

$menu_list = [
    [
        'name' => 'Beranda',
        'url'  => 'home',
        'icon' => 'fa fa-home'
    ],
    [
        'title' => 'Penerbitan'
    ],
    [
        'name' => 'Draft',
        'url'  => 'draft',
        'icon' => 'fa fa-paperclip'
    ],
    [
        'name'  => 'Buku',
        'url'   => 'book',
        'icon'  => 'fa fa-book',
        'level' => 'superadmin|admin_penerbitan|admin_percetakan|admin_gudang|admin_pemasaran'
    ],
    [
        'title' => 'Produksi',
        'level' => 'superadmin|admin_percetakan|staff_percetakan'
    ],
    [
        'name' => 'Order Cetak',
        'url'  => 'print_order',
        'icon' => 'fa fa-print',
        'level' => 'superadmin|admin_percetakan|staff_percetakan'
    ],
    [
        'title' => 'Pasca Produksi',
        'level' => 'superadmin|admin_gudang|admin_pemasaran|staff_gudang'
    ],
    [
        'name'  => 'Stok Buku',
        'url'   => 'book_stock',
        'icon'  => 'fas fa-boxes',
        'level' => 'superadmin|admin_gudang|admin_pemasaran',
    ],
    [
        'name'  => 'Aset Buku',
        'url'   => 'book_asset',
        'icon'  => 'fas fa-coins',
        'level' => 'superadmin|admin_gudang|admin_pemasaran',
    ],
    [
        'name'  => 'Penerimaan Buku',
        'url'   => 'book_receive',
        'icon'  => 'fas fa-file-import',
        'level' => 'superadmin|admin_gudang'
    ],
    [
        'name'  => 'Pesanan Buku',
        'url'   => 'book_request',
        'icon'  => 'fa fa-file-invoice',
        'level' => 'superadmin|admin_gudang|admin_pemasaran'
    ],
    [
        'name'  => 'Pemindahan Buku',
        'url'   => 'book_transfer',
        'icon'  => 'fas fa-dolly',
        'level' => 'superadmin|admin_gudang|admin_pemasaran'
    ],
    [
        'name'  => 'Buku Non Penjualan',
        'url'   => 'book_non_sales',
        'icon'  => 'fas fa-gifts',
        'level' => 'superadmin|admin_gudang|admin_pemasaran'
    ],
    [
        'name'  => 'Transaksi Buku',
        'url'   => 'book_transaction',
        'icon'  => 'fas fa-exchange-alt',
        'level' => 'superadmin|admin_gudang'

    ],
    [
        'title' => 'Pemasaran',
        'level' => 'superadmin|admin_gudang|admin_pemasaran|admin_keuangan'
    ],
    [
        'name' => 'Proforma',
        'url'  => 'proforma',
        'icon' => 'fa fa-file-archive',
        'level' => 'superadmin|admin_gudang|admin_pemasaran'
    ],
    [
        'name' => 'Faktur',
        'url'  => 'invoice',
        'icon' => 'fa fa-file-invoice',
        'level' => 'superadmin|admin_gudang|admin_pemasaran',
        'child' => [
            [
                'name' => 'Dashboard',
                'url'  => 'invoice',
                'level' => 'superadmin|admin_gudang|admin_pemasaran',
            ],
            [
                'name' => 'Showroom',
                'url'  => 'invoice/add_showroom',
                'level' => 'superadmin|admin_gudang|admin_pemasaran'
            ],
        ]
    ],
    [
        'name' => 'Pendapatan',
        'url'  => 'earning',
        'icon' => 'fas fa-money-check-alt',
        'level' => 'superadmin|admin_gudang|admin_keuangan'
    ],
    [
        'name' => 'Royalti',
        'url'  => 'royalty',
        'icon' => 'fa fa-star',
        'level' => 'superadmin|admin_gudang|admin_keuangan'
    ],
    [
        'title' => 'Data',
        'level' => 'superadmin|admin_penerbitan|editor|layouter|admin_gudang|admin_pemasaran'
    ],
    [
        'name'  => 'Lembar Kerja',
        'url'   => 'worksheet',
        'icon'  => 'fa fa-pencil-alt',
        'level' => 'superadmin|admin_penerbitan|editor|layouter',
    ],
    [
        'name'  => 'Penulis',
        'url'   => 'author',
        'icon'  => 'fa fa-user-tie',
        'level' => 'superadmin|admin_penerbitan'
    ],
    [
        'name'  => 'Reviewer',
        'url'   => 'reviewer',
        'icon'  => 'fa fa-user-graduate',
        'level' => 'superadmin|admin_penerbitan'
    ],
    [
        'name'  => 'Customer',
        'url'   => 'customer',
        'icon'  => 'fa fa-address-card',
        'level' => 'superadmin|admin_pemasaran|admin_keuangan'
    ],
    [
        'name'  => 'Akun User',
        'url'   => 'user',
        'icon'  => 'fa fa-users',
        'level' => 'superadmin'
    ],
    [
        'name'  => 'Master Data',
        'icon'  => 'fa fa-puzzle-piece',
        'level' => 'superadmin|admin_penerbitan|admin_gudang|admin_pemasaran',
        'child' => [
            [
                'name'  => 'Kategori Draft',
                'url'   => 'category',
                'level' => 'superadmin|admin_penerbitan'
            ],
            [
                'name'  => 'Tema Draft',
                'url'   => 'theme',
                'level' => 'superadmin|admin_penerbitan'
            ],
            [
                'name'  => 'Unit Kerja Penulis',
                'url'   => 'work_unit',
                'level' => 'superadmin|admin_penerbitan'
            ],
            [
                'name'  => 'Institusi Penulis',
                'url'   => 'institute',
                'level' => 'superadmin|admin_penerbitan'
            ],
            [
                'name'  => 'Fakultas Reviewer',
                'url'   => 'faculty',
                'level' => 'superadmin|admin_penerbitan'
            ],
            [
                'name'  => 'Perpustakaan',
                'url'   => 'library',
                'level' => 'superadmin|admin_gudang|admin_pemasaran'
            ],
        ]
    ],
    [
        'name'  => 'Dokumen',
        'url'   => 'document',
        'icon'  => 'fa fa-file',
        'level' => 'superadmin|admin_penerbitan'
    ],
    [
        'title' => 'Laporan',
        'level' => 'superadmin|admin_penerbitan'
    ],
    [
        'name'  => 'Laporan Produksi',
        'url'   => 'production_report',
        'icon'  => 'fa fa-chart-bar',
        'level' => 'superadmin'
    ],
    // [
    //     'name'  => 'Grafik',
    //     'url'   => 'reporting',
    //     'icon'  => 'fa fa-chart-bar',
    //     'level' => 'superadmin|admin_penerbitan'
    // ],
    [
        'name'  => 'Performa Staff',
        'url'   => 'performance',
        'icon'  => 'fa fa-walking',
        'level' => 'superadmin|admin_penerbitan'
    ],
    [
        'title' => 'Sistem',
        'level' => 'superadmin'
    ],
    [
        'name'  => 'Pengaturan',
        'url'   => 'setting',
        'icon'  => 'fa fa-cog',
        'level' => 'superadmin'
    ],
]
?>

<aside class="app-aside app-aside-expand-md app-aside-light">
    <div class="aside-content">
        <header class="aside-header d-block d-md-none">
            <button
                class="btn-account"
                type="button"
                data-toggle="collapse"
                data-target="#dropdown-aside"
            >
                <span class="user-avatar user-avatar-lg">
                    <img src="<?= base_url('assets/images/avatars/profile.jpg'); ?>">
                </span>
                <span class="account-icon">
                    <span class="fa fa-caret-down fa-lg"></span>
                </span>
                <span class="account-summary">
                    <span class="account-name"><?= $username; ?></span>
                    <span class="account-description"><?= ucwords(str_replace('_', ' ', $level)) ?></span>
                </span>
            </button>

            <div
                id="dropdown-aside"
                class="dropdown-aside collapse"
            >
                <div class="pb-3">
                    <?php if ($level_native == 'author_reviewer') : ?>
                        <?php if ($level == 'author') : ?>
                            <a
                                class="dropdown-item"
                                href="<?= base_url('auth/multilevel/reviewer'); ?>"
                            >
                                <span class="dropdown-icon fa fa-sign-in-alt"></span> Masuk sebagai Reviewer</a>
                        <?php else : ?>
                            <a
                                class="dropdown-item"
                                href="<?= base_url('auth/multilevel/author'); ?>"
                            >
                                <span class="dropdown-icon fa fa-sign-in-alt"></span> Masuk sebagai Author</a>
                        <?php endif; ?>
                        <hr>
                    <?php endif; ?>

                    <a
                        class="dropdown-item"
                        href="<?= base_url('auth/change_email'); ?>"
                    >
                        <span class="dropdown-icon fa fa-envelope"></span> Ubah Email</a>
                    <a
                        class="dropdown-item"
                        href="<?= base_url('auth/change_password'); ?>"
                    >
                        <span class="dropdown-icon fa fa-cog"></span> Ubah Password</a>
                    <a
                        class="dropdown-item"
                        href="<?= base_url('auth/logout'); ?>"
                    >
                        <span class="dropdown-icon fa fa-sign-out-alt"></span> Logout</a>
                </div>
            </div>
        </header>

        <div class="aside-menu overflow-hidden">
            <nav
                id="stacked-menu"
                class="stacked-menu"
            >
                <ul class="menu">
                    <?php foreach ($menu_list as $menu) : ?>
                        <?php
                        $level_allowed = isset($menu['level']) ? explode('|', $menu['level']) : [];
                        $is_shown = !isset($menu['level']) || isset($menu['level']) && in_array($level, $level_allowed);
                        ?>
                        <?php if ($is_shown) : ?>
                            <!-- title -->
                            <?php if (isset($menu['title'])) {
                                echo '<li class="menu-header">' . $menu['title'] . '</li>';
                            } ?>

                            <!-- single -->
                            <?php if (isset($menu['name']) && !isset($menu['child'])) : ?>
                                <li class="menu-item <?= ($pages == $menu['url']) ? 'has-active' : ''; ?>">
                                    <a
                                        href="<?= base_url($menu['url']); ?>"
                                        class="menu-link"
                                    >
                                        <span class="menu-icon <?= $menu['icon'] ?>"></span>
                                        <span class="menu-text"><?= $menu['name'] ?></span>
                                    </a>
                                </li>
                            <?php endif ?>

                            <!-- nested -->
                            <?php if (isset($menu['name']) && isset($menu['child'])) : ?>
                                <?php $child_pages = array_map(function ($child) {
                                    return $child['url'];
                                }, $menu['child']) ?>
                                <li class="menu-item has-child <?= in_array($pages, $child_pages) ? 'has-active' : ''; ?>">
                                    <a
                                        href="#"
                                        class="menu-link"
                                    >
                                        <span class="menu-icon <?= $menu['icon'] ?>"></span>
                                        <span class="menu-text"><?= $menu['name'] ?></span>
                                    </a>
                                    <ul class="menu">
                                        <?php foreach ($menu['child'] as $child) : ?>
                                            <li class="menu-item <?= ($pages == $child['url']) ? 'has-active' : ''; ?>">
                                                <a
                                                    href="<?= base_url($child['url']); ?>"
                                                    class="menu-link"
                                                ><?= $child['name'] ?></a>
                                            </li>
                                        <?php endforeach ?>
                                    </ul>
                                </li>
                            <?php endif ?>
                        <?php endif ?>
                    <?php endforeach ?>
                </ul>
            </nav>
        </div>

        <footer class="aside-footer border-top p-3">
            <button
                class="btn btn-light btn-block text-primary"
                data-toggle="skin"
            >Night mode <i class="fas fa-moon ml-1"></i></button>
        </footer>
    </div>
</aside>
