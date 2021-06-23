<header class="page-title-bar">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url(); ?>"><span class="fa fa-home"></span></a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url('print_order'); ?>">Order Cetak</a>
            </li>
            <li class="breadcrumb-item">
                <a class="text-muted">Form</a>
            </li>
        </ol>
    </nav>
</header>

<div class="page-section">
    <div class="row">
        <div class="col-md-8">
            <section class="card">
                <div class="card-body">
                    <?= form_open_multipart($form_action, 'novalidate="" id="form-print-order"'); ?>
                    <fielsdet>
                        <legend>Form Order Cetak</legend>
                        <?= isset($input->print_order_id) ? form_hidden('print_order_id', $input->print_order_id) : ''; ?>

                        <div class="form-group">
                            <label for="print-mode">
                                Mode Cetak
                            </label>
                            <?= form_dropdown('print_mode', ['book' => 'Buku', 'nonbook' => 'Non Buku', 'outsideprint' => 'Cetak Di Luar'], $input->print_mode, 'id="print-mode" class="form-control custom-select d-block"'); ?>
                            <?= form_error('print_mode'); ?>
                        </div>

                        <div
                            class="form-group"
                            id="book-id-wrapper"
                        >
                            <label for="book-id">
                                <?= $this->lang->line('form_book_title'); ?>
                                <abbr title="Required">*</abbr>
                            </label>
                            <?= form_dropdown('book_id', get_dropdown_list_book(), $input->book_id, 'id="book-id" class="form-control custom-select d-block"'); ?>
                            <?= form_error('book_id'); ?>
                        </div>

                        <div
                            id="book-info"
                            style="display:none"
                        >
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered mb-0">
                                    <tbody>
                                        <tr>
                                            <td width="175px"> Judul Buku </td>
                                            <td><a
                                                    href=""
                                                    id="info-book-title"
                                                ></a></td>
                                        </tr>
                                        <tr>
                                            <td width="175px"> Halaman Buku </td>
                                            <td id="info-book-pages"></td>
                                        </tr>
                                        <tr>
                                            <td width="175px"> ISBN </td>
                                            <td id="info-isbn"></td>
                                        </tr>
                                        <tr>
                                            <td width="175px"> Tahun Terbit </td>
                                            <td id="info-year"></td>
                                        </tr>
                                        <tr>
                                            <td width="175px"> File Buku </td>
                                            <td>
                                                <a
                                                    title=""
                                                    class="btn btn-success btn-xs my-0"
                                                    target="_blank"
                                                    href=""
                                                    id="info-book-file-link"
                                                ><i class="fa fa-external-link-alt"></i> File Buku</a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <br>
                        </div>

                        <div class="form-group">
                            <label for="deadline_date">
                                Deadline Produksi
                            </label>
                            <div class="input-group mb-3">
                                <?= form_input('deadline_date', $input->deadline_date, 'class="form-control dates"'); ?>
                                <div class="input-group-append">
                                    <button
                                        class="btn btn-outline-secondary"
                                        type="button"
                                        id="deadline_clear"
                                    >Clear</button>
                                </div>
                            </div>
                            <?= form_error('deadline_date'); ?>
                        </div>

                        <div
                            class="form-group"
                            id="name-wrapper"
                            style="display:none"
                        >
                            <label for="name">
                                <?= $this->lang->line('form_print_order_name'); ?>
                            </label>
                            <?= form_input('name', $input->name, 'class="form-control" id="name"'); ?>
                            <small
                                class="text-muted"
                                id="nonbook_example"
                                style="display:none;"
                            >*Contoh : UGMPRESS - Cetak Non Buku</small>
                            <?= form_error('name'); ?>
                        </div>

                        <div class="form-group">
                            <label for="print-order-notes">
                                <?= $this->lang->line('form_print_order_notes'); ?>
                            </label>
                            <?= form_textarea('print_order_notes', $input->print_order_notes, 'class="form-control" id="print-order-notes"'); ?>
                            <?= form_error('print_order_notes'); ?>
                        </div>

                        <div class="form-group">
                            <label for="order-number">
                                <?= $this->lang->line('form_print_order_number'); ?>
                                <abbr title="Required">*</abbr>
                            </label>
                            <?= form_input('order_number', $input->order_number, 'class="form-control" id="order-number"'); ?>
                            <?= form_error('order_number'); ?>
                        </div>

                        <div class="form-group">
                            <label for="order-code">
                                <?= $this->lang->line('form_print_order_code'); ?>
                                <abbr title="Required">*</abbr>
                            </label>
                            <?= form_input('order_code', $input->order_code, 'class="form-control" id="order-code"'); ?>
                            <?= form_error('order_code'); ?>
                        </div>

                        <div class="form-group">
                            <label for="type">
                                <?= $this->lang->line('form_print_order_type'); ?>
                                <abbr title="Required">*</abbr>
                            </label>
                            <?= form_dropdown('type', ['pod' => 'POD', 'offset' => 'Offset'], $input->type, 'id="type" class="form-control custom-select d-block"'); ?>
                            <?= form_error('type'); ?>
                        </div>

                        <div class="form-group">
                            <label for="paper-divider">
                                Faktor Pembagi Kertas
                            </label>
                            <?= form_input('paper_divider', $input->order_number, 'class="form-control" id="paper-divider" list="paper-divider-list"'); ?>
                            <datalist id="paper-divider-list">
                                <option value="1">
                                <option value="2">
                                <option value="4">
                                <option value="8">
                            </datalist>
                            <?= form_error('paper_divider'); ?>
                        </div>

                        <div class="form-group">
                            <label for="total">
                                <?= $this->lang->line('form_print_order_total'); ?>
                                <abbr title="Required">*</abbr>
                            </label>
                            <?php
                            $form_total = array(
                                'type'  => 'number',
                                'name'  => 'total',
                                'id'    => 'total',
                                'value' => $input->total,
                                'class' => 'form-control',
                                'min'   => '0'
                            );
                            ?>
                            <?= form_input($form_total); ?>
                            <?= form_error('total'); ?>
                        </div>

                        <div
                            class="form-group"
                            style="display:none"
                            id="non_book_pages-wrapper"
                        >
                            <label for="non_book_pages">
                                Jumlah Halaman
                                <abbr title="Required">*</abbr>
                            </label>
                            <?php
                            $form_non_book_pages = array(
                                'type'  => 'number',
                                'name'  => 'non_book_pages',
                                'id'    => 'non_book_pages',
                                'value' => $input->non_book_pages,
                                'class' => 'form-control',
                                'min'   => '0'
                            );
                            ?>
                            <?= form_input($form_non_book_pages); ?>
                            <?= form_error('non_book_pages'); ?>
                        </div>

                        <input
                            type="hidden"
                            id="paper-estimation"
                            value=""
                        >

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered mb-0">
                                <tbody>
                                    <tr>
                                        <td width="175px"> Jumlah Kertas </td>
                                        <td id="paper-estimation-info"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <br>

                        <div class="form-group">
                            <label for="paper-content">
                                <?= $this->lang->line('form_print_order_paper_content'); ?>
                                <abbr title="Required">*</abbr>
                            </label>
                            <?= form_input('paper_content', $input->paper_content, 'class="form-control" id="paper-content"'); ?>
                            <?= form_error('paper_content'); ?>
                        </div>

                        <div class="form-group">
                            <label for="paper-cover">
                                <?= $this->lang->line('form_print_order_paper_cover'); ?>
                                <abbr title="Required">*</abbr>
                            </label>
                            <?= form_input('paper_cover', $input->paper_cover, 'class="form-control" id="paper-cover"'); ?>
                            <?= form_error('paper_cover'); ?>
                        </div>

                        <div class="form-group">
                            <label for="paper-size">
                                <?= $this->lang->line('form_print_order_paper_size'); ?>
                                <abbr title="Required">*</abbr>
                            </label>
                            <?= form_input('paper_size', $input->paper_size, 'class="form-control" id="paper-size"'); ?>
                            <?= form_error('paper_size'); ?>
                        </div>

                        <div class="form-group">
                            <label for="location-binding">
                                Lokasi Jilid
                            </label>
                            <?= form_dropdown('location_binding', ['inside' => 'Internal', 'outside' => 'External', 'partial' => 'Parsial'], $input->location_binding, 'id="location-binding" class="form-control custom-select d-block"'); ?>
                            <?= form_error('location_binding'); ?>
                        </div>

                        <div
                            class="form-group"
                            style="display:none"
                            id="location-binding-outside-wrapper"
                        >
                            <label for="location-binding-outside">
                                Lokasi Jilid Di Luar
                            </label>
                            <?= form_input('location_binding_outside', $input->location_binding_outside, 'class="form-control" id="location-binding-outside"'); ?>
                            <?= form_error('location_binding_outside'); ?>
                        </div>

                        <div class="form-group">
                            <label for="location-laminate">
                                Lokasi Laminasi
                            </label>
                            <?= form_dropdown('location_laminate', ['inside' => 'Internal', 'outside' => 'External', 'partial' => 'Parsial'], $input->location_laminate, 'id="location-laminate" class="form-control custom-select d-block"'); ?>
                            <?= form_error('location_laminate'); ?>
                        </div>

                        <div
                            class="form-group"
                            style="display:none"
                            id="location-laminate-outside-wrapper"
                        >
                            <label for="location-laminate-outside">
                                Lokasi Laminasi Di Luar
                            </label>
                            <?= form_input('location_laminate_outside', $input->location_laminate_outside, 'class="form-control" id="location-laminate-outside"'); ?>
                            <?= form_error('location_laminate_outside'); ?>
                        </div>

                        <div class="form-group">
                            <label for="print-order-file"><?= $this->lang->line('form_print_order_file'); ?></label>
                            <div class="custom-file">
                                <?= form_upload('print_order_file', '', 'class="custom-file-input" id="print-order-file"'); ?>
                                <label
                                    class="custom-file-label"
                                    for="print-order-file"
                                >Pilih file</label>
                            </div>
                            <small class="form-text text-muted">Menerima tipe file :
                                <?= get_allowed_file_types('print_order_file')['to_text']; ?></small>
                            <small class="text-danger"><?= $this->session->flashdata('print_order_file_no_data'); ?></small>
                            <?= file_form_error('print_order_file', '<p class="small text-danger">', '</p>'); ?>
                        </div>
                        </fieldset>
                        <hr>
                        <div class="form-actions">
                            <button
                                class="btn btn-primary ml-auto"
                                type="submit"
                                value="Submit"
                                id="btn-submit"
                            >Submit</button>
                        </div>
                        <?= form_close(); ?>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $("#book-id").select2({
        placeholder: '-- Pilih --',
        dropdownParent: $('#app-main')
    });

    loadValidateSetting();
    $("#form-print-order").validate({
            rules: {
                book_id: "crequired",
                name: "crequired",
                category: "crequired",
                order_number: "crequired",
                order_code: "crequired",
                type: "crequired",
                mode: "crequired",
                total: {
                    crequired: true,
                    cnumber: true
                },
                paper_content: "crequired",
                paper_cover: "crequired",
                paper_size: "crequired",
            },
            errorElement: "span",
            errorPlacement: validateErrorPlacement,
        },
        validateSelect2()
    );

    handleCategoryChange($('#print-mode').val())

    $('#print-mode').change(function(e) {
        handleCategoryChange(e.target.value)
    })

    function handleCategoryChange(category) {
        if (category === 'nonbook') {
            $('#book-id-wrapper').hide()
            $('#name-wrapper').show()
            $('#nonbook_example').show()
            $('#book-id').val('');
            $('#non_book_pages-wrapper').show()
        } else {
            $('#book-id-wrapper').show()
            $('#name-wrapper').hide()
            $('#nonbook_example').hide()
            $('#name').val('')
            $('#non_book_pages-wrapper').hide()
            $('#non_book_pages').val('')
            $('#paper-estimation').val('')
        }
    }

    $('#book-id').change(function(e) {
        const bookId = e.target.value
        console.log(bookId)

        $.ajax({
            type: "GET",
            url: "<?= base_url('print_order/api_get_book/'); ?>" + bookId,
            datatype: "JSON",
            success: function(res) {
                console.log(res);
                var published_date = new Date(res.data.published_date);
                $('#print-mode').change(function(e) {
                    if ($('#print-mode').val() == 'nonbook') {
                        $('#book-info').hide();
                        $('#info-paper-required').hide();
                    } else {
                        $('#book-info').show();
                    }
                });
                $('#book-info').show()
                $('#info-book-title').html(res.data.book_title)
                $('#info-book-title').attr("href", "<?= base_url('book/view/'); ?>" + bookId)
                $('#info-book-pages').html(res.data.book_pages)
                $('#info-isbn').html(res.data.isbn)
                $('#info-year').html(published_date.getFullYear())
                $('#info-book-file-link').attr("href", "" + res.data.book_file_link)
                $('#info-book-file-link').attr("title", "" + res.data.book_title)

                if (res.data.from_outside == 0) {
                    $('#name').val('');
                    $('#name-wrapper').hide()
                } else {
                    $('#name-wrapper').show()
                }
            },
            error: function(err) {
                console.log(err);
            },
        });
    })

    $("#total,#paper-divider,#book-id,#non_book_pages").change(function(halaman) {
        if ($('#print-mode').val() != 'nonbook') {
            $("#paper-estimation").val(($("#total").val() * $("#info-book-pages").text()) / $("#paper-divider").val())
            $("#paper-estimation-info").html($("#paper-estimation").val())
        } else {
            $("#paper-estimation").val(($("#total").val() * $("#non_book_pages").val()) / $("#paper-divider").val())
            $("#paper-estimation-info").html($("#paper-estimation").val())
        }
    });


    $('#location-binding').change(function(e) {
        if ($('#location-binding').val() != 'inside') {
            $('#location-binding-outside-wrapper').show();
        } else {
            $('#location-binding-outside').val('');
            $('#location-binding-outside-wrapper').hide();
        }
    })

    $('#location-laminate').change(function(e) {
        if ($('#location-laminate').val() != 'inside') {
            $('#location-laminate-outside-wrapper').show();
        } else {
            $('#location-laminate-outside').val('');
            $('#location-laminate-outside-wrapper').hide();
        }
    })

    const $flatpickr = $('.dates').flatpickr({
        altInput: true,
        altFormat: 'j F Y H:i:S',
        dateFormat: 'Y-m-d H:i:S',
        enableTime: true
    });

    $("#deadline_clear").click(function() {
        $flatpickr.clear();
    })
})
</script>
