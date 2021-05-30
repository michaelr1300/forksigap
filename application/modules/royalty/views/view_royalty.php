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
<?php $selected_date = date('Y-m-d', strtotime("-1 days"));;
if ($period_end != null) {
    $selected_date = $period_end;
}
$url = '';
if ($period_end == null) $url = '';
else $url = '/' . $period_end;
$month = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
if ($royalty_payment == NULL) {
    $last_paid_date = '2021/01/01';
    $button_label = 'Ajukan Royalti';
} else {
    $last_paid_date = $royalty_payment->last_paid_date;
    if ($royalty_payment->status == 'requested') $button_label = 'Konfirmasi Pembayaran';
    if ($royalty_payment->status == NULL) $button_label = 'Ajukan Royalti';
}
?>
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
                    <div class="form-group">
                        <label
                            for="due-date"
                            class="font-weight-bold"
                        >
                            Tanggal Pembayaran Royalti</label>
                        <div class="input-group mb-3">
                            <input
                                name="due-date"
                                id="due-date"
                                class="form-control dates"
                                value="<?= $selected_date ?>"
                            />
                            <div class="input-group-append">
                                <button
                                    class="btn btn-outline-secondary"
                                    type="button"
                                    id="due_clear"
                                >Clear</button>
                            </div>
                            <div class="input-group-append">
                                <button
                                    class="btn btn-primary"
                                    onclick="filterDate()"
                                ><i class="fa fa-filter"></i> Filter</button>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label
                            for="last-paid-date"
                            class="font-weight-bold"
                        >
                            Pembayaran Royalti Terakhir</label>
                        <input
                            type="text"
                            class="form-control"
                            id="last-paid-date"
                            value="<?= date("d", strtotime($last_paid_date)) . " " . $month[intval(date("m", strtotime($last_paid_date))) - 1] . " " . date("Y", strtotime($last_paid_date)) ?>"
                            readonly
                        />
                    </div>
                    <hr>
                </div>
                <div id="paid_period"></div>
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
                <div id="confirm_notif"></div>
                <button
                    type="button"
                    class="btn btn-primary float-right ml-3"
                    id="pay-royalty"
                ><?= $button_label; ?></button>
                <a
                    href="<?= base_url('royalty/generate_pdf/' . $author->author_id . $url) ?>"
                    class="btn btn-outline-danger float-right"
                    id="btn-generate-pdf"
                    title="Generate PDF"
                >Generate PDF <i class="fas fa-file-pdf fa-fw"></i></a>
                <div
                    class="modal modal-alert fade"
                    id="modal-confirm"
                    tabindex="-1"
                    role="dialog"
                    aria-labelledby="modal-confirm"
                    aria-hidden="true"
                >
                    <div
                        class="modal-dialog"
                        role="document"
                    >
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Konfirmasi Pembayaran</h5>
                            </div>
                            <form
                                id="confirm-royalty"
                                method="post"
                            >
                                <p class="ml-5 mt-3">Yakin Membayar Royalti?</p>
                                <div class="modal-footer">
                                    <button
                                        type="submit"
                                        class="btn btn-primary"
                                    >Save</button>
                                    <button
                                        type="button"
                                        class="btn btn-light"
                                        data-dismiss="modal"
                                    >Close</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script>
$(document).ready(function() {
    <?php if ($button_label == 'Konfirmasi Pembayaran') { ?>
        $('#paid_period').hide();
        var startPeriod = '<?= date("d", strtotime($royalty_payment->last_paid_date)) . " " . $month[intval(date("m", strtotime($royalty_payment->last_paid_date))) - 1] . " " . date("Y", strtotime($royalty_payment->last_paid_date)) ?>'
        var endPeriod = '<?= date("d", strtotime($royalty_payment->last_request_date)) . " " . $month[intval(date("m", strtotime($royalty_payment->last_request_date))) - 1] . " " . date("Y", strtotime($royalty_payment->last_request_date)) ?>'
        $('#confirm_notif').html('<hr><p class="pl-5">Konfirmasi pembayaran royalti untuk periode <b>' + startPeriod + '</b> hingga <b>' + endPeriod + '</b> </p><hr>')
    <?php } ?>
    showPaidPeriod()
    const $flatpickr = $('.dates').flatpickr({
        altInput: true,
        altFormat: 'j F Y',
        dateFormat: 'Y-m-d',
        enableTime: false,
        maxDate: new Date().fp_incr(-1)
    });
    $("#due_clear").click(function() {
        $flatpickr.clear();
    })
    $('#pay-royalty').on("click", function() {
        $('#modal-confirm').modal('toggle')
    })
    $('#confirm-royalty').on("submit", function() {
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
})

function filterDate() {
    location.href = "<?= base_url('royalty/view/' . $author->author_id . '/'); ?>" + $('#due-date').val();
}

function showPaidPeriod() {
    var Month = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"]
    var dueDate = $('#due-date').val().split("-")
    var stringDueDate = dueDate[2] + " " + Month[dueDate[1] - 1] + " " + dueDate[0]
    $('#paid_period').html("<p class='pl-5'>Periode royalti yang akan dibayarkan <b>" + $('#last-paid-date').val() + '</b> hingga <b>' + stringDueDate + '</b></p><hr>')
}

$('#due-date').change(function() {
    showPaidPeriod()
})
</script>
