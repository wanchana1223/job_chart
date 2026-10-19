<?php
// 1. เชื่อมต่อฐานข้อมูล MySQL
$host = "localhost";
$user = "root";
$password = "";
$dbname = "company_db";

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// กำหนดภาษาไทย
mysqli_set_charset($conn, "utf8mb4");

// 2. รันคำสั่ง SQL Query
$sql = "SELECT 
            department_name AS department, 
            COUNT(opening_id) AS total 
        FROM toopening 
        WHERE status = 'Open' 
        GROUP BY department_name 
        ORDER BY total DESC";

$result = mysqli_query($conn, $sql);

$departments = array();
$totals = array();

if ($result && mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        $departments[] = $row['department'];
        $totals[] = (int)$row['total'];
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงานตำแหน่งงานที่เปิดรับ (Dynamic)</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            background-color: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .container {
            width: 100%;
            max-width: 800px;
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>สรุปจำนวนตำแหน่งงานที่เปิดรับสมัครตามแผนก (Real-time)</h2>
    <canvas id="myChart"></canvas>
</div>

<script>
    const ctx = document.getElementById('myChart').getContext('2d');

    // แปลงข้อมูล Array จาก PHP ไปเป็น JavaScript JSON
    const departments = <?php echo json_encode($departments); ?>;
    const totals = <?php echo json_encode($totals); ?>;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: departments,
            datasets: [{
                label: 'จำนวนตำแหน่งที่เปิดรับ (ตำแหน่ง)',
                data: totals,
                backgroundColor: [
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(153, 102, 255, 0.8)',
                    'rgba(255, 99, 132, 0.8)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 99, 132, 1)'
                ],
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 2 }
                }
            }
        }
    });
</script>

</body>
</html>