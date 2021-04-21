<?php
$level = check_level();
?>
<div class="modal fade" id="modal-staff-<?= $progress; ?>" tabindex="-1" role="dialog"
    aria-labelledby="modal-staff-preparing" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> Staff gudang untuk penyiapan buku </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <fieldset>
                        <div class="form-group" id="form-staff-preparing">
                            <label for="staff-preparing">Nama Staff Gudang</label>
                            <input type="text" id="preparing_staff" class="form-control">
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="modal-footer">
                <button id="btn-submit-staff-preparing" class="btn btn-primary" type="button">Submit</button>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
        const book_request_id = '<?= $book_request->invoice_id; ?>';

        // submit progress
        $(`#preparing-progress-wrapper`).on('click', ` #btn-submit-staff-preparing`, function() {
            const $this = $(this);

            $this.attr("disabled", "disabled").html("<i class='fa fa-spinner fa-spin '></i>");
            $.ajax({
                type: "POST",
                url: "<?php echo base_url('book_request/api_update/'); ?>" + book_request_id,
                datatype: "JSON",
                data: {
                    [`preparing_staff`]: $(`#preparing_staff`).val(),
                },
                success: function(res) {
                    showToast(true, res.data);
                },
                error: function(err) {
                    showToast(false, err.responseJSON.message);
                },
                complete: function() {
                    $this.removeAttr("disabled").html("Submit");
                    $(`#preparing-progress-wrapper`).load(` #preparing-progress`);
                    $(`#modal-staff-preparing`).modal('hide');
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                }
            });
        });
    })
</script>