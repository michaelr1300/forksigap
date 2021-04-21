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
        /* margin-left: 50px;
        margin-right: 50px; */
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
        font-size: 14px;
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
        width: 25%;
    }

    .middle {
        width: 50%;
    }

    .right {
        width: 25%;
    }

    .row:after {
        content: "";
        display: table;
        clear: both;
    }

    .left-header {
        width: 20%;
    }

    .left-50 {
        width: 50%;
    }
    .middle-10{
        width: 10%
    }
    .right-20 {
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

    .box{
        font-family: 'Calibri', sans-serif;
        font-weight: lighter;
        font-size: 14px;
        color: black;
        border: 1px solid black;
        padding: 10px;
        min-width: 20px;
        height: 20px;
        box-sizing: border-box;
    }
    .content{
        height: 30%
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
            <br>Jl. Sendok, Karanggayam CT VIII Caturtunggal Depok, Sleman, D.I. Yogyakarta 55281
            <br>Telp/Fax.: 0274-561037, Email: ugmpress.ugm.ac.id | gmupress@ugm.ac.id </p>
        </div>
    </div>
    <h1><b>BON PERMINTAAN BUKU NON JUAL</h1>
    <div class="row">
        <div class="column left-50">
            <p>NAMA :  
            <br>SEKSI :  
            <br>JENIS : <?=get_book_non_sales_type()[$type]?> </p>
        </div>
        <div class="column middle-10">
            <p><b>Nomor Bon : </b></p>   
        </div>
        <div class="column right-20">
            <div class="box"><?=$number?></div>
        </div>
    </div>
    <div class="content">
    <table>
        <tr>
            <th>NO</th>
            <th>JUDUL BUKU</th>
            <th>JUMLAH</th>
        </tr>
        <?php $i=1?>
        <?php foreach($book_list as $books) : ?>
        <tr>
            <td><?=$i++?></td>
            <td style="text-align: left;"><?= $books->book_title?></td>
            <td><?= $books->qty?></td>
        </tr>
        <?php endforeach?>
    </table>
    </div>
    <br><br>
    <div class="row" style="margin-left:50px; margin-right:50px">
        <div class="column left" style="text-align:left">
            <p>Tanggal, <?=date('d F Y', strtotime($date))?> </p>
            <p>Yang Menerima<br><br><br><br><br><br><br>..........................</h2>
        </div>
        <div class="column middle" style="text-align:left"></div>
        <div class="column right" style="text-align:left">
            <br>
            <p>Yang Menyatakan<br><br><br><br><br><br><br>..........................</h2>
        </div>
    </div>

</body>

</html>