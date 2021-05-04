<?php
$level              = check_level();
$per_page           = 10;
$keyword            = $this->input->get('keyword');
$status             = $this->input->get('status');
$type               = $this->input->get('type');
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
                <a class="text-muted">Pesanan Buku</a>
            </li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title"> Pesanan Buku </h1>
            <span class="badge badge-info">Total : <?= $total; ?></span>
        </div>
        <!-- <a href="<?//= base_url("$pages/add"); ?>" class="btn btn-primary btn-sm"><i class="fa fa-plus fa-fw"></i>
            Tambah</a> -->
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
                            <div class="col-12 col-md-4 mb-3">
                                <label for="per_page">Data per halaman</label>
                                <?= form_dropdown('per_page', get_per_page_options(), $per_page, 'id="per_page" class="form-control custom-select d-block" title="List per page"'); ?>
                            </div>
                            <div class="col-12 col-md-4 mb-3">
                                <label for="status">Status</label>
                                <?= form_dropdown('status', get_book_request_status(), $status, 'id="status" class="form-control custom-select d-block" title="Filter Status"'); ?>
                            </div>
                            <div class="col-12 col-md-4 mb-3">
                                <label for="category">Kategori</label>
                                <?= form_dropdown('type', get_book_request_category(), $type, 'id="type" class="form-control custom-select d-block" title="Filter Kategori"'); ?>
                            </div>
                            <div class="col-12 col-md-8">
                                <label for="keyword">Pencarian</label>
                                <?= form_input('keyword', $keyword, 'placeholder="Cari berdasarkan Nomor Order" class="form-control"'); ?>
                            </div>
                            <div class="col-12 col-lg-4">
                                <label>&nbsp;</label>
                                <div class="btn-group btn-block" role="group" aria-label="Filter button">
                                    <button class="btn btn-secondary" type="button"
                                        onclick="location.href = '<?= base_url($pages); ?>'"> Reset</button>
                                    <button class="btn btn-primary" type="submit" value="Submit"><i
                                            class="fa fa-filter"></i> Filter</button>
                                </div>
                            </div>
                        </div>
                        <?= form_close(); ?>
                    </div>
                    <?php if ($book_request) : ?>
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr class="text-center">
                                    <th scope="col" class="pl-4">No</th>
                                    <th scope="col" style="min-width:150px;">Nomor Order</th>
                                    <th scope="col" style="min-width:200px;">Tanggal Pesanan</th>
                                    <th scope="col" style="min-width:200px;">Kategori Pesanan</th>
                                    <th scope="col" style="min-width:200px;">Asal Stok</th>
                                    <th scope="col" style="min-width:200px;">Status</th>
                                    <th style="min-width:100px;"> Aksi </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($book_request as $book_request) : ?>
                                <tr class="text-center">
                                    <td class="align-middle pl-4"><?= ++$i; ?></td>
                                    <td class="text-left align-middle">
                                        <a href="<?= base_url('book_request/view/' . $book_request->invoice_id . ''); ?>"
                                            class="font-weight-bold">
                                            <?= highlight_keyword($book_request->number, $keyword); ?>
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <?= format_datetime($book_request->issued_date); ?>
                                    </td>
                                    <td class="align-middle">
                                        <?= get_book_request_category()[$book_request->type]?>
                                    </td>
                                    <td class="align-middle">
                                        <?= get_book_request_source()[$book_request->source]?>
                                    </td>
                                    <td class="align-middle">
                                        <?= get_book_request_status()[$book_request->status]; ?>
                                    </td>
                                    <td class="align-middle">
                                        <button type="button" class="btn btn-sm btn-secondary" data-toggle="modal"
                                            data-target="#modal-edit-<?= $book_request->invoice_id; ?>"><i
                                                class="fa fa-pencil-alt"></i><span class="sr-only">Edit</span></button>
                                        <div class="text-left">
                                            <div class="modal modal-alert fade"
                                                id="modal-edit-<?= $book_request->invoice_id; ?>" tabindex="-1"
                                                role="dialog"
                                                aria-labelledby="modal-edit-<?= $book_request->invoice_id; ?>"
                                                aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">
                                                                <i class="fa fa-pencil-alt text-black mr-1"></i>
                                                                Edit Status
                                                            </h5>
                                                        </div>
                                                        <form action="<?=base_url('book_request/edit_book_request/')?>" method='post'>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label for="number" class="font-weight-bold">Nomor Pesanan</label>
                                                                    <input type="text" name="number" id="number" class="form-control" value=<?=$book_request->number ?> disabled/>
                                                                    <input type="hidden" name="invoice_id" id="invoice_id" class="form-control" value=<?=$book_request->invoice_id ?>/>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="status" class="font-weight-bold">Status Pesanan</label>
                                                                    <?= form_dropdown('status', get_book_request_status_edit(), $book_request->status, 'id="status" class="form-control custom-select d-block" title="Edit Status"'); ?>
                                                                    <small>Status pesanan sekarang = <?= get_book_request_status()[$book_request->status]; ?></small>
                                                                </div>     
                                                            </div>
                                                            <div class="modal-footer">
                                                                <div class="form-group">
                                                                    <input type="submit" class="btn btn-primary" id="submit"/>
                                                                    <button type="button" class="btn btn-light"
                                                                        data-dismiss="modal">Close</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else : ?>
                    <p class="text-center">Data tidak tersedia</p>
                    <?php endif; ?>
                    <?= $pagination ?? null; ?>
                </div>
            </section>
        </div>
    </div>
</div>