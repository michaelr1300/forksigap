<header class="page-title-bar">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url(); ?>"><span class="fa fa-home"></span></a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url('book_receive'); ?>">Penerimaan Buku</a>
            </li>
            <li class="breadcrumb-item">
                <a class="text-muted">Form Edit Penerimaan Buku</a>
            </li>
        </ol>
    </nav>
</header>
<div class="page-section">
    <div class="row">
        <div class="col-md-8">
            <section class="card">
                <div class="card-body">
                    <form method="post" action='<?=base_url('book_receive/update/' . $book_receive->book_receive_id)?>' id="form-book-receive">
                    <fieldset>
                        <legend>Form Edit Penerimaan Buku</legend>
                        <?= isset($book_receive->book_receive_id) ? form_hidden('book_receive_id', $book_receive->book_receive_id) : ''; ?>
                        <div class="form-group">
                            <label for="entry_date">
                                <?= $this->lang->line('form_book_receive_entry_date')?>
                            </label>
                            <?= form_input('entry_date', $book_receive->entry_date, 'class="form-control dates"')?>
                            <?= form_error('entry_date'); ?>
                        </div>
                        <div class="form-group">
                            <label for="deadline">
                                <?= $this->lang->line('form_book_receive_deadline')?>
                            </label>
                            <?= form_input('deadline', $book_receive->deadline, 'class="form-control dates"')?>
                            <?= form_error('deadline'); ?>
                        </div>
                        <div class="form-group">
                            <label for="finish_date">
                                <?= $this->lang->line('form_book_receive_finish_date')?>
                            </label>
                            <?= form_input('finish_date', $book_receive->finish_date, 'class="form-control dates"')?>
                            <?= form_error('finish_date'); ?>
                        </div>
                        <hr>
                        <h5 class="card-title">Serah Terima</h5>
                        <div class="form-group">
                            <label>Status Serah Terima</label>
                            <div class="mb-1">
                                <label>
                                    <?= form_radio('is_handover', 1, isset($book_receive->is_handover) && ($book_receive->is_handover == 1) ? true : false); ?>
                                    <i class="fa fa-check text-success"></i> Sudah selesai serah terima
                                </label>
                            </div>
                            <div class="mb-1">
                                <label>
                                    <?= form_radio('is_handover', 0, isset($book_receive->is_handover) && ($book_receive->is_handover == 0) ? true : false); ?>
                                    <i class="fa fa-times text-danger"></i> Belum selesai serah terima
                                </label>
                            </div>
                            <?= form_error('is_handover'); ?>
                        </div>
                        <div class="form-group">
                            <label for="handover_start_date">Tanggal Mulai Serah Terima
                            </label>
                            <div class="has-clearable">
                                <button type="button" class="close" aria-label="Close">
                                    <span aria-hidden="true">
                                        <i class="fa fa-times-circle"></i>
                                    </span>
                                </button>
                                <?= form_input('handover_start_date', $book_receive->handover_start_date, 'class="form-control dates"'); ?>
                                <?= form_error('handover_start_date'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="handover_end_date">Tanggal Selesai Serah Terima
                            </label>
                            <div class="has-clearable">
                                <button type="button" class="close" aria-label="Close">
                                    <span aria-hidden="true">
                                        <i class="fa fa-times-circle"></i>
                                    </span>
                                </button>
                                <?= form_input('handover_end_date', $book_receive->handover_end_date, 'class="form-control dates" '); ?>
                                <?= form_error('handover_end_date'); ?>
                            </div>
                        </div>
                        <hr>
                        <h5 class="card-title">Wrapping</h5>
                        <div class="form-group">
                            <label>Status Wrapping</label>
                            <div class="mb-1">
                                <label>
                                    <?= form_radio('is_wrapping', 1, isset($book_receive->is_wrapping) && ($book_receive->is_wrapping == 1) ? true : false); ?>
                                    <i class="fa fa-check text-success"></i> Sudah selesai wrapping
                                </label>
                            </div>
                            <div class="mb-1">
                                <label>
                                    <?= form_radio('is_wrapping', 0, isset($book_receive->is_wrapping) && ($book_receive->is_wrapping == 0) ? true : false); ?>
                                    <i class="fa fa-times text-danger"></i> Belum selesai wrapping
                                </label>
                            </div>
                            <?= form_error('is_wrapping'); ?>
                        </div>
                        <div class="form-group">
                            <label for="wrapping_start_date">Tanggal Mulai Wrapping
                            </label>
                            <div class="has-clearable">
                                <button type="button" class="close" aria-label="Close">
                                    <span aria-hidden="true">
                                        <i class="fa fa-times-circle"></i>
                                    </span>
                                </button>
                                <?= form_input('wrapping_start_date', $book_receive->wrapping_start_date, 'class="form-control dates"'); ?>
                                <?= form_error('wrapping_start_date'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="wrapping_end_date">Tanggal Selesai Pracetak
                            </label>
                            <div class="has-clearable">
                                <button type="button" class="close" aria-label="Close">
                                    <span aria-hidden="true">
                                        <i class="fa fa-times-circle"></i>
                                    </span>
                                </button>
                                <?= form_input('wrapping_end_date', $book_receive->wrapping_end_date, 'class="form-control dates" '); ?>
                                <?= form_error('wrapping_end_date'); ?>
                            </div>
                        </div>
                    </fieldset>
                    <hr>
                    <div class="form-actions">
                        <input class="btn btn-primary ml-auto" type="submit" value="Submit" id="btn-submit">
                    </div>
                    </form>
                    <?//= form_close(); ?>
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
    $("#form-book-receive").validate({
            rules: {
                book_id: "crequired",
                // name: "crequired",
                // category: "crequired",
                order_number: "crequired",
                order_code: "crequired",
                // type: "crequired",
                total: {
                    crequired: true,
                    cnumber: true
                },
                // paper_content: "crequired",
                // paper_cover: "crequired",
                // paper_size: "crequired",
            },
            errorElement: "span",
            errorPlacement: validateErrorPlacement,
        },
        validateSelect2()
    );

    // handleCategoryChange($('#category').val())

    // $('#category').change(function(e) {
    //     const category = e.target.value
    //     handleCategoryChange(category)
    // })

    // function handleCategoryChange(category) {
    //     if (category === 'nonbook') {
    //         $('#book-id-wrapper').hide()
    //         $('#name-wrapper').show()
    //         $('#nonbook_example').show()
    //         $('#book-id').val('');
    //     } else {
    //         $('#book-id-wrapper').show()
    //         $('#name-wrapper').hide()
    //         $('#nonbook_example').hide()
    //         $('#name').val('');
    //     }
    // }

    // $("#total,#paper-divider,#book-id").change(function(halaman) {
    //     $("#paper-estimation").val(($("#total").val() * $("#info-book-pages").text()) / $(
    //         "#paper-divider").val());
    // });

    $('.dates').flatpickr({
        altInput: true,
        altFormat: 'j F Y',
        dateFormat: 'Y-m-d'
    });

    // $('#delete-file').change(function() {
    //     if (this.checked) {
    //         $('#upload-file-form').hide()
    //     } else {
    //         $('#upload-file-form').show()
    //     }
    // })

    $('#book-id').change(function(e) {
        const bookId = e.target.value
        console.log(bookId)

        $.ajax({
            type: "GET",
            url: "<?= base_url('print_order/api_get_book/'); ?>" + bookId,
            datatype: "JSON",
            success: function(res) {
                console.log(res);
                $('#book-info').show()
                $('#info-book-title').html(res.data.book_title)
                $('#info-book-title').attr("href", "<?= base_url('book/view/'); ?>" +
                    bookId)
                $('#info-book-pages').html(res.data.book_pages)
                $('#info-isbn').html(res.data.isbn)
                $('#info-book-file-link').attr("href", "" + res.data.book_file_link)
                $('#info-book-file-link').attr("title", "" + res.data.book_title)

                if (res.data.from_outside == 0) {
                    $('#name-wrapper').val('');
                    $('#name-wrapper').hide()
                } else {
                    $('#name-wrapper').val('');
                    $('#name-wrapper').show()
                }
            },
            error: function(err) {
                console.log(err);
            },
        });
    })

    // $('#location-binding').change(function(e) {
    //     if ($('#location-binding').val() != 'inside') {
    //         $('#location-binding-outside-wrapper').show();
    //     } else {
    //         $('#location-binding-outside').val('');
    //         $('#location-binding-outside-wrapper').hide();
    //     }
    // })

    // $('#location-laminate').change(function(e) {
    //     if ($('#location-laminate').val() != 'inside') {
    //         $('#location-laminate-outside-wrapper').show();
    //     } else {
    //         $('#location-laminate-outside').val('');
    //         $('#location-laminate-outside-wrapper').hide();
    //     }
    // })
})
</script>