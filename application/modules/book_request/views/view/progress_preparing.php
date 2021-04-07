<?php
$is_preparing_started       = format_datetime($book_request->preparing_start_date);
$is_preparing_finished      = format_datetime($book_request->preparing_end_date);
$is_preparing_deadline_set  = format_datetime($book_request->preparing_deadline);
$staff_gudang               = $this->book_request->get_staff_gudang_by_progress('preparing', $book_request->book_request_id);
?>
<section id="preparing-progress-wrapper" class="card">
    <div id="preparing-progress">
        <header class="card-header">
            <div class="d-flex align-items-center"><span class="mr-auto">Penyiapan Buku</span>
                
                <div class="card-header-control">
                    <button id="btn-start-preparing" title="Mulai proses preparing" type="button" class="d-inline btn 
                        <?= !$is_preparing_started ? 'btn-warning' : 'btn-secondary'; ?> <?= ($is_preparing_started || !$is_preparing_deadline_set) ? 'btn-disabled' : ''; ?>
                        " <?= ($is_preparing_started || !$is_preparing_deadline_set) ? 'disabled' : ''; ?>><i
                            class="fas fa-play"></i><span class="d-none d-lg-inline"> Mulai</span></button>
                    <button id="btn-finish-preparing" title="Selesai proses preparing" type="button"
                        class="d-inline btn btn-secondary <?= !$is_preparing_started ? 'btn-disabled' : '' ?>"
                        <?= !$is_preparing_started ? 'disabled' : '' ?>><i class="fas fa-stop"></i><span
                            class="d-none d-lg-inline"> Selesai</span></button>
                </div>
                <?php ?>
            </div>
        </header>

        <!-- ALERT -->
        <?php 
            $this->load->view('book_request/view/common/progress_alert', [
                'progress'          => 'preparing',
                // 'staff_gudang'  => $staff_gudang
            ]);
            ?>

        <div class="list-group list-group-flush list-group-bordered" id="list-group-preparing">
            <div class="list-group-item justify-content-between">
                <span class="text-muted">Status</span>
                <span class="font-weight-bold">
                    <?php if ($book_request->is_preparing) : ?>
                    <span class="text-success">
                        <i class="fa fa-check"></i>
                        <span>Selesai</span>
                    </span>
                   
                    <?php elseif (!$book_request->is_preparing && $book_request->preparing_start_date) : ?>
                    <span class="text-primary">
                        <span>Sedang Diproses</span>
                    </span>
                    <?php endif ?>
                </span>
            </div>

            <div class="list-group-item justify-content-between">
                <span class="text-muted">Tanggal mulai</span>
                <strong>
                    <?= format_datetime($book_request->preparing_start_date); ?></strong>
            </div>

            <div class="list-group-item justify-content-between">
                <span class="text-muted">Tanggal selesai</span>
                <strong>
                    <?= format_datetime($book_request->preparing_end_date); ?></strong>
            </div>

            <div class="list-group-item justify-content-between">
                <?php if (($_SESSION['level'] == 'superadmin' || ($_SESSION['level'] == 'admin_gudang' && empty($book_request->preparing_deadline)))) : ?>
                <a href="#" id="btn-modal-deadline-preparing" title="Ubah deadline" data-toggle="modal"
                    data-target="#modal-deadline-preparing">Deadline <i class="fas fa-edit fa-fw"></i></a>
                <?php else : ?>
                <span class="text-muted">Deadline</span>
                <?php endif ?>
                <strong><?= format_datetime($book_request->preparing_deadline); ?></strong>
            </div>

            <div class="m-3">
                <div class="text-muted pb-1">Catatan Admin</div>
                <?= $book_request->preparing_notes_admin ?>
            </div>

            <hr class="m-0">
        </div>

        <div class="card-body">
            <div class="card-button">
                <!-- button aksi -->
                <?php if (($_SESSION['level'] == 'superadmin' || $_SESSION['level'] == 'admin_gudang')) : ?>
                <button title="Aksi admin"
                    class="btn btn-outline-dark <?= !$book_request->preparing_end_date ? 'btn-disabled' : ''; ?>" data-toggle="modal"
                    data-target="#modal-action-preparing" <?= !$book_request->preparing_end_date ? 'disabled' : ''; ?>>Aksi</button>
                <?php endif; ?>

                <!-- button tanggapan preparing -->
                <button type="button" class="btn btn-outline-success" data-toggle="modal"
                    data-target="#modal-preparing-notes">Catatan</button>

            </div>
        </div>

        <?php    

            // modal deadline
            $this->load->view('book_request/view/common/deadline_modal', [
                'progress' => 'preparing',
            ]);

            // modal action
            $this->load->view('book_request/view/common/action_modal', [
                'progress' => 'preparing',
            ]);

            // modal note
            $this->load->view('book_request/view/common/notes_modal', [
                'progress' => 'preparing',
            ]);
            ?>
    </div>
</section>

<script>
$(document).ready(function() {
    const book_request_id = '<?= $book_request->book_request_id ?>'

    // inisialisasi segment
    reload_preparing_segment()

    // ketika load segment, re-initialize call function-nya
    function reload_preparing_segment() {
        $('#preparing-progress-wrapper').load(' #preparing-progress', function() {
            // reinitiate modal after load
            initFlatpickrModal()
        });
    }

    // mulai penyiapan buku
    $('#preparing-progress-wrapper').on('click', '#btn-start-preparing', function() {
        $.ajax({
            type: "POST",
            url: "<?= base_url('book_request/api_start_progress/'); ?>" + book_request_id,
            datatype: "JSON",
            data: {
                progress: 'preparing'
            },
            success: function(res) {
                showToast(true, res.data);
            },
            error: function(err) {
                showToast(false, err.responseJSON.message);
            },
            complete: function() {
                // reload segmen preparing
                reload_preparing_segment()
                // reload progress
                $('#progress-list-wrapper').load(' #progress-list');
                // reload data 
                $('#book-request-data-wrapper').load(' #book-request');
            },
        })
    })

    // selesai penyiapan
    $('#preparing-progress-wrapper').on('click', '#btn-finish-preparing', function() {
        $.ajax({
            type: "POST",
            url: "<?= base_url('book_request/api_finish_progress/'); ?>" + book_request_id,
            datatype: "JSON",
            data: {
                progress: 'preparing'
            },
            success: function(res) {
                showToast(true, res.data);
            },
            error: function(err) {
                showToast(false, err.responseJSON.message);
            },
            complete: function() {
                // reload segmen preparing
                reload_preparing_segment()
                // reload progress
                $('#progress-list-wrapper').load(' #progress-list');
                // reload data
                $('#book-request-data-wrapper').load(' #book-request');
            },
        })
    })
})
</script>