<section class="card">
    <header class="card-header">
        <ul class="nav nav-tabs card-header-tabs">
            <li class="nav-item">
                <a class="nav-link active show" data-toggle="tab" href="#book-receive-data-wrapper"><i
                        class="fa fa-info-circle"></i> Detail Penerimaan Buku</a>
            </li>
        </ul>
    </header>

    <div class="card-body">
        <div class="tab-content">
            <!-- BOOK RECEIVE DATA -->
            <div id="book-receive-data-wrapper" class="tab-pane fade active show">
                <div id="book-receive">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered mb-0">
                            <tbody>
                                <!-- <tr>
                                    <td width="200px"> Kategori Cetak </td>
                                    <td>Cetak Ulang Non Revisi </td>
                                </tr> -->
                                <tr>
                                    <td width="200px"> Judul Buku </td>
                                    <td><strong><?=$book_receive->book_title?></strong> </td>
                                </tr>
                                <!-- <tr>
                                    <td width="200px"> Catatan Order Cetak </td>
                                    <td> </td>
                                </tr> -->
                                <tr>
                                    <td width="200px"> Nomor Order </td>
                                    <td><?= $book_receive->order_number;?> </td>
                                </tr>
                                <!-- <tr>
                                    <td width="200px"> Kode Order </td>
                                    <td>700800 </td>
                                </tr>
                                <tr>
                                    <td width="200px"> Tipe Cetak </td>
                                    <td>pod </td>
                                </tr>
                                <tr>
                                    <td width="200px"> Jumlah Eksemplar </td>
                                    <td> </td>
                                </tr> -->
                                <tr>
                                    <td width="200px"> Hasil Cetak </td>
                                    <td><?=$book_receive->total?></td>
                                </tr>
                                <tr>
                                    <td width="200px"> Tanggal Masuk Gudang </td>
                                    <td><?=$book_receive->entry_date?> </td>
                                </tr>
                                <tr>
                                    <td width="200px"> Tanggal Finalisasi</td>
                                    <td><?=$book_receive->finish_date?> </td>
                                </tr>
                                <tr>
                                    <td width="200px"> Deadline</td>
                                    <td>
                                        <div class="text-danger"><?=$book_receive->deadline?></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="200px"> Diinput Oleh </td>
                                    <td>superadmin </td>
                                </tr>
                                <tr>
                                    <td width="200px"> Status</td>
                                    <td><?=get_book_receive_status()[$book_receive->book_receive_status];?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
