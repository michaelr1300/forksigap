<?php
$level              = check_level();
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
                <!-- book-data -->
                <div
                    class="tab-pane fade active show"
                    id="logistic-data"
                >
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered mb-0">
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    <hr>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered mb-0">
                            <tbody>
                                <?php $url = '';
                                if ($period_end == null) $url = '';
                                else $url = '/' . $period_end; ?>
                                <tr>
                                    <td width="200px"> Periode Royalti </td>
                                    <td>test</td>
                                </tr>
                                <tr>
                                    <td width="200px"> Tahun Royalti </td>
                                    <td>test</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <hr>
                </div>

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
                                style="width:10%;"
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
                        $total_earning = 0;
                        $total_royalty = 0; ?>
                        <?php foreach ($royalty_details as $royalty) : ?>
                            <tr>
                                <td class="text-center"><?= $index + 1; ?></td>
                                <td class="text-left"><?= $royalty->book_title; ?></td>
                                <td class="text-center"><?= $royalty->count; ?></td>
                                <td class="text-right pr-5">Rp <?= $royalty->penjualan; ?></td>
                                <td class="text-right pr-5">Rp <?= round($royalty->earned_royalty, 0); ?></td>
                            </tr>
                            <?php $index++;
                            $total_earning += $royalty->penjualan;
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
                                <b>Rp <?= $total_earning; ?></b>
                            </td>
                            <td class="text-right pr-5">
                                <b>Rp <?= $total_royalty; ?></b>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <button
                    type="button"
                    class="btn btn-primary float-right ml-3"
                    id="pay-royalty"
                >Bayar Royalti</button>
                <a
                    href="<?= base_url('royalty/generate_pdf/' . $author->author_id . $url) ?>"
                    class="btn btn-outline-danger float-right"
                    id="btn-generate-pdf"
                    title="Generate PDF"
                >Generate PDF <i class="fas fa-file-pdf fa-fw"></i></a>
            </div>
        </div>
    </section>
</div>
<script>
$('#pay-royalty').on("click", function() {
    var paid_date = "<?= $period_end; ?>"
    if (paid_date == "") paid_date = new Date().toISOString().slice(0, 10)
    $.ajax({
        type: "POST",
        url: "<?= base_url("royalty/pay"); ?>",
        data: {
            paid_date: paid_date,
            author_id: "<?= $author->author_id ?>"
        },
        success: function(result) {
            var response = $.parseJSON(result)
            //Validation Error
            if (response.status != true) {
                $(".error-message").addClass('d-none');
                for (var i = 0; i < response.input_error.length; i++) {
                    // Show error message
                    $('#' + response.input_error[i]).removeClass('d-none');
                }
            } else {
                location.href = "<?= base_url('royalty'); ?>";
            }
        },
        error: function(req, err) {
            console.log(err)
        }
    });
})
</script>
