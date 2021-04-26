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
                <a href="<?= base_url('invoice'); ?>">Royalti</a>
            </li>
            <li class="breadcrumb-item">
                <a class="text-muted">
                    <!-- <?= $author->author_name ?></a> -->
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
            <?php //=isset($input->draft_id) ? form_hidden('draft_id', $input->draft_id) : ''; ?>
            <div class="tab-content">
                <!-- book-data -->
                <div
                    class="tab-pane fade active show"
                    id="logistic-data"
                >
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered mb-0">
                            <tbody>
                            <tr>
                                <td width="200px"> <?=$this->lang->line('form_author_nip');?> </td>
                                <td><?=$author->author_nip;?> </td>
                            </tr>
                            <tr>
                                <td width="200px"> <?=$this->lang->line('form_author_latest_education');?> </td>
                                <td>
                                    <?=($author->author_latest_education == 's4') ? 'Professor' : ucwords($author->author_latest_education);?>
                                </td>
                            </tr>
                            <tr>
                                <td width="200px"> <?=$this->lang->line('form_work_unit_name');?> </td>
                                <td> <?=konversiID('work_unit', 'work_unit_id', $author->work_unit_id)->work_unit_name;?> </td>
                            </tr>
                            <tr>
                                <td width="200px"> <?=$this->lang->line('form_institute_name');?> </td>
                                <td> <?=konversiID('institute', 'institute_id', $author->institute_id)->institute_name;?> </td>
                            </tr>
                            <tr>
                                <td width="200px"> <?=$this->lang->line('form_author_address');?> </td>
                                <td><?=$author->author_address;?> </td>
                            </tr>
                            <tr>
                                <td width="200px"> <?=$this->lang->line('form_author_contact');?> </td>
                                <td><?=$author->author_contact;?> </td>
                            </tr>
                            <tr>
                                <td width="200px"> <?=$this->lang->line('form_author_email');?> </td>
                                <td><?=$author->author_email;?> </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <hr>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered mb-0">
                            <tbody>
                                <tr>
                                    <td width="200px"> Periode Royalti </td>
                                    <td>Juli - Desember (misalnya)</td>
                                </tr>
                                <tr>
                                    <td width="200px"> Tahun Royalti </td>
                                    <td>Tahun Lahir Keqing</td>
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
                            >Royalti</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $i = 0; ?>
                    <?php foreach ($invoice_books as $invoice_book) : ?>
                    <?php $i++; ?>
                        <tr class="text-center">
                            <td class="align-middle pl-4">
                                <?= $i ?>
                            </td>
                            <td class="text-left align-middle">
                                <!-- <?= $invoice_book->book_title ?> -->
                            </td>
                            <td class="align-middle">
                                Penulis
                            </td>
                            <td class="align-middle">
                                Rp <?= $invoice_book->price * $invoice_book->qty * (1 - $invoice_book->discount/100) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                        <tr style="text-align:center;">
                        <td scope="col"
                            class="align-middle"
                            colspan="3"
                        >
                        <b>Total Royalti</b>
                        </td>
                        <td>
                            <b>Rp 12.000.000</b>
                        </td>
                        </tr>
                    </tbody>
                </table>
				<br>
            </div>
        </div>
    </section>
</div>