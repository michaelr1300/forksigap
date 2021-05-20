<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <title>Royalty Penulis</title>

    <style>
    body {
        font-size: 12px;
        line-height: 24px;
        font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        color: #333;
    }

    table.royalty-table,
    thead.royalty-table,
    td.royalty-table,
    tr.royalty-table,
    .royalty-table {
        border: 1px solid black;
        border-collapse: collapse;
    }

    </style>
</head>

<body>
    <table style="width: 100%;">
        <tr>
            <td style="width:20%;">
                <img
                    src="<?= base_url('assets/images/logo_ugm_press.jpg'); ?>"
                    style="width:100%; max-width:120px;"
                >
            </td>
            <td style="width:80%">
                <b>GADJAH MADA UNIVERSITY PRESS</b><br>
                Jl. Sendok, Karanggayam CT VIII<br>
                Caturtunggal Depok, Sleman, D.I. Yogyakarta 55281<br>
                Telp/Fax (0274)-561037<br>
                NPWP:01.246.578.7-542.000 - E-mail : ugmpress.ugm.ac.id | ugmpress@ugm.ac.id
            </td>
        </tr>
    </table>

    <div style="text-align: center;">
        <h5>Daftar Penerima Royalti</h5>
        <?php $url = '';
        if ($period_time == null) $url = '';
        else $url = '/' . $period_time . '/' . $date_year; ?>
        <?php if ($period_time == '1') $period_time = 'Periode Januari-Juni';
        elseif ($period_time == '2') $period_time = 'Periode Juli-Desember';
        else $period_time = ''; ?>
        <?php if ($date_year == null) $date_year = '';
        else $date_year = 'Tahun ' . $date_year ?>
        <h6><?= $period_time ?></h6>
        <h6><?= $date_year ?></h6>
        <h6><b><?= $author->author_name ?></b></h6>
    </div>

    <table
        class="royalty-table"
        style="width: 100%;"
    >
        <thead
            class="royalty-table"
            style="text-align: center;"
        >
            <tr class="royalty-table">
                <th
                    scope="col"
                    width="5%"
                    class="align-middle royalty-table"
                >No</th>
                <th
                    scope="col"
                    width="25%"
                    class="align-middle royalty-table"
                >Judul Buku</th>
                <th
                    scope="col"
                    width="8%"
                    class="align-middle royalty-table"
                >Stok Lalu (Eks)</th>
                <th
                    scope="col"
                    width="10%"
                    class="align-middle royalty-table"
                >Harga (Rp)</th>
                <th
                    scope="col"
                    width="8%"
                    class="align-middle royalty-table"
                >Terjual (Eks)</th>
                <th
                    scope="col"
                    width="8%"
                    class="align-middle royalty-table"
                >Royalty %</th>
                <th
                    scope="col"
                    width="15%"
                    class="align-middle royalty-table"
                >Dibayar (Rp)</th>
                <th
                    scope="col"
                    width="8%"
                    class="align-middle royalty-table"
                >Sisa Stok (Eks)</th>
            </tr>
        </thead>
        <tbody>
            <?php $index = 0;
            $total_earning = 0;
            $total_royalty = 0; ?>
            <?php foreach ($royalty_details as $royalty) : ?>
                <tr>
                    <td class="text-center"><?= $index + 1; ?></td>
                    <td class="text-left"><?= $royalty->book_title ?></td>
                    <td class="text-left">Stok Lalu</td>
                    <td class="text-center">Price book</td>
                    <td class="text-right pr-5">Terjual</td>
                    <td class="text-left">15 %</td>
                    <td class="text-right pr-5">Rp <?= round($royalty->earned_royalty, 0) ?></td>
                    <td class="text-left">Sisa Stok</td>
                </tr>
        </tbody>
    </table>

    <table style="width: 100%;">
        <?php $index++;
                $total_royalty += $royalty->earned_royalty; ?>
    <?php endforeach; ?>
    <tr>
        <td
            scope="col"
            colspan="4"
        ></td>
        <td
            scope="col"
            colspan="2"
        >Jumlah</td>
        <td>Rp <?= $total_royalty; ?></td>
        <td></td>
    </tr>
    <tr>
        <td
            scope="col"
            colspan="4"
        ></td>
        <td
            scope="col"
            colspan="2"
        >PPh 15%</td>
        <td>Rp <?= (0.15 *  $total_royalty) ?></td>
        <td></td>
    </tr>
    <tr>
        <td
            scope="col"
            colspan="4"
        ></td>
        <td
            scope="col"
            colspan="2"
        >Netto</td>
        <td><b>Rp <?= (0.85 *  $total_royalty) ?></b></td>
        <td></td>
    </tr>
    </table>
</body>

</html>
