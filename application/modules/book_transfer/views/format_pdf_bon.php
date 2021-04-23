<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
    h1 {
        font-family: 'Calibri', sans-serif;
        font-size: 14px;
        font-weight: bold;
        color: black;
        margin-bottom: 0;
    }

    table {
        margin-left: 50px;
        margin-right: 50px;
        width: 100%;
        border-collapse: collapse;
        margin-top:25px
    }

    th{
        text-align: center;
        border: 1px solid black;
        text-align: center;
    }
    tr {
        font-family: 'Calibri', sans-serif;
        font-weight: lighter;
        font-size: 12px;
        color: black;
    }

    td {
        border: 1px solid black;
        text-align: center;
    }

    p {
        font-family: 'Calibri', sans-serif;
        font-size: 14px;
        color: black;
        border-spacing: 50px;
    }

    .column {
        float: left;
        height: auto;
    }

    .left {
        width: 35%;
    }

    .middle {
        width: 30%;
    }

    .right {
        width: 35%;
    }

    .row:after {
        content: "";
        display: table;
        clear: both;
    }
    .left-header {
        width: 20%;
    }

    .image-logo{
        width:40px;height:10px;
        background-image:url('/assets/images/logo_ugm_press.jpg');
        background-repeat:no-repeat;
    }

    .border-bottom{
        border-top: 0px;
        border-left: 0px;
        border-right: 0px;
        border-bottom: 1px solid black;
    }
    .content{
        height: 45%
    }
    </style>

</head>

<body>
    <div class="row">
        <div class="column left-header">
            <div class="image-logo"></div>
            <p><b>UGM PRESS</b></p>
        </div>
        <div class="column">
        <p>
            <b>GADJAH MADA UNIVERSITY PRESS</b>
            <br>Jl. Grafika No. 1, Kampus UGM
            <br>No. Telp 0274-561037, Email: gmupress@ugm.ac.id</p>
        </div>
    </div>
    <h1 style="text-align: center;"><b>BUKU KONSIYANSI<br><?= strtoupper($destination) ?></b></h1>
    <div class="content">
        <table>
            <tr>
                <th>NO</th>
                <th>JUDUL BUKU</th>
                <th>JUMLAH</th>
                <th></th>
                <th>HARGA</th>
                <th>TOTAL</th>
            </tr>
            <?php $i=1?>
            <?php foreach($book_list as $books) : ?>
            <tr>
                <td><?=$i++?></td>
                <td style="text-align: left;"><?= $books->book_title?></td>
                <td><?= $books->qty?></td>
                <td>X</td>
                <td style="text-align: left;"> Rp <?=$books->price?></td>
                <td style="text-align: left;"> Rp <?=$books->price * $books->qty?></td>
            </tr>
            <?php endforeach?>
            <?php 
                $total=0;
                foreach($book_list as $books){
                    $total += $books->price;
                }
                $total_discount = $total * (0.01* $discount);
                ?>
            <tr style="border: 0px">
                <td style="border: 0px"></td>
                <td style="border: 0px"></td>
                <td style="border: 0px"></td>
                <td style="border: 0px"></td>
                <td style="border: 0px"></td>
                <td style="text-align: left; border: 0px">Rp <?= $total?></td>
            </tr>
            <tr style="border: 0px">
                <td style="border: 0px"></td>
                <td style="border: 0px"></td>
                <td style="border: 0px">DISKON</td>
                <td style="border: 0px"></td>
                <td style="border: 0px"><?= $discount?>%</td>
                <td class="border-bottom"; style="text-align: left;">Rp <?=$total_discount?></td>
            </tr>
            <tr style="border: 0px">
                <td style="border: 0px"></td>
                <td style="border: 0px"></td>
                <td style="border: 0px"><b>TOTAL</b></td>
                <td style="border: 0px"></td>
                <td style="border: 0px"></td>
                <td style="text-align: left; border: 0px"><b>Rp <?= $total - $total_discount?></b></td>
            </tr>
        </table>
    </div>
    <br><br>
    <div class="row">
        <div class="column left" style="text-align:center">
            <p>Yang Menerima<br><br><br><br><br><br><br>..........................</h2>
        </div>
        <div class="column middle" style="text-align:center"></div>
        <div class="column right" style="text-align:center">
            <p>Yogyakarta, <?=date('d F Y', strtotime($transfer_date))?><br><br><br><br><br><br><br>..........................</h2>
        </div>
    </div>

    <!-- <div style="text-align: center;">
        <p>Manajer UGM Press<br><br><br><br><br><br><br>Dr. I Wayan Mustika, S.T., M.Eng</h2>
    </div> -->

</body>

</html>