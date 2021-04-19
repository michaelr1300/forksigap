<?php
$date_year          = $this->input->get('date_year');
$invoice_type       = $this->input->get('invoice_type');
$date_month         = $this->input->get('date_month');

$date_year_options = [];

// $date_month_options = [
//     ''  => '- Bulannya Ga Mau Muncul Ini Gimana Ya -',
// ];

for ($dy = intval(date('Y')); $dy >= 2015; $dy--) {
    $date_year_options[$dy] = $dy;
}

$invoice_type_options = [
    ''  => '- Filter Kategori Faktur -',
    'credit' => 'Kredit',
    'cash' => 'Tunai',
    'online' => 'Online',
    'showroom' => 'Showroom'
];
?>

<link
    rel="stylesheet"
    href="<?= base_url('assets/vendor/chart.js/new/Chart.min.css'); ?>"
>
<script src="https://cdnjs.cloudflare.com/ajax/libs/js-url/2.5.3/url.js"></script>
<header class="page-title-bar">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url(); ?>"><span class="fa fa-home"></span></a>
            </li>
            <li class="breadcrumb-item">
                <a
                    href="<?= base_url('earning'); ?>"
                    class="text-muted"
                >Pendapatan</a>
            </li>
            <li class="breadcrumb-item active">
                <a class="text-muted">Pendapatan Faktur</a>
            </li>
        </ol>
    </nav>
</header>

<div class="page-section">
    <div class="row">
        <div class="col-12">
            <section class="card card-fluid">
                <div class="card-body p-0">
                    <header class="card-header">
                        <ul class="nav nav-tabs card-header-tabs">
                            <li class="nav-item">
                                <a
                                    class="nav-link active"
                                    href="<?= base_url('earning/'); ?>"
                                >Pendapatan Faktur</a>
                            </li>
                            <li class="nav-item">
                                <a
                                    class="nav-link"
                                    href="<?= base_url('earning/detail'); ?>"
                                >Detail Pendapatan</a>
                            </li>
                        </ul>
                    </header>
                    <div class="p-3">
                        <?= form_open($pages, ['method' => 'GET']); ?>
                        <div class="row">
                            <div class="col">
                                <?= form_dropdown('date_year', $date_year_options, $date_year, 'id="date_year" class="form-control custom-select d-block" title="Filter Tahun Cetak"'); ?>
                            </div>
                            <!-- <div class="col">
                                <?= form_dropdown('date_month', $date_month_options, $date_month, 'id="date_month" class="form-control custom-select d-block" title="Filter Bulan Cetak"'); ?>
                            </div> -->
                            <div class="col">
                                <?= form_dropdown('invoice_type', $invoice_type_options, $invoice_type, 'id="invoice_type" class="form-control custom-select d-block" title="Filter Tipe Invoice"'); ?>
                            </div>
                            <div class="col">
                                <div
                                    class="btn-group btn-block"
                                    role="group"
                                    aria-label="Filter button"
                                >
                                    <button
                                        class="btn btn-secondary"
                                        type="button"
                                        onclick="location.href = '<?= base_url('earning/'); ?>'"
                                    > Reset</button>
                                    <button
                                        class="btn btn-primary"
                                        type="submit"
                                        value="Submit"
                                    ><i class="fa fa-filter"></i> Filter</button>
                                    <!-- <button
                                        class="btn btn-success"
                                        type="submit"
                                        id="excel"
                                        name="excel"
                                        value="1"
                                    >Excel</button> -->
                                </div>
                            </div>
                        </div>
                        <?= form_close(); ?>
                        <hr>
                    </div>
                    <div class="container">
                        <div class="row">
                            <div
                                class="col-md-12"
                                style="text-align: center;"
                            >
                                <b>
                                    <h5>LAPORAN PENDAPATAN FAKTUR</h5>
                                </b>
                                <b>
                                    <h7>Bulan Tertentu</h7>
                                </b>
                                <b>
                                    <p><?= $this->input->get('date_year'); ?></p>
                                </b>
                            </div>
                            <div class="col-md-12">
                                <canvas id="total_year"></canvas>
                            </div>
                            <div
                                class="col-md-12"
                                style="text-align: center;"
                            >
                                <b>
                                    <h5>JUMLAH FAKTUR TERCETAK</h5>
                                </b>
                            </div>
                            <div class="col-md-12">
                                <canvas id="total_invoice"></canvas>
                            </div>
                        </div>

                        <div
                            id="table_laporan"
                            name="table_laporan"
                            style="display:none;"
                        >
                            <hr>
                            <div style="text-align: center;">
                                <b>
                                    <p id="month_year">

                                    </p>
                                </b>
                            </div>
                            <div style="text-align: left;">
                                <b>
                                    <p>LAPORAN JUDUL BUKU YANG BERHASIL DI CETAK</p>
                                </b>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th
                                                scope="col"
                                                class="pl-4 align-middle text-center"
                                            >No</th>
                                            <th
                                                scope="col"
                                                class="align-middle text-center"
                                            >Judul Buku</th>
                                            <th
                                                scope="col"
                                                class="align-middle text-center"
                                            >Kategori Cetak</th>
                                            <th
                                                scope="col"
                                                class="align-middle text-center"
                                            >Jumlah Pesanan</th>
                                            <th
                                                scope="col"
                                                class="align-middle text-center"
                                            >Jumlah Hasil Cetak</th>
                                            <th class="align-middle text-center">Ref</th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        id="to_fill"
                                        class="align-middle text-center"
                                    >
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<script src="<?= base_url('assets/vendor/chart.js/new/Chart.bundle.min.js'); ?>"></script>

<script>
//assign nilai dari model ke variable javascript
var data = [];
var total_earning = [];
var count_invoice = [];
var count_invoice_book = [];
<?php for ($i = 0; $i < 12; $i++) { ?>
    data.push(<?= json_encode($model[$i]['data']) ?>)
    total_earning.push(<?= $model[$i]['total_earning'] ?>)
    count_invoice.push(<?= $model[$i]['count_invoice'] ?>)
    count_invoice_book.push(<?= $model[$i]['count_invoice_book'] ?>)
<?php } ?>

// function get_category(category) {
//     if (category == 'new') {
//         return "Cetak Baru";
//     } else if (category == 'revise') {
//         return "Cetak Ulang Revisi";
//     } else if (category == 'reprint') {
//         return "Cetak Ulang Non Revisi";
//     } else if (category == 'nonbook') {
//         return "Cetak Non Buku";
//     } else if (category == 'outsideprint') {
//         return "Cetak Di Luar";
//     } else if (category == 'from_outside') {
//         return "Cetak Dari Luar";
//     } else {
//         return null;
//     }
// }

// function populateTable(items, date_month) {
//     $('#month_year').html(date_month + ' ' + date_year);
//     var i = 1;
//     const table = document.getElementById("to_fill");
//     items.forEach(item => {
//         let row = table.insertRow();
//         let no = row.insertCell(0);
//         no.innerHTML = i++;
//         let title = row.insertCell(1);
//         title.innerHTML = item.title;
//         let category = row.insertCell(2);
//         category.innerHTML = get_category(item.category);
//         let total = row.insertCell(3);
//         total.innerHTML = item.total;
//         let total_new = row.insertCell(4);
//         total_new.innerHTML = item.total_new;
//         let id = row.insertCell(5);
//         id.innerHTML = "<a href='" + base_url + item.id + "'> Link Order Cetak </a>";
//     });

// }

var ctx = document.getElementById("total_year").getContext('2d');
var total_year = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"],
        datasets: [{
            label: 'Jumlah Judul Buku Terjual',
            data: total_earning,
            backgroundColor: 'rgba(255, 153, 0, 0.8)',
            borderColor: 'rgba(255, 153, 0, 0.2)',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            yAxes: [{
                display: true,
                ticks: {
                    beginAtZero: true
                }
            }],
            xAxes: [{
                display: true,
                ticks: {
                    beginAtZero: true
                }
            }]
        },
        layout: {
            padding: {
                left: 0,
                right: 0,
                top: 0,
                bottom: 0
            }
        },
        legend: {
            position: 'bottom'
        }
    }
});

var ctx2 = document.getElementById("total)invoice").getContext('2d');
var total_invoice = new Chart(ctx2, {
    type: 'bar',
    data: {
        labels: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"],
        datasets: [{
            label: 'Jumlah Faktur Tercetak',
            data: count_invoice,
            backgroundColor: 'rgba(255, 255, 0, 0.8)',
            borderColor: 'rgba(255, 153, 0, 0.2)',
            backgroundColor: 'rgba(51, 51, 204, 0.8)',
            borderColor: 'rgba(51, 51, 204, 0.2)',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            yAxes: [{
                display: true,
                ticks: {
                    beginAtZero: true
                }
            }],
            xAxes: [{
                display: true,
                ticks: {
                    beginAtZero: true
                }
            }]
        },
        layout: {
            padding: {
                left: 0,
                right: 0,
                top: 0,
                bottom: 0
            }
        },
        legend: {
            position: 'bottom'
        }
        // onClick: function(e) {
        //     var bar = this.getElementAtEvent(e)[0];
        //     var index = bar._index;
        //     var datasetIndex = bar._datasetIndex;
        //     if (index == 0) {
        //         $('#table_laporan').toggle();
        //         $("#to_fill").empty();
        //         populateTable(jan_data, "January");
        //     } else if (index == 1) {
        //         $('#table_laporan').toggle();
        //         $("#to_fill").empty();
        //         populateTable(feb_data, "February");
        //     } else if (index == 2) {
        //         $('#table_laporan').toggle();
        //         $("#to_fill").empty();
        //         populateTable(mar_data, "March");
        //     } else if (index == 3) {
        //         $('#table_laporan').toggle();
        //         $("#to_fill").empty();
        //         populateTable(apr_data, "April");
        //     } else if (index == 4) {
        //         $('#table_laporan').toggle();
        //         $("#to_fill").empty();
        //         populateTable(may_data, "May");
        //     } else if (index == 5) {
        //         $('#table_laporan').toggle();
        //         $("#to_fill").empty();
        //         populateTable(jun_data, "June");
        //     } else if (index == 6) {
        //         $('#table_laporan').toggle();
        //         $("#to_fill").empty();
        //         populateTable(jul_data, "July");
        //     } else if (index == 7) {
        //         $('#table_laporan').toggle();
        //         $("#to_fill").empty();
        //         populateTable(aug_data, "August");
        //     } else if (index == 8) {
        //         $('#table_laporan').toggle();
        //         $("#to_fill").empty();
        //         populateTable(sep_data, "September");
        //     } else if (index == 9) {
        //         $('#table_laporan').toggle();
        //         $("#to_fill").empty();
        //         populateTable(oct_data, "October");
        //     } else if (index == 10) {
        //         $('#table_laporan').toggle();
        //         $("#to_fill").empty();
        //         populateTable(nov_data, "November");
        //     } else if (index == 11) {
        //         $('#table_laporan').toggle();
        //         $("#to_fill").empty();
        //         populateTable(dec_data, "December");
        //     }
        // }
    }
});

// chart2.onclick = function(evt) {
//     // console.log("preparasi");
//     $.ajax({
//         type: "POST",
//         url: "http://localhost/sigap/production_report/coba",
//         data: {
//             year: urlParams.get('date_year'),
//             month: urlParams.get('date_month')
//         },
//         success: function(result) {
//             //console.log(JSON.parse(result));
//             var detail_data = JSON.parse(result);
//             var detail_table = "";
//             for (i in detail_data) {
//                 console.log(detail_data[i]);
//                 var detail_row = "<tr>";
//                 var base_url = "<?= base_url('print_order/view/'); ?>/";
//                 var no = Number(i) + 1
//                 detail_row += "<td class='align-middle text-center pl-4'>" + no + "</td>";
//                 detail_row += "<td class='align-middle text-center4'>" + detail_data[i].book_title + "</td>";
//                 detail_row += "<td class='align-middle text-center4'>" + categories[detail_data[i].category] + " </td>";
//                 detail_row += "<td class='align-middle text-center4'>" + detail_data[i].total + "</td>";
//                 detail_row += "<td class='align-middle text-center4'>" + detail_data[i].total_postprint + "</td>";
//                 detail_row += "<td class='align-middle text-center4'> <a href='" + base_url + detail_data[i].print_order_id + "'> Link Order Cetak </a></td></tr>";
//                 detail_table += detail_row;
//             }
//             $("tbody").hide();
//             $("tbody").html(detail_table);
//             $(".laporan").fadeIn("slow");
//             $("tbody").fadeIn("slow");
//         }
//     });
// }
</script>
