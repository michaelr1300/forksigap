<?php
$author_nip = [
    'type'  => 'number',
    'name'  => 'author_nip',
    'id'    => 'author_nip',
    'value' => $input->author_nip,
    'class' => 'form-control',
];

$latest_education_options = [
    ''      => '-- Pilih --',
    's1'    => 'S1',
    's2'    => 'S2',
    's3'    => 'S3',
    's4'    => 'Professor',
    'other' => 'Other',
];

// penampil ktp
$ktp_place = null;
if (isset($input->author_ktp) && $input->author_ktp) {
    if ($input->author_ktp) {
        $getextension = explode(".", $input->author_ktp);
    } else {
        $getextension[1] = '';
    }
    // jika ekstensi pdf maka tampilkan link
    if ($getextension[1] != 'pdf') {
        $ktp_place = '<img class="uploaded-image" src="' . base_url('authorktp/' . $input->author_ktp) . '" width="100%"><br>';
    } else {
        $ktp_place = '<div align="middle"><a href="' . base_url('authorktp/' . $input->author_ktp) . '" class="btn btn-success><i class="fa fa-download"></i> Lihat KTP</a></div>';
    }
}
?>

<header class="page-title-bar">
   <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
         <li class="breadcrumb-item">
            <a href="<?=base_url();?>"><span class="fa fa-home"></span></a>
         </li>
         <li class="breadcrumb-item">
            <a href="<?=base_url('author');?>">Penulis</a>
         </li>
         <li class="breadcrumb-item">
            <a class="text-muted">Form</a>
         </li>
      </ol>
   </nav>
</header>
<div class="page-section">
   <div class="row">
      <div class="col-md-6">
         <section class="card">
            <div class="card-body">
               <?=form_open_multipart($form_action, 'novalidate="" id="form_author"');?>
               <fieldset>
                  <legend>Form Penulis</legend>
                  <?=isset($input->author_id) ? form_hidden('author_id', $input->author_id) : '';?>
                  <div class="form-group">
                     <label for="user_id">Pilih akun untuk Login</label>
                     <?=form_dropdown('user_id', getDropdownListAuthor('user', ['user_id', 'username']), $input->user_id, 'id="user_id" class="form-control custom-select d-block"');?>
                     <small class="form-text text-muted">Author dapat login ke sistem apabila mempunyai akun pengguna.
                        Kosongkan pilihan jika tidak menetapkan akun.</small>
                     <?=form_error('user_id');?>
                  </div>
                  <hr class="my-2">

                  <div class="form-group">
                     <label for="author_name">
                        <?=$this->lang->line('form_author_name');?>
                        <abbr title="Required">*</abbr>
                     </label>
                     <?=form_input('author_name', $input->author_name, 'class="form-control" id="author_name"');?>
                     <?=form_error('author_name');?>
                  </div>

                  <div class="form-group">
                     <label for="author_nip">
                        <?=$this->lang->line('form_author_nip');?>
                        <abbr title="Required">*</abbr>
                     </label>

                     <?=form_input($author_nip);?>
                     <?=form_error('author_nip');?>
                  </div>

                  <div class="form-group">
                     <label for="work_unit_id">
                        <?=$this->lang->line('form_work_unit_name');?>
                        <abbr title="Required">*</abbr>
                     </label>
                     <?=form_dropdown('work_unit_id', getDropdownList('work_unit', ['work_unit_id', 'work_unit_name']), $input->work_unit_id, 'id="work_unit" class="form-control custom-select d-block"');?>
                     <?=form_error('work_unit_id');?>
                  </div>

                  <div class="form-group">
                     <label for="institute_id">
                        <?=$this->lang->line('form_institute_name');?>
                        <abbr title="Required">*</abbr>
                     </label>
                     <?=form_dropdown('institute_id', getDropdownList('institute', ['institute_id', 'institute_name']), $input->institute_id, 'id="institute" class="form-control custom-select d-block"');?>
                     <?=form_error('institute_id');?>
                  </div>

                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label for="author_degree_front"><?=$this->lang->line('form_author_degress_front');?></label>
                           <?=form_input('author_degree_front', $input->author_degree_front, 'class="form-control" id="author_degree_front" placeholder="contoh = Ir."');?>
                           <?=form_error('author_degree_front');?>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group">
                           <label for="author_degree_back"><?=$this->lang->line('form_author_degress_back');?></label>
                           <?=form_input('author_degree_back', $input->author_degree_back, 'class="form-control" id="author_degree_back" placeholder="contoh = S.T"');?>
                           <?=form_error('author_degree_back');?>
                        </div>
                     </div>
                  </div>

                  <div class="form-group">
                     <label
                        for="author_latest_education"><?=$this->lang->line('form_author_latest_education');?></label>
                     <?=form_dropdown('author_latest_education', $latest_education_options, $input->author_latest_education, 'id="author_latest_education" class="form-control custom-select d-block"');?>
                     <?=form_error('author_latest_education');?>
                  </div>

                  <div class="form-group">
                     <label for="author_address"><?=$this->lang->line('form_author_address');?></label>
                     <?=form_input('author_address', $input->author_address, 'class="form-control" id="author_address"');?>
                     <?=form_error('author_address');?>
                  </div>

                  <div class="form-group">
                     <label for="author_contact"><?=$this->lang->line('form_author_contact');?></label>
                     <?=form_input('author_contact', $input->author_contact, 'class="form-control" id="author_contact" type="number"');?>
                     <?=form_error('author_contact');?>
                  </div>

                  <div class="form-group">
                     <label for="author_email"><?=$this->lang->line('form_author_email');?></label>
                     <?=form_input('author_email', $input->author_email, 'class="form-control" id="author_email"');?>
                     <?=form_error('author_email');?>
                  </div>

                  <div class="form-group">
                     <label for="bank_id"><?=$this->lang->line('form_author_bank_name');?></label>
                     <?=form_dropdown('bank_id', getDropdownBankList('bank', ['bank_id', 'bank_name']), $input->bank_id, 'id="bank" class="form-control custom-select d-block"');?>
                     <?=form_error('bank_id');?>
                  </div>

                  <div class="form-group">
                     <label for="author_saving_num"><?=$this->lang->line('form_author_saving_num');?></label>
                     <?=form_input('author_saving_num', $input->author_saving_num, 'class="form-control" id="author_saving_num"');?>
                     <?=form_error('author_saving_num');?>
                  </div>

                  <div class="form-group">
                     <label for="heir_name"><?=$this->lang->line('form_author_heir_name');?></label>
                     <?=form_input('heir_name', $input->heir_name, 'class="form-control" id="heir_name"');?>
                     <?=form_error('heir_name');?>
                  </div>
                  <hr>

                  <div class="form-group">
                     <label for="author_ktp"><?=$this->lang->line('form_author_ktp');?></label>
                     <div class="custom-file">
                        <?=form_upload('author_ktp', '', 'class="custom-file-input" onchange="preview_image(event)"');?>
                        <label
                           class="custom-file-label"
                           for="author_ktp"
                        >Pilih file</label>
                     </div>
                     <small class="form-text text-muted">Hanya menerima file bertype : jpg, jpeg, png, pdf. Maksimal 15
                        MB</small>
                     <?=file_form_error('author_ktp', '<p class="text-danger">', '</p>');?>
                     <div class="col-8 offset-2 mt-3">
                        <?=$ktp_place;?>
                        <img
                           width="100%"
                           id="output_image"
                        />
                     </div>
                  </div>
               </fieldset>
               <hr>
               <div class="form-actions">
                  <button
                     class="btn btn-primary ml-auto"
                     type="submit"
                  >Submit data</button>
               </div>
               </form>
            </div>
         </section>
      </div>
   </div>
</div>
<script>
$(document).ready(function() {
   loadValidateSetting();
   // $("#form_author").validate({
   //       rules: {
   //          author_nip: {
   //             crequired: true,
   //             cminlength: 3,
   //             cnumber: true
   //          },
   //          author_name: {
   //             crequired: true,
   //             huruf: true
   //          },
   //          work_unit_id: "crequired",
   //          institute_id: "crequired",
   //          author_contact: {
   //             cnumber: true
   //          },
   //          author_email: {
   //             cemail: true
   //          },
   //          heir_name: {
   //             huruf: true
   //          },
   //          author_ktp: {
   //             dokumen: "jpg|png|jpeg|pdf",
   //             filesize15: 157280640
   //          }
   //       },
   //       errorElement: "span",
   //       errorPlacement: validateErrorPlacement
   //    },
   //    validateSelect2()
   // );

   let select2Options = {
      placeholder: '-- Pilih --',
      allowClear: true
   }

   $("#user_id").select2(select2Options);
   $("#work_unit").select2(select2Options);
   $("#institute").select2(select2Options);
   $("#bank").select2(select2Options);
})
</script>