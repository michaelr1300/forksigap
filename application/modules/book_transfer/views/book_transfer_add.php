<header class="page-title-bar">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url(); ?>"><span class="fa fa-home"></span></a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url('book_transfer'); ?>">Pemindahan Buku</a>
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
                    <form action="<?= base_url("book_transfer/add_book_transfer"); ?>" method="post">
                        <fielsdet>
                            <legend>Form Pemindahan Buku</legend>
                            <link href="<?php echo base_url(); ?>assets/autocomplete/jquery-ui.min.css"
                                rel="stylesheet">
                            
                            <div class="form-group">
                                <label>Judul Buku</label>
                                <select class="form-control" name="book_tittle" id="book_tittle" required>
                                    <option value="">No Selected</option>
                                    <?php foreach($book as $row):?>
                                    <option value="<?php echo $row->book_id;?>"><?php echo $row->book_tittle;?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>


                            <div
                            class="form-group"
                            id="book-id-wrapper"
                            >   
                            <label for="library-id">
                                <?= $this->lang->line('form_library_name'); ?>
                                <abbr title="Required">*</abbr>
                            </label>
                            <?= form_dropdown('library_id', get_dropdown_list_library(), $input->library_id, 'id="library-id" class="form-control custom-select d-block"'); ?>
                            <?= form_error('library_id'); ?>
                            </div>

                            
                            <div class="form-group">
                                <label for="quantity">Jumlah Buku<abbr title="Required">*</abbr></label>
                                <input type="number" name="qty" id="qty" class="form-control" min="1" />
                            </div>
                            
                        </fieldset>
                        <hr>
                        <!-- button -->
                        <input type="submit" class="btn btn-primary ml-auto" value="Submit" />
                        <a class="btn btn-secondary" href="<?php echo base_url('book_transfer') ?>"
                            role="button">Back</a>
                    </form>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
 
 $('#book').change(function(){ 
     var id=$(this).val();
     $.ajax({
         url : "<?php echo site_url('book_transfer/get_book');?>",
         method : "POST",
         data : {id: id},
         async : true,
         dataType : 'json',
         success: function(data){
              
             var html = '';
             var i;
             for(i=0; i<data.length; i++){
                 html += '<option value='+data[i].book_id+'>'+data[i].book_tittle+'</option>';
             }
             $('#book').html(html);

         }
     });
     return false;
 }); 
  
});
</script>
