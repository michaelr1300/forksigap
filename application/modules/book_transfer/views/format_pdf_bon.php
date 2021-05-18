<!-- <!DOCTYPE html>
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
    }
    tr {
        font-family: 'Calibri', sans-serif;
        font-weight: lighter;
        font-size: 12px;
        color: black;
    }

    td {
        border: 1px solid black;
        height: 28px;
        padding-left:4px;
        padding-right:4px;
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

    .logo{
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
    .wrapper {
        display : flex;
        align-items : center;
        background-color: #00FF00;
        height: 100px;
        }

    .price{
        /* top: 50%; */
        transform: translateY(50%);
        text-align: right;
        float: right;
    }

    span{
        text-align: right;
        float: right;
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
    .column {
        float: left;
        height: auto;
    }

    .middle-10{
        width: 10%
    }
    .right-20 {
        width: 20%;
    }

    
    </style>

</head>

<body>
    <div class="row">
        <div class="column left-header">
            <img src="<?=base_url('assets/images/logo_ugm_press.jpg')?>" alt="logo" style="width:100%; max-width:90px;">
        </div>
        <div class="column">
            <p>
                <b>GADJAH MADA UNIVERSITY PRESS</b>
                <br>Jl. Sendok, Karanggayam CT VIII Caturtunggal Depok, Sleman, D.I. Yogyakarta 55281
                <br>Telp/Fax.: 0274-561037, Email: ugmpress.ugm.ac.id | gmupress@ugm.ac.id 
            </p>
        </div>
    </div>
    <h1 style="text-align: center;"><b>BON PEMINDAHAN BUKU<br><?= strtoupper($destination) ?></b></h1>
    <div class="content">
        <table>
            <tr>
                <th>NO</th>
                <th>JUDUL BUKU</th>
                <th>JUMLAH</th> -->
                <!-- <th>HARGA</th>
                <th>TOTAL</th> -->
            <!-- </tr>
            <?php $i=1; $total=0;?>
            <?php foreach($book_list as $books) : ?>
            <tr>
                <td><?=$i++?></td>
                <td><?= $books->book_title?></td>
                <td style="text-align: center;"><?= $books->qty?></td> -->
                <!-- <td> Rp
                    <div class="price"><?=number_format($books->price, 0, ",", ".")?></div>
                </td>
                <td> Rp <div class="price"><?=number_format($books->price * $books->qty, 0, ",", ".")?></div></td> -->
                <!-- <?php $total+=($books->price*$books->qty); ?>
            </tr>
            <?php endforeach?> -->
            <!-- <?php  $total_discount = $total * (0.01* $discount);?>
            <tr style="border: 0px">
                <td style="border: 0px"></td>
                <td style="border: 0px"></td>
                <td style="border: 0px"></td>
                <td style="border: 0px"></td>
                <td style="border: 0px">Rp <div class="price"><?//= number_format($total, 0, ",", ".")?></div></td>
            </tr>
            <tr style="border: 0px">
                <td style="border: 0px"></td>
                <td style="border: 0px"></td>
                <td style="border: 0px">DISKON</td>
                <td style="border: 0px; text-align: center;"><?//= $discount?>%</td>
                <td class="border-bottom" style="text-align: left;">Rp <div class="price"><?//= number_format($total_discount, 0, ",", ".")?></div></td>
            </tr>
            <tr style="border: 0px">
                <td style="border: 0px"></td>
                <td style="border: 0px"></td>
                <td style="border: 0px"><b>TOTAL</b></td>
                <td style="border: 0px"></td>
                <td style="border: 0px; font-weight: bold;">Rp <div class="price"><?//= number_format($total-$total_discount, 0, ",", ".") ?></div></td>
            </tr> -->
        <!-- </table>
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
</body>

</html> -->

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

    table{
        border: 0;
        margin-left: -2px;
    }

    .book-list{
        /* margin-left: 50px;
        margin-right: 50px; */
        width: 100%;
        border-collapse: collapse;
        margin-top:16px
    }

    .td-book-list {
        border: 1px solid black;
        text-align: center;
        padding-left: 4px;
        padding-right: 4px;
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
        width: 10%;
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
            <img src="<?=base_url('assets/images/logo_ugm_press.jpg')?>" alt="logo" style="width:100%; max-width:90px;">
        </div>
        <div class="column">
        <p>
            <b>GADJAH MADA UNIVERSITY PRESS</b>
            <br>Jl. Sendok, Karanggayam CT VIII Caturtunggal Depok, Sleman, D.I. Yogyakarta 55281
            <br>Telp/Fax.: 0274-561037, Email: ugmpress.ugm.ac.id | gmupress@ugm.ac.id </p>
        </div>
    </div>
    <br>
    <h1><b>BON PEMINDAHAN BUKU</h1>
    <div class="row">
        <div class="column left-50">
            <table>
                <tr>
                    <td>TUJUAN</td>
                    <td>:</td>
                    <td><?=strtoupper($destination)?></td>
                </tr>
            </table>
        </div>
        <div class="column middle-10">
            <p><b>Nomor Bon : </b></p>   
        </div>
        <div class="column right-20">
            <div class="box"><?=$number?></div>
        </div>
    </div>
    <div class="content">
    <table class="book-list">
        <tr>
            <th>NO</th>
            <th>JUDUL BUKU</th>
            <th>JUMLAH</th>
        </tr>
        <?php $i=1?>
        <?php foreach($book_list as $books) : ?>
        <tr>
            <td class="td-book-list"><?=$i++?></td>
            <td class="td-book-list" style="text-align: left;"><?= $books->book_title?></td>
            <td class="td-book-list"><?= $books->qty?></td>
        </tr>
        <?php endforeach?>
    </table>
    </div>
    <br><br>
    <div class="row" style="margin-left:50px; margin-right:50px">
        <div class="column left" style="text-align:left">
            <p>Tanggal, <?=date('d F Y', strtotime($transfer_date))?> <br>
            Yang Menerima<br><br><br><br><br><br><br>..........................</h2>
        </div>
        <div class="column middle" style="text-align:left"></div>
        <div class="column right" style="text-align:left">
            <br>
            <p>Yang Menyatakan<br><br><br><br><br><br><br>..........................</h2>
        </div>
    </div>

</body>

</html>