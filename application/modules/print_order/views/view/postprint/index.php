<section
    id="postprint-progress-wrapper"
    class="card"
>
    <div id="postprint-progress">
        <header class="card-header">
            <div class="d-flex align-items-center"><span class="mr-auto">Jilid</span>
                <div class="card-header-control">
                    <button
                        id="btn-start-postprint"
                        title="Mulai proses pra cetak"
                        type="button"
                        class="d-inline btn btn-secondary"
                    ><i class="fas fa-play"></i><span class="d-none d-lg-inline"> Mulai</span></button>
                    <button
                        id="btn-finish-edit"
                        title="Selesai proses editorial"
                        type="button"
                        class="d-inline btn btn-secondary"
                    ><i class="fas fa-stop"></i><span class="d-none d-lg-inline"> Selesai</span></button>
                </div>
            </div>
        </header>

        <div
            class="list-group list-group-flush list-group-bordered"
            id="list-group-postprint"
        >
            <div class="list-group-item justify-content-between">
                <span class="text-muted">Status</span>
                <span class="font-weight-bold">
                    <?php if ($print_order->is_postprint == 'y') : ?>
                        <span class="text-success">
                            <i class="fa fa-check"></i>
                            <span>Pracetak Selesai</span>
                        </span>
                    <?php else : ?>
                        <span class="text-primary">
                            <span>Sedang Diproses</span>
                        </span>
                    <?php endif ?>
                </span>
            </div>

            <div class="list-group-item justify-content-between">
                <span class="text-muted">Tanggal mulai</span>
                <strong>
                    <?= format_datetime($print_order->postprint_start_date); ?></strong>
            </div>

            <div class="list-group-item justify-content-between">
                <span class="text-muted">Tanggal selesai</span>
                <strong>
                    <?= format_datetime($print_order->postprint_end_date); ?></strong>
            </div>

            <div class="list-group-item justify-content-between">
                <?php if (is_admin()) : ?>
                    <a
                        href="#"
                        id="btn-modal-deadline-postprint"
                        title="Ubah deadline"
                        data-toggle="modal"
                        data-target="#modal-deadline-postprint"
                    >Deadline <i class="fas fa-edit fa-fw"></i></a>
                <?php else : ?>
                    <span class="text-muted">Deadline</span>
                <?php endif ?>
            </div>

            <hr class="m-0">
        </div>

        <div class="card-body">
            <div class="card-button">
                <!-- button tanggapan edit -->
                <button
                    type="button"
                    class="btn btn-outline-success"
                    data-toggle="modal"
                    data-target="#modal-postprint"
                >Catatan</button>
            </div>
        </div>
    </div>
</section>
