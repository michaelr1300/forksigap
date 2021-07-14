<?php $i = 0;?>
<header class="page-title-bar">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?=base_url();?>"><span class="fa fa-home"></span></a>
            </li>
            <li class="breadcrumb-item">
                <a class="text-muted">Perpustakaan</a>
            </li>
        </ol>
    </nav>
</header>
<div class="page-section">
    <div class="row">
        <div class="col-md-6">
            <section class="card card-fluid">
                <header class="card-header">
                    <div class="d-flex align-items-center">
                        <span class="mr-auto">Perpustakaan <span class="badge badge-info"><?=$total;?></span></span>
                        <div class="card-header-control">
                            <a href="<?=base_url('library/add');?>" class="btn btn-primary btn-sm"><i
                                    class="fa fa-plus fa-fw"></i> Tambah</a>
                        </div>
                    </div>
                </header>
                <div class="card-body p-0">
                    <?php if ($libraries): ?>
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Perpustakaan</th>
                                    <th style="width:100px; min-width:100px;"> &nbsp; </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($libraries as $library): ?>
                                <tr>
                                    <td class="align-middle"><?=++$i;?></td>
                                    <td class="align-middle"><?=$library->library_name;?></td>
                                    <td class="align-middle text-right">
                                        <a href="<?=base_url('library/edit/' . $library->library_id . '');?>"
                                            class="btn btn-sm btn-secondary">
                                            <i class="fa fa-pencil-alt"></i>
                                            <span class="sr-only">Edit</span>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" data-toggle="modal"
                                            data-target="#modal-hapus-<?=$library->library_id;?>"><i
                                                class="fa fa-trash-alt"></i><span class="sr-only">Delete</span></button>
                                    </td>
                                </tr>
                                <div class="modal modal-alert fade" id="modal-hapus-<?=$library->library_id;?>"
                                    tabindex="-1" role="dialog" aria-labelledby="modal-hapus" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">
                                                    <i class="fa fa-exclamation-triangle text-red mr-1"></i> Konfirmasi
                                                    Hapus
                                                </h5>
                                            </div>
                                            <div class="modal-body">
                                                <p>Apakah anda yakin akan menghapus <span
                                                        class="font-weight-bold"><?=$library->library_name;?></span>?
                                                </p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-danger"
                                                    onclick="location.href='<?=base_url('library/delete/' . $library->library_id . '');?>'"
                                                    data-dismiss="modal">Hapus</button>
                                                <button type="button" class="btn btn-light"
                                                    data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach;?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <p class="text-center mt-3">Data tidak tersedia</p>
                    <?php endif;?>
                </div>
            </section>
        </div>
    </div>
</div>