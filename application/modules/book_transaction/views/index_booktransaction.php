<?php
$level              = check_level();
$per_page           = $this->input->get('per_page') ?? 10;
$start_date         = $this->input->get('start_date');
$end_date           = $this->input->get('end_date');
$keyword            = $this->input->get('keyword');
$page               = $this->uri->segment(2);
$i                  = isset($page) ? $page * $per_page - $per_page : 0;

?>

<header class="page-title-bar">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url(); ?>"><span class="fa fa-home"></span></a>
            </li>
            <li class="breadcrumb-item active">
                <a class="text-muted">Transaksi Buku</a>
            </li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title"> Transaksi Buku </h1>
            <span class="badge badge-info">Total:
                <?= $total; ?>
            </span>
        </div>
        <!-- <a  href="<?= base_url("$pages/add"); ?>"
            class="btn btn-primary btn-sm">
            <i class="fa fa-plus fa-fw"></i> Tambah
        </a> -->
    </div>
</header>
<div class="page-section">
    <div class="row">
        <div class="col-12">
            <section class="card card-fluid">
                <div class="card-body p-0">
                    <div class="p-3">
                        <?= form_open($pages, ['method' => 'GET']); ?>
                        <div class="row">
                            <div class="col-12 col-md-4">
                                <label for="per_page">Data per halaman</label>
                                <?= form_dropdown('per_page', get_per_page_options(), $per_page, 'id="per_page" class="form-control custom-select d-block" title="List per page"'); ?>
                            </div>
                            <div class="col-12 col-md-4 mb-3">
                                <label for="start_date">Tanggal Mulai</label>
                                <?= form_input('start_date', $start_date, 'class="form-control dates"')?>
                            </div>
                            <div class="col-12 col-md-4 mb-3">
                                <label for="end_date">Tanggal Selesai</label>
                                <?= form_input('end_date', $end_date, 'class="form-control dates"')?>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="status">Pencarian</label>
                                <?= form_input('keyword', $keyword, 'placeholder="Cari berdasarkan Nama" class="form-control"'); ?>
                            </div>
                            <div class="col-12 col-lg-4">
                                <label>&nbsp;</label>
                                <div class="btn-group btn-block" role="group" aria-label="Filter button">
                                    <button class="btn btn-secondary" type="button"
                                        onclick="location.href = '<?= base_url($pages); ?>'"> Reset</button>
                                    <button class="btn btn-primary" type="submit" value="Submit"><i
                                            class="fa fa-filter"></i> Filter</button>
                                    <?php if ($level == "superadmin" || $level == "admin_gudang") : ?>
                                    <button class="btn btn-success" type="submit" id="excel" name="excel"
                                        value="1">Excel</button>
                                    <!-- <a class="btn btn-success" id="excel" name="excel"
                                        href = '<?//= base_url('/book_transaction/generate_excel'); ?>'>Excel</a>
 -->
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?= form_close(); ?>
                    </div>
                    <?php if ($book_transactions) : ?>
                    <table class="table table-striped mb-0 table-responsive">
                        <thead>
                            <tr>
                                <th scope="col" class="pl-4 align-middle text-center">No</th>
                                <th scope="col" style="min-width:350px;" class="align-middle text-center">
                                    Judul</th>
                                <th scope="col" style="min-width:100px;" class="align-middle text-center">
                                    Stok Awal
                                </th>
                                <th scope="col" style="min-width:150px;" class="align-middle text-center">
                                    Perubahan</th>
                                <th scope="col" style="min-width:150px;" class="align-middle text-center">
                                    Jenis Transaksi</th>
                                <th scope="col" style="min-width:100px;" class="align-middle text-center">
                                    Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($book_transactions as $book_transaction) : ?>
                            <?php if ($book_transaction->stock_in) {
                                    $stock_display = $book_transaction->stock_in;
                                    $type_display = "Masuk";
                                }
                                else {
                                    $stock_display = $book_transaction->stock_out;
                                    $type_display = "Keluar";
                                } ?>
                            <tr>
                                <td class="align-middle text-center"><?= ++$i; ?></td>
                                <td class="align-middle">
                                    <!-- Perlu diedit sprtinya,, -->
                                    <a href="<?= base_url('book_transaction/view/' . $book_transaction->book_transaction_id . ''); ?>"
                                        class="font-weight-bold">
                                        <?= highlight_keyword($book_transaction->book_title, $keyword); ?>
                                </td>
                                
                                <td class="align-middle text-center">
                                    <?= $book_transaction->stock_initial; ?>
                                </td>
                                <td class="align-middle text-center">
                                    <?= $stock_display ?>
                                </td>
                                <td class="align-middle text-center">
                                    <?= $type_display ?>
                                </td>
                                <td class="align-middle text-center">
                                    <?= substr($book_transaction->date,0,10); ?>
                                </td>
                            </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                    <?php else : ?>
                    <p class="text-center my-5">Data tidak tersedia</p>
                    <?php endif; ?>
                    <?= $pagination ?? null; ?>
                </div>
            </section>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    $('.dates').flatpickr({
        altInput: true,
        altFormat: 'j F Y',
        dateFormat: 'Y-m-d'
    });
})
</script>