<?php
$level          = check_level();
$startDate      = $this->input->get('start_date');
$endDate        = $this->input->get('end_date');
?>

<header class="page-title-bar mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url(); ?>"><span class="fa fa-home"></span></a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url('royalty'); ?>">Royalti</a>
            </li>
            <li class="breadcrumb-item">
                <a class="text-muted">
                    <?= $author->author_name ?></a>
            </li>
        </ol>
    </nav>
</header>
<div class="page-section">
    <section
        id="data-invoice"
        class="card"
    >
        <div class="card-body">
            <div class="tab-content">
                <div class="text-center">
                    <h4>Detail Royalti</h4>
                    <h6><?= $author->author_name ?></h6>
                    <p>Periode <b><?= $royalty->start_date ?></b> hingga <b><?= $royalty->end_date ?></b></p>
                </div>
                <div class="my-4">
                    <p>Status: <b><?= $royalty->status ?></b></p>
                </div>
                <hr>
                <table class="table table-striped mb-0">
                    <thead>
                        <tr class="text-center">
                            <th
                                scope="col"
                                style="width:2%;"
                            >No</th>
                            <th
                                scope="col"
                                style="width:30%;"
                            >Judul Buku</th>
                            <th
                                scope="col"
                                style="width:15%;"
                            >Jumlah Buku Terjual</th>
                            <th
                                scope="col"
                                style="width:15%;"
                            >Penjualan</th>
                            <th
                                scope="col"
                                style="width:15%;"
                            >Royalti</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $index = 0;
                        $total_sales = 0;
                        $total_royalty = 0; ?>
                        <?php foreach ($royalty_details as $royalty) : ?>
                            <tr>
                                <td class="text-center"><?= $index + 1; ?></td>
                                <td class="text-left"><?= $royalty->book_title; ?></td>
                                <td class="text-center"><?= $royalty->count; ?></td>
                                <td class="text-right pr-5">Rp <?= $royalty->total_sales; ?></td>
                                <td class="text-right pr-5">Rp <?= round($royalty->earned_royalty, 0); ?></td>
                            </tr>
                            <?php $index++;
                            $total_sales += $royalty->total_sales;
                            $total_royalty += $royalty->earned_royalty; ?>
                        <?php endforeach; ?>
                        <tr style="text-align:center;">
                            <td
                                scope="col"
                                class="align-middle"
                                colspan="3"
                            >
                                <b>Total</b>
                            </td>
                            <td class="text-right pr-5">
                                <b>Rp <?= $total_sales; ?></b>
                            </td>
                            <td class="text-right pr-5">
                                <b>Rp <?= $total_royalty; ?></b>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <a
                    type="button btn-success"
                    class="btn btn-primary float-right mr-3 mt-3"
                    href="<?= base_url('royalty/view/' . $author->author_id) ?>"
                >Kembali</a>
            </div>
        </div>
    </section>
</div>
<script>
$(document).ready(function() {
    <?php $month = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    if ($startDate == "") {
        $startDate = '1 Januari 2021';
    } else {
        $startDate = date("d", strtotime($startDate)) . " " . $month[intval(date("m", strtotime($startDate))) - 1] . " " . date("Y", strtotime($startDate));
    } ?>
    <?php if ($endDate == "") {
        $endDate = date("Y/m/d", strtotime("-1 day"));
        $endDate = date("d", strtotime($endDate)) . " " . $month[intval(date("m", strtotime($endDate))) - 1] . " " . date("Y", strtotime($endDate));
    } else {
        $endDate = date("d", strtotime($endDate)) . " " . $month[intval(date("m", strtotime($endDate))) - 1] . " " . date("Y", strtotime($endDate));
    } ?>
    $('#period_exp').html("Menampilkan seluruh royalti pada periode <b><?= $startDate ?></b> hingga <b><?= $endDate ?></b>")
})
</script>
