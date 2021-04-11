<?php
$level              = check_level();
?>
<header class="page-title-bar mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url(); ?>"><span class="fa fa-home"></span></a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url('book_stock'); ?>">Stok Buku</a>
            </li>
            <li class="breadcrumb-item">
                <a class="text-muted">
                    <?= $input->book_title; ?>
                </a>
            </li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title"> Stok Buku </h1>
        </div>
        <?php if($level == 'superadmin'):?>
        <div>
            <a href="<?= base_url("$pages/edit/$input->book_stock_id"); ?>" class="btn btn-primary btn-sm"><i
                    class="fa fa-edit fa-fw"></i> Edit Buku</a>
        </div>
        <?php endif?>
    </div>
</header>

<div class="page-section">
    <section id="data-draft" class="card">
        <header class="card-header">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item ">
                    <a class="nav-link active show" data-toggle="tab" href="#stock-data"><i
                            class="fa fa-warehouse pr-1"></i>Detail Stok Buku</a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link" data-toggle="tab" href="#chart-book"><i
                            class="fa fa-chart-line pr-1"></i>Transaksi Buku</a>
                </li>
            </ul>
        </header>
        <div class="card-body">
            <div class="tab-content">
                <!--stock data-->
                <div class="tab-pane fade active show" id="stock-data">
                    <div id="reload-stock">
                        <?php if ($level == 'superadmin'|| $level == 'admin_gudang' || $level == 'admin_pemasaran') : ?>
                        <?php $i = 1; ?>
                        <div class="row">
                            <div class="col-6 text-left">
                                <strong>Stok Buku</strong>
                            </div>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered mb-0 nowrap">
                                <tbody>
                                    <tr>
                                        <td width="160px">Judul Buku</td>
                                        <td><strong>
                                                <?= $input->book_title; ?>
                                            </strong></td>
                                    </tr>
                                    <tr>
                                        <td width="160px">Stok Keseluruhan</td>
                                        <td><?= $input->warehouse_present+$input->library_present+$input->showroom_present; ?>
                                        </td>
                                    </tr>
                                    <td width="160px">Stok Gudang</td>
                                    <td>
                                        <?= $input->warehouse_present; ?>
                                    </td>
                                    </tr>
                                    <tr>
                                        <td width="160px">Stok Showroom</td>
                                        <td><?= $input->showroom_present; ?></td>
                                    </tr>
                                    <tr>
                                        <td width="160px">Stok Perpustakaan</td>
                                        <td><?= $input->library_present; ?></td>
                                    </tr>
                                    <?php if($input->warehouse_present) :?>
                                    </tr>
                                    <td width="160px">Detail Stok Perpustakaan</td>
                                    <td>
                                        <table class="table table-bordered mb-0 table-responsive">
                                            <tbody>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama Perpustakaan</th>
                                                    <th>Stok</th>
                                                </tr>
                                                <?php $no=1; foreach($book_stock->library_stock as $library_data) : ?>
                                                <tr>
                                                    <td class="align-middle text-center">
                                                        <?= $no++; ?>
                                                    </td>
                                                    <td class="align-middle">
                                                        <?=$library_data->library_name?>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <?=$library_data->library_stock?>
                                                    </td>
                                                </tr>
                                                <?php endforeach ?>
                                            </tbody>
                                        </table>
                                    </td>
                                    </tr>
                                    <?php endif?>
                                </tbody>
                            </table>
                        </div>
                        <?php if (empty($book_stock->revision) == FALSE) : ?>
                        <hr>
                        <!-- Log Revisi Stok -->
                        <p class="font-weight-bold">Log Revisi Stok</p>
                        <div class="table-responsive" style="max-height:500px;">
                            <table class="table table-striped table-bordered mb-0">
                                <thead>
                                    <tr class="text-center">
                                        <th scope="col">No</th>
                                        <th scope="col">Awal</th>
                                        <th scope="col">Perubahan</th>
                                        <th scope="col">Tanggal</th>
                                        <th scope="col">Catatan</th>
                                        <?php if ($level == 'superadmin' || $level == 'admin_gudang') : ?>
                                        <th scope="col"></th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no=1; foreach($book_stock->revision as $revision) : ?>
                                    <tr class="text-center">
                                        <td>
                                            <?= $no++; ?>
                                        </td>
                                        <td>
                                            <?= $revision->warehouse_past; ?>
                                        </td>
                                        <td>
                                            <?php 
                                                if ($revision->operator == "+") {
                                                    echo '<div class="text-success"> ' . $revision->operator . ' ' . $revision->warehouse_revision . '</div>';
                                                } elseif ($revision->operator == "-") {
                                                    echo '<div class="text-danger"> ' . $revision->operator . ' ' . $revision->warehouse_revision . '</div>';
                                                } 
                                            ?>
                                        </td>
                                        <td>
                                            <?//= date('d F Y H:i:s', strtotime($history->date)); ?>
                                        </td>
                                        <td>
                                            <?= $revision->notes; ?>
                                        </td>
                                    </tr>
                                    <?php if($no==6) break;?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                        <!-- Log perubahan Stok -->
                        <?php else : ?>
                        <p>Data hanya dapat dilihat oleh Superadmin, Admin Penerbitan, Admin Percetakan, Admin Gudang,
                            dan Admin Pemasaran</p>
                        <?php endif; ?>
                    </div>
                </div>
                <!--stock data-->

                <!-- book transaction chart -->
                <link rel="stylesheet" href="<?= base_url('assets/vendor/chart.js/new/Chart.min.css'); ?>">
                <script src="https://cdnjs.cloudflare.com/ajax/libs/js-url/2.5.3/url.js"></script>
                <div class="tab-pane fade" id="chart-book">
                    <!-- Per month chart -->
                    <div class="row mb-4">
                        <p class="col-12 font-weight-bold">Transaksi Buku per Bulan</p>
                        <div class="col-3">
                            <input type="year" id="year" name="year" class="form-control">
                        </div>
                        <canvas id="chart-transaction-per-month" height="35vh !important" width="100% !important">
                        <script>
                        var ctx = document.getElementById('chart-transaction-per-month').getContext('2d');
                        var myChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli',
                                    'Agustus', 'September', 'Oktober', 'November', 'Desember'
                                ],
                                datasets: [{
                                    label: 'Transaksi Buku per Bulan',
                                    data: [12, 19, 10, 12, 15, 14, 10, 22, 18, 11, 12, 21],
                                    backgroundColor: [
                                        'rgba(74, 138, 216, 0.2)'
                                        // 'rgba(54, 162, 235, 0.2)',
                                        // 'rgba(255, 206, 86, 0.2)',
                                        // 'rgba(75, 192, 192, 0.2)',
                                        // 'rgba(153, 102, 255, 0.2)',
                                        // 'rgba(255, 159, 64, 0.2)'
                                    ],
                                    borderColor: [
                                        'rgba(74, 138, 216, 1)',
                                        // 'rgba(54, 162, 235, 1)',
                                        // 'rgba(255, 206, 86, 1)',
                                        // 'rgba(75, 192, 192, 1)',
                                        // 'rgba(153, 102, 255, 1)',
                                        // 'rgba(255, 159, 64, 1)'
                                    ],
                                    // borderWidth: 1
                                }]
                            },
                            options: {
                                scales: {
                                    yAxes: [{
                                        ticks: {
                                            beginAtZero: true
                                        }
                                    }]
                                }
                            }
                        });
                        </script>
                    </div>
                    <!-- Per month chart -->
                    <hr>
                    <!-- Per day chart -->
                    <div class="row">
                        <p class="col-12 font-weight-bold">Transaksi Buku per Hari</p>
                        <div class="col-3">
                            <input type="date" id="date" name="date" class="form-control">
                        </div>
                        <canvas id="chart-transaction-per-day" height="35vh !important" width="100% !important">
                        <script>
                        book_id = <?=$book_stock->book_id?>
                        today = new Date();
                        document.getELementById('date').valueAsDate = today;
                        date = today.toISOString().split('T')[0];
                        var ctx_perday = document.getElementById('chart-transaction-per-day');
                        var chart_transaction_per_day = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: [],
                                datasets: [{
                                    label: 'Transaksi Buku per Hari',
                                    data: [],
                                    backgroundColor: [
                                        'rgba(74, 138, 216, 0.2)'
                                        // 'rgba(54, 162, 235, 0.2)',
                                        // 'rgba(255, 206, 86, 0.2)',
                                        // 'rgba(75, 192, 192, 0.2)',
                                        // 'rgba(153, 102, 255, 0.2)',
                                        // 'rgba(255, 159, 64, 0.2)'
                                    ],
                                    borderColor: [
                                        'rgba(74, 138, 216, 1)',
                                        // 'rgba(54, 162, 235, 1)',
                                        // 'rgba(255, 206, 86, 1)',
                                        // 'rgba(75, 192, 192, 1)',
                                        // 'rgba(153, 102, 255, 1)',
                                        // 'rgba(255, 159, 64, 1)'
                                    ],
                                    // borderWidth: 1
                                }]
                            },
                            options: {
                                scales: {
                                    yAxes: [{
                                        ticks: {
                                            beginAtZero: true
                                        }
                                    }]
                                }
                            }
                        });
                        var updatePerDay = function(date, book_id) {
                            $.ajax({
                            url: "<?=base_url('book_stock/api_chart_data/')?>"+book_id+'/'+date,
                            type: 'GET',
                            dataType: 'json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(data) {
                                    chart_transaction_per_day.data.labels = data.stock_in.month;
                                    stock_in = data.stock_in.map(a => a);
                                    chart_transaction_per_day.data.datasets[0].data = stock_in;
                                    chart_transaction_per_day.update();
                                },
                                error: function(data){
                                    console.log(data);
                                }
                            });
                        }

                        updatePerDay(date);

                        $("#date").change(function() {
                            get_url();
                        });
                        
                        function get_url() {
                            date = $("#date").val();
                            url = "<?=base_url('book_stock/api_chart_data/') ?>"+book_id+'/'+date;
                            updatePerDay(date);
                        }
                        </script>
                    </div>
                </div>
                <!-- book transaction data -->

            </div>
        </div>
    </section>
</div>
<script>
$(document).ready(function() {
    $('.dates').flatpickr({
        altInput: true,
        altFormat: 'j F Y H:i:S',
        dateFormat: 'Y-m-d H:i:S',
        enableTime: true
    });

    $("#date_clear").click(function() {
        $('.dates').clear();
    })
})
</script>