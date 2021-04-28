<?php
$level              = check_level();
$per_page           = 10;
$keyword            = $this->input->get('keyword');
$date_year          = $this->input->get('date_year');
$period_time        = $this->input->get('period_time');
$page               = $this->uri->segment(2);
$i                  = isset($page) ? $page * $per_page - $per_page : 0;


$date_year_options = [];

$period_time_options = [
    '1' => ' Januari - Juni ',
    '2' => ' Juli - Desember '
];

for ($dy = intval(date('Y')); $dy >= 2015; $dy--) {
    $date_year_options[$dy] = $dy;
}

function royalti_action()
{
    return html_escape('
    <div class="list-group list-group-bordered" style="margin: -9px -15px;border-radius:0;">
      <a href="' . base_url("royalty/index") . '" class="list-group-item list-group-item-action p-2">
        <div class="list-group-item-figure">
        <div class="tile bg-success">
        <span class="fa fa-check"></span>
        </div>
        </div>
        <div class="list-group-item-body"> Sudah Dibayar </div>
      </a>
      <a href="' . base_url("royalty/index") . '" class="list-group-item list-group-item-action p-2">
        <div class="list-group-item-figure">
        <div class="tile bg-danger">
        <span class="fa fa-ban"></span>
        </div>
        </div>
        <div class="list-group-item-body"> Belum Dibayar </div>
      </a>
    </div>
    ');
}

?>

<header class="page-title-bar">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url(); ?>"><span class="fa fa-home"></span></a>
            </li>
            <li class="breadcrumb-item active">
                <a class="text-muted">Royalti</a>
            </li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title"> Royalti </h1>
            <span class="badge badge-info">Total : ??</span>
        </div>
    </div>
</header>
<div class="page-section">
    <div class="row">
        <div class="col-12">
            <section class="card card-fluid">
                <div class="card-body p-0">
                    <div class="p-3">
                        <div
                            class="alert alert-info alert-dismissible fade show"
                            role="alert"
                        >
                            <h5>Info</h5>
                            <p class="m-0">Klik tombol <button class="btn btn-sm btn-secondary"><i class="fa fa-thumbs-up"></i>
                                    Aksi</button> untuk mengubah status penerimaan royalti penulis
                            </p>
                            <button
                                type="button"
                                class="close"
                                data-dismiss="alert"
                                aria-label="Close"
                            >
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <?= form_open($pages, ['method' => 'GET']); ?>
                        <div class="row">
                            <div class="col-12 col-md-4 mt-2">
                                <label for="per_page">Data per halaman</label>
                                <?= form_dropdown('per_page', get_per_page_options(), $per_page, 'id="per_page" class="form-control custom-select d-block" title="List per page"'); ?>
                            </div>
                            <div class="col-12 col-md-4 mt-2">
                                <label for="date_year">Tahun</label>
                                <?= form_dropdown('date_year', $date_year_options, $date_year, 'id="date_year" class="form-control custom-select d-block" title="Filter Tahun Cetak"'); ?>
                            </div>
                            <div class="col-12 col-md-4 mt-2">
                                <label for="period_time">Periode</label>
                                <?= form_dropdown('period_time', $period_time_options, $period_time, 'id="period_time" class="form-control custom-select d-block" title="Periode Royalti"'); ?>
                            </div>
                            <div class="col-12 col-md-8 mt-2">
                                <label for="status">Pencarian</label>
                                <?= form_input('keyword', $keyword, 'placeholder="Cari berdasarkan Nama Penulis, NIP, Institusi" class="form-control"'); ?>
                            </div>
                            <div class="col-12 col-md-4 mt-2">
                                <label>&nbsp;</label>
                                <div
                                    class="btn-group btn-block"
                                    role="group"
                                    aria-label="Filter button"
                                >
                                    <button
                                        class="btn btn-secondary"
                                        type="button"
                                        onclick="location.href = '<?= base_url($pages); ?>'"
                                    > Reset</button>
                                    <button
                                        class="btn btn-primary"
                                        type="submit"
                                        value="Submit"
                                    ><i class="fa fa-filter"></i> Filter</button>
                                </div>
                            </div>
                        </div>
                        <?= form_close(); ?>
                    </div><br>
                    <div class="text-center">
                        <h5>Daftar Penerima Royalti</h5>
                        <h7>Periode $periode_bang_andrew_mulya</h7><br>
                        <h7>Tahun Lahirnya Keqing</h7>
                    </div>
                    <table class="table table-striped mb-0 table-responsive">
                        <thead>
                            <tr class="text-center">
                                <th
                                    scope="col"
                                    style="width:5%;"
                                    class="pl-4"
                                >No</th>
                                <th
                                    scope="col"
                                    style="width:35%;"
                                >Nama</th>
                                <th
                                    scope="col"
                                    style="width:15%;"
                                >NIP</th>
                                <th
                                    scope="col"
                                    style="width:15%;"
                                >Institusi</th>
                                <th
                                    scope="col"
                                    style="width:15%;"
                                >Jumlah Royalti</th>
                                <th
                                    scope="col"
                                    style="width:20%;"
                                    class="pr-4"
                                >Status</th>
                                <th
                                    scope="col"
                                    style="width:20%;"
                                    class="pr-4"
                                > &nbsp; </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="text-center">
                                <td class="align-middle pl-4">
                                    1
                                </td>
                                <td class="text-center align-middle">
                                    <a
                                        href="<?= base_url("$pages/view/"); ?>"
                                        class="font-weight-bold"
                                    >
                                        Abdul
                                    </a>
                                </td>
                                <td class="align-middle">
                                    tes
                                </td>
                                <td class="align-middle">
                                    tes
                                </td>
                                <td class="align-middle">
                                    tes
                                </td>
                                <td class="align-middle">
                                    Sudah Dibayar
                                </td>
                                <td class="align-middle text-right d-flex">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-secondary"
                                        data-container="body"
                                        data-toggle="popover"
                                        data-placement="left"
                                        data-html="true"
                                        data-content="<?= royalti_action(); ?>"
                                        data-trigger="focus"
                                        style="margin-right:5px;"
                                    >
                                        <i class="fa fa-thumbs-up">Aksi</i>
                                    </button>
                                </td>
                            </tr>
                            <tr style="text-align:center;">
                                <td
                                    scope="col"
                                    class="align-middle"
                                    colspan="4"
                                >
                                    <b>Total</b>
                                </td>
                                <td
                                    scope="col"
                                    class="align-middle"
                                    colspan="3"
                                >
                                    <b>Rp 11.000.000</b>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- <?= $pagination ?? null; ?> -->
                </div>
            </section>
        </div>
    </div>
</div>
