<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <style>
    h1 {
        font-family: 'Calibri', sans-serif;
        font-size: 20px;
        font-weight: bold;
        text-align: center;
        color: black;
    }

    table {
        margin-left: 100px;
        margin-right: 100px;
    }

    tr {
        font-family: 'Calibri', sans-serif;
        font-weight: lighter;
        font-size: 16px;
        color: black;
    }

    h2 {
        font-family: 'Calibri', sans-serif;
        font-size: 16px;
        color: black;
        border-spacing: 50px;
    }

    </style>

</head>

<body>
    <h1><b>LEMBAR ANTRIAN KERJA</b></h1>
    <table>
        <tr>
            <td>
                JUDUL
            </td>
            <td>
                :
            </td>
            <td>
                <?= $title; ?>
            </td>
        </tr>
        <tr>
            <td>
                JENIS PEKERJAAN
            </td>
            <td>
                :
            </td>
            <td>
                <?= $jobtype; ?>
            </td>
        </tr>
        <tr>
            <td>
                NOMOR ORDER
            </td>
            <td>
                :
            </td>
            <td>
                <?= $ordernumber; ?>
            </td>
        </tr>
        <tr>
            <td>
                JUMLAH ORDER
            </td>
            <td>
                :
            </td>
            <td>
                <?= $total; ?>
            </td>
        </tr>
        <tr>
            <td>
                JUMLAH TERCETAK
            </td>
            <td>
                :
            </td>
            <td>
                <?= $total_postprint; ?>
            </td>
        </tr>
        <tr>
            <td>
                JUMLAH HASIL WRAPPING
            </td>
            <td>
                :
            </td>
            <td>
                <?= $total_postprint; ?>
            </td>
        </tr>
        <tr>
            <td>
                TANGGAL MULAI KERJA
            </td>
            <td>
                :
            </td>
            <td>
                <?= $wrapping_start_date; ?>
            </td>
        </tr>
        <tr>
            <td>
                DEADLINE
            </td>
            <td>
                :
            </td>
            <td>
                <?= $wrapping_deadline; ?>
            </td>
        </tr>
        <tr>
            <td>
                TANGGAL SELESAI KERJA
            </td>
            <td>
                :
            </td>
            <td>
                <?= $wrapping_end_date; ?>
            </td>
        </tr>
        <tr>
            <td>
                PIC
            </td>
            <td>
                :
            </td>
            <td>
                <?=$staff?>
            </td>
        </tr>
        <!-- <tr>
            <td>
                STATUS PENYELESAIAN
            </td>
            <td>
                :
            </td>
            <td>
                <?//= $progress; ?>
            </td>
        </tr> -->
        <tr>
            <td>
                KETERANGAN
            </td>
            <td>
                :
            </td>
            <td>
                <?= $notes; ?>
            </td>
        </tr>
    </table>
    <br><br><br>
    <div style="text-align: center;">
        <h2>Manajer UGM Press<br><br><br><br><br><br><br>Dr. I Wayan Mustika, S.T., M.Eng</h2>
    </div>

</body>

</html>
