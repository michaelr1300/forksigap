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
                    <?//= form_open_multipart($form_action, 'novalidate="" id="form-book-receive"'); ?>
                    <fielsdet>
                        <legend>Form Edit Penerimaan Buku</legend>
                        <?= isset($input->book_receive_id) ? form_hidden('print_order_id', $input->book_receive_id) : ''; ?>
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

    handleCategoryChange($('#category').val())

    $('#category').change(function(e) {
        const category = e.target.value
        handleCategoryChange(category)
    })

    function handleCategoryChange(category) {
        if (category === 'nonbook') {
            $('#book-id-wrapper').hide()
            $('#name-wrapper').show()
            $('#nonbook_example').show()
            $('#book-id').val('');
        } else {
            $('#book-id-wrapper').show()
            $('#name-wrapper').hide()
            $('#nonbook_example').hide()
            $('#name').val('');
        }
    }

    $("#total,#paper-divider,#book-id").change(function(halaman) {
        $("#paper-estimation").val(($("#total").val() * $("#info-book-pages").text()) / $(
            "#paper-divider").val());
    });

    $('.dates').flatpickr({
        altInput: true,
        altFormat: 'j F Y',
        dateFormat: 'Y-m-d'
    });

    $('#delete-file').change(function() {
        if (this.checked) {
            $('#upload-file-form').hide()
        } else {
            $('#upload-file-form').show()
        }
    })

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
})
</script>