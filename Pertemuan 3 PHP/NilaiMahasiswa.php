<?php
$mahasiswa = [
    ["nama" => "Adha", "nim" => "2311001", "tugas" => 80, "uts" => 75, "uas" => 85],
    ["nama" => "Hanns", "nim" => "2211002", "tugas" => 70, "uts" => 65, "uas" => 60],
    ["nama" => "Citra", "nim" => "2111003", "tugas" => 90, "uts" => 88, "uas" => 92]
];

function hitungNilaiAkhir($tugas, $uts, $uas) {
    return ($tugas * 0.3) + ($uts * 0.3) + ($uas * 0.4);
}

function tentukanGrade($nilai) {
    if ($nilai >= 85) return "A";
    elseif ($nilai >= 75) return "B";
    elseif ($nilai >= 65) return "C";
    elseif ($nilai >= 50) return "D";
    else return "E";
}

function tentukanStatus($nilai) {
    return ($nilai >= 60) ? "Lulus" : "Tidak Lulus";
}

$totalNilai = 0;
$nilaiTertinggi = 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sistem Penilaian Mahasiswa</title>
    <style>
        table { border-collapse: collapse; width: 70%; margin: 20px auto; }
        th, td { border: 1px solid black; padding: 10px; text-align: center; }
        th { background-color: #9F1521; color: white; }
    </style>
</head>
<body>

<h2 style="text-align:center;">Data Nilai Mahasiswa</h2>

<table>
    <tr>
        <th>Nama</th>
        <th>NIM</th>
        <th>Nilai Akhir</th>
        <th>Grade</th>
        <th>Status</th>
    </tr>

    <?php foreach ($mahasiswa as $mhs): 
        $nilaiAkhir = hitungNilaiAkhir($mhs['tugas'], $mhs['uts'], $mhs['uas']);
        $grade = tentukanGrade($nilaiAkhir);
        $status = tentukanStatus($nilaiAkhir);
        $totalNilai += $nilaiAkhir;
        if ($nilaiAkhir > $nilaiTertinggi) {
            $nilaiTertinggi = $nilaiAkhir;
        }
    ?>
    <tr>
        <td><?= $mhs['nama']; ?></td>
        <td><?= $mhs['nim']; ?></td>
        <td><?= number_format($nilaiAkhir, 2); ?></td>
        <td><?= $grade; ?></td>
        <td><?= $status; ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<?php $rataRata = $totalNilai / count($mahasiswa); ?>

<h4 style="text-align: center;" >Rata-rata Kelas: <?= number_format($rataRata, 2); ?></h4>
<h4 style="text-align: center;" >Nilai Tertinggi: <?= number_format($nilaiTertinggi, 2); ?></h4>

</body>
</html>