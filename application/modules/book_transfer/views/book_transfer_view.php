<?php
$level              = check_level();
?>
<!-- BREADCUMB,TITLE -->
<header class="page-title-bar mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url(); ?>"><span class="fa fa-home"></span></a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url('book_transfer'); ?>">Pemindahan Buku</a>
            </li>
            <li class="breadcrumb-item">
                <a class="text-muted"><?= $book_transfer->book_title; ?></a>
            </li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center my-3">
        <div class="page-title mb-0 pb-0 h1"> Pemindahan Buku </div>
        <a href="<?= base_url('book_request/edit/'.$book_transfer->book_transfer_id); ?>"
            class="btn btn-secondary btn-sm"><i class="fa fa-edit fa-fw"></i> Edit Pemindahan Buku</a>
    </div>
</header>
<!-- BREADCUMB,TITLE -->

<!-- DETAIL -->
<section class="card" id="detail_book_transfer">
    <header class="card-header">
        <ul class="nav nav-tabs card-header-tabs">
            <li class="nav-item">
                <a class="nav-link active show" data-toggle="tab" href="#detail_info_wrapper"><i
                        class="fa fa-info-circle"></i> Detail Pemindahan Buku</a>
            </li>
        </ul>
    </header>

    <div class="card-body">
        <div class="tab-content">
            <!-- DATA INFO -->
            <div id="detail_info_wrapper" class="tab-pane fade active show">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered mb-0 nowrap">
                        <tbody>
                            <tr>
                                <td width="200px"> Judul Buku </td>
                                <td>
                                    <?= $book_transfer->book_title;?>
                                </td>
                            </tr>
                            <tr>
                                <td width="200px"> Jumlah </td>
                                <td> <?= $book_transfer->quantity?>
                                </td>
                            </tr>
                            <tr>
                                <td width="200px"> Tanggal Pindah </td>
                                <td>
                                </td>
                            </tr>
                            <tr>
                                <td width="200px"> Tujuan Pemindahan </td>
                                <td> <?= $book_transfer->destination?>
                                </td>
                            </tr>
                            <?php if ($book_transfer->destination == 'library') : ?>
                            <tr>
                                <td width="200px"> Nama Perpustakaan </td>
                                <td> <?= $book_transfer->library_name?>
                                </td>
                            </tr>
                            <?php endif?>
                            <tr>
                                <td width="200px"> Status </td>
                                <td>
                                    <!-- <?//= get_book_request_status()[$book_transfer->transfer_status ?? $book_transfer->status]?> -->
                                    <?= $book_transfer->status?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- DATA INFO -->
        </div>
    </div>
</section>
<!-- DETAIL -->

<!-- PROGRESS -->
<hr class="my-3">
<?php
// $progress_list = ['permintaan', 'final'];

// $permintaan_class = '';
// $permintaan_title = '';
// if($rData->request_status == 0 && $rData->flag == 0){
//     $pracetak_title = 'Belum mulai';
// }elseif($rData->request_status == 1 && $rData->flag == 0){
//     $permintaan_class .= 'active ';
//     $permintaan_title = 'Dalam Proses';
// }elseif($rData->request_status == 2 && $rData->flag == 1){
//     $permintaan_class .= 'error ';
//     $permintaan_title = 'Ditolak';
// }elseif($rData->request_status == 2 && $rData->flag == 2){
//     $permintaan_class .= 'success ';
//     $permintaan_title = 'Selesai';
// }

// $final_class = '';
// $final_title = '';
// if($rData->final_status == 0 && $rData->flag == 0){
//     $final_title = 'Belum mulai';
// }elseif($rData->final_status == 1 && $rData->flag == 0){
//     $final_class .= 'active ';
//     $final_title = 'Dalam Proses';
// }elseif($rData->final_status == 2 && $rData->flag == 1){
//     $final_class .= 'error ';
//     $final_title = 'Ditolak';
// }elseif($rData->final_status == 2 && $rData->flag == 2){
//     $final_class .= 'success ';
//     $final_title = 'Selesai';
// }
?>

<!-- <section id="progress-list-wrapper" class="card">
    <div id="progress-list">
        <header class="card-header">Progress</header>
        <div class="card-body">
            <ol class="progress-list mb-0 mb-sm-4">

                <?php// foreach ($progress_list as $progress) : ?>
                <li class="<?//= ${"{$progress}_class"} ?>">
                    <button data-html="true" type="button" data-toggle="tooltip" title="<?//= ${"{$progress}_title"}; ?>">
                        <span width="300px" class="progress-indicator"></span>
                    </button>
                    <span class="progress-label d-none d-sm-inline-block"><?//= $progress; ?></span>
                </li>
                <?php //endforeach; ?>
            </ol>
        </div>
    </div>
</section>
<hr class="my-3"> -->
<!-- REQUEST -->
<?php
    // $this->load->view('book_request/view/progress_preparing');
?>
<!-- FINAL -->
<?php
    // if($book_request->request_status == 2 && $book_request->flag == 2 && $book_request->final_status == 1 && ($level == 'superadmin' || $level == 'admin_gudang')){
    //     $this->load->view('book_request/view/progress_final');
    // }//load progres final ketika request sudah
    // elseif($book_request->request_status == 2 && $book_request->flag == 2 && $book_request->final_status == 2){
    //     $this->load->view('book_request/view/summary');
    // }//load summary ketika final telah di proses
    // elseif($book_request->request_status == 2 && $book_request->flag == 1){
    //     $this->load->view('book_request/view/summary');
    // }
    //load summary ketika permintaan ditolak
?>
<!-- FINAL -->