<header class="page-title-bar">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url(); ?>"><span class="fa fa-home"></span></a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url('book_request'); ?>">Permintaan Buku</a>
            </li>
            <li class="breadcrumb-item">
                <a class="text-muted">Form Edit</a>
            </li>
        </ol>
    </nav>
</header>
<div class="page-section">
    <div class="row">
        <div class="col-md-8">
            <section class="card">
                <div class="card-body">
                    <?= form_open_multipart($form_action, 'novalidate="" id="form-book-receive"'); ?>
                    <fieldset>
                        <legend>Form Edit Pemindahan Buku</legend>
                        <?= isset($input->book_transfer_id) ? form_hidden('book_transfer_id', $input->book_transfer_id) : ''; ?>
                        <div class="form-group">
                            <div id="prefetch">
                                <label for="book_title" class="font-weight-bold">Judul buku<abbr
                                        title="Required">*</abbr></label>
                                <span class="form-control d-block bg-secondary"><?=$book_transfer->book_title?></span>
                                <?= form_hidden('book_id', $input->book_id); ?>
                                <?= form_error('book_id'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="quantity">Jumlah Buku<abbr title="Required">*</abbr></label>
                            <?php
                                $form_quantity = array(
                                    'type'  => 'number',
                                    'name'  => 'quantity',
                                    'id'    => 'quantity',
                                    'value' => $input->quantity,
                                    'class' => 'form-control bg-secondary',
                                    'min'   => '0',
                                    'readonly' => true
                                );
                                ?>
                            <?= form_input($form_quantity); ?>
                            <?= form_error('quantity'); ?>
                        </div>
                        <div class="form-group">
                            <label for="destination">Tujuan Pemindahan<abbr title="Required">*</abbr></label>
                            <span class="form-control d-block bg-secondary"><?=get_book_transfer_destination()[$book_transfer->destination]?></span>
                            <?= form_hidden('destination', $input->destination); ?>
                            <?= form_error('destination'); ?>
                        </div>
                        <?php if($input->library_id) :?>
                        <div class="form-group">
                            <label for="library-id">Perpustakaan
                                <abbr title="Required">*</abbr>
                            </label>
                            <span class="form-control d-block bg-secondary"><?=get_dropdown_list_library()[$book_transfer->library_id]?></span>
                            <!-- <input type="text" class="form-control d-block" value="<?=get_dropdown_list_library()[$input->library_id]?>" disabled> -->
                            <?= form_hidden('library_id', get_dropdown_list_library(), $input->library_id); ?>
                            <?= form_error('library_id'); ?>
                        </div>
                        <?php endif?>
                        <div class="form-group">
                            <label for="status">Status
                                <abbr title="Required">*</abbr>
                            </label>
                            <?= form_dropdown('status', get_book_transfer_status(), $input->status, 'id="status" class="form-control custom-select d-block"'); ?>
                            <?= form_error('status'); ?>
                        </div>
                    </fieldset>
                    <hr>
                    <!-- button -->
                    <input type="submit" class="btn btn-primary" value="Submit" />
                    <a class="btn btn-secondary" href="<?php echo base_url('book_transfer') ?>" role="button">Back</a>
                    <?=form_close()?>
                </div>
            </section>
        </div>
    </div>
</div>