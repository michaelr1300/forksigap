<?php $level = check_level() ?>
<div class="modal fade" id="modal-<?= $progress ?>-notes" tabindex="-1" role="dialog"
    aria-labelledby="modal-<?= $progress ?>-notes" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-overflow" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> Catatan progress</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <fieldset>
                    <div class="form-group">
                        <?php
                        if ($is_final) {
                            echo "<div>" . $book_request->{"{$progress}_notes"} . "</div>";
                        } else {
                            echo form_textarea([
                                'name'  => "{$progress}_notes",
                                'class' => 'form-control',
                                'id'    => "{$progress}-notes",
                                'rows'  => '6',
                                'value' => $book_request->{"{$progress}_notes"}
                            ]);
                        }
                        ?>
                    </div>
                </fieldset>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light ml-auto" data-dismiss="modal">Close</button>
                <?php if (!$is_final) : ?>
                <button class="btn btn-primary" type="button" id="btn-submit-<?= $progress ?>-notes"
                    data-dismiss="modal">Submit</button>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const bookRequestId = '<?= $book_request->book_request_id ?>';
    const progress = '<?= $progress ?>';

    // reload segmen ketika modal diclose
    $(`#${progress}-progress-wrapper`).on('shown.bs.modal', `#modal-${progress}-notes`, function() {
        initSummernote()
    })

    // submit progress
    $(`#${progress}-progress-wrapper`).on('click', `#btn-submit-${progress}-notes`, function() {
        const $this = $(this);

        $this.attr("disabled", "disabled").html("<i class='fa fa-spinner fa-spin '></i>");
        $.ajax({
            type: "POST",
            url: "<?php echo base_url('book_receive/api_update/'); ?>" + bookRequestId,
            datatype: "JSON",
            data: {
                [`${progress}_notes`]: $(`#${progress}-notes`).val(),
                progress
            },
            success: function(res) {
                showToast(true, res.data);
            },
            error: function(err) {
                showToast(false, err.responseJSON.message);
            },
            complete: function() {
                $this.removeAttr("disabled").html("Submit");
            }
        });
    });

    function initSummernote() {
        // inisiasi summernote
        $(`#${progress}-notes`).summernote(summernoteConfig)
    }
})
</script>