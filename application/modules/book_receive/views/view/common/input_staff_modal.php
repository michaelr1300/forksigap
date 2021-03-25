<?php
$level = check_level();
if ($level == 'superadmin' || $level == 'admin_gudang') :
    $progress_text = '';
    if ($progress == 'handover') {
        $progress_text = 'Serah Terima';
    } elseif ($progress == 'wrapping') {
        $progress_text = 'Wrapping';
    }
?>
<div class="modal fade" id="modal-staff-<?= $progress; ?>" tabindex="-1" role="dialog"
    aria-labelledby="modal-staff-<?= $progress; ?>" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> Staff Gudang untuk Progress <?= $progress_text; ?> </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <fieldset>
                        <div class="form-group" id="form-staff-<?= $progress; ?>">
                            <label for="staff-<?= $progress; ?>">Nama Staff Gudang</label>
                            <input type="text" id="<?= $progress ?>_staff" class="form-control">
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="modal-footer">
                <button id="btn-submit-staff-<?= $progress?>" class="btn btn-primary" type="button">Submit</button>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
        const book_receive_id = '<?= $book_receive->book_receive_id; ?>';
        const progress = '<?= $progress; ?>';

        // submit progress
        $(`#${progress}-progress-wrapper`).on('click', `#btn-submit-staff-${progress}`, function() {
            const $this = $(this);

            $this.attr("disabled", "disabled").html("<i class='fa fa-spinner fa-spin '></i>");
            $.ajax({
                type: "POST",
                url: "<?php echo base_url('book_receive/api_update/'); ?>" + book_receive_id,
                datatype: "JSON",
                data: {
                    progress,
                    [`${progress}_staff`]: $(`#${progress}_staff`).val(),
                },
                success: function(res) {
                    showToast(true, res.data);
                },
                error: function(err) {
                    showToast(false, err.responseJSON.message);
                },
                complete: function() {
                    $this.removeAttr("disabled").html("Submit");
                    $(`#${progress}-progress-wrapper`).load(` #${progress}-progress`);
                    $(`#modal-staff-${progress}`).modal('hide');
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                }
            });
        });
    })
</script>
<?php endif; ?>