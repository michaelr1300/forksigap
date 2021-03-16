<header class="page-title-bar mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url(); ?>"><span class="fa fa-home"></span></a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url('book_receive'); ?>">Penerimaan Buku</a>
            </li>
            <li class="breadcrumb-item">
                <a class="text-muted">
                    <?= $book_receive->book_title; ?>
                </a>
            </li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center my-3">
        <div class="page-title mb-0 pb-0 h1"> Penerimaan Buku </div>
        <div>
            <?php if (!$is_final && $_SESSION['level'] == 'superadmin') : 
            ?>
            <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal"
                data-target="#modal-additional-notes">
                <i class="fa fa-edit fa-fw"></i> Edit Penerimaan Buku</button>
            <div class="modal fade" id="modal-additional-notes" tabindex="-1" role="dialog"
                aria-labelledby="modal-additional-notes" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-overflow" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"> Edit Penerimaan Buku</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <fieldset>
                                <div class="form-group">
                                    <?//= form_open('book_receive/add_additional_notes/' . $book_receive->book_receive_id, ''); ?>
                                    <?php /*
                                        echo form_textarea([
                                            'name'  => "additional_notes",
                                            'class' => 'form-control',
                                            'id'    => "additional-notes",
                                            'rows'  => '6',
                                            'value' => $book_receive->additional_notes
                                        ]);  */
                                    ?>
                                </div>
                            </fieldset>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light ml-auto" data-dismiss="modal">Close</button>
                            <?php //if (!$is_final) : 
                            ?>
                            <button class="btn btn-primary" type="submit" value="Submit"
                                id="btn-submit-additional-notes">Submit</button>
                            <?= form_close(); ?>
                            <?php //endif 
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; 
            ?>
        </div>
    </div>

    <!-- FINAL ALERT -->
    <?php if ($is_final) : 
    ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fa fa-exclamation-triangle"></i>
        <strong>Penerimaan buku telah selesai</strong>, data progress tidak dapat diubah.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php endif 
    ?>
</header>

<div class="page-section">
    <?php
    $this->load->view('book_receive/view/detail');
    echo '<hr>';
    $this->load->view('book_receive/view/progress');
    if ($_SESSION['level'] == 'superadmin' || $_SESSION['level'] == 'admin_gudang') {
        echo '<hr>';
        $this->load->view('book_receive/view/handover');
        if ($book_receive->is_handover) {
            echo '<hr>';
            $this->load->view('book_receive/view/wrapping');
            if($book_receive->is_wrapping){
                echo '<hr>';
                $this->load->view('book_receive/view/final');
            }
        }
    }
    ?>
</div>