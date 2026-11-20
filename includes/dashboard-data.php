<?php
function dashboard_stats(): array
{
    global $conn;
    $stats = ['total' => 0, 'normal' => 0, 'risk' => 0, 'stunting' => 0, 'posyandu' => 8, 'measurements' => 0];
    $result = $conn->query("SELECT COUNT(*) total, SUM(status = 'Normal') normal_count, SUM(status = 'Berisiko') risk_count, SUM(status = 'Stunting') stunting_count FROM balita");
    if ($result && ($row = $result->fetch_assoc())) {
        $stats['total'] = (int) $row['total'];
        $stats['normal'] = (int) $row['normal_count'];
        $stats['risk'] = (int) $row['risk_count'];
        $stats['stunting'] = (int) $row['stunting_count'];
    }
    $result = $conn->query('SELECT COUNT(*) total FROM pengukuran');
    if ($result) $stats['measurements'] = (int) $result->fetch_assoc()['total'];
    return $stats;
}

function posyandu_chart_data(): array
{
    global $conn;
    $data = array_fill(1, 8, 0);
    $result = $conn->query('SELECT posyandu_id, COUNT(*) total FROM balita GROUP BY posyandu_id');
    while ($result && ($row = $result->fetch_assoc())) {
        $data[(int) $row['posyandu_id']] = (int) $row['total'];
    }
    return array_values($data);
}

function gender_chart_data(): array
{
    global $conn;
    $data = ['L' => 0, 'P' => 0];
    $result = $conn->query("SELECT jk, COUNT(*) total FROM balita GROUP BY jk");
    while ($result && ($row = $result->fetch_assoc())) $data[$row['jk']] = (int) $row['total'];
    return array_values($data);
}

function average_measurement_data(): array
{
    global $conn;
    $data = ['weight' => array_fill(1, 8, 0), 'height' => array_fill(1, 8, 0)];
    $result = $conn->query('SELECT posyandu_id, AVG(berat) avg_weight, AVG(tinggi) avg_height FROM balita GROUP BY posyandu_id');
    while ($result && ($row = $result->fetch_assoc())) {
        $index = (int) $row['posyandu_id'];
        $data['weight'][$index] = round((float) $row['avg_weight'], 1);
        $data['height'][$index] = round((float) $row['avg_height'], 1);
    }
    return ['weight' => array_values($data['weight']), 'height' => array_values($data['height'])];
}

function recent_children(int $limit = 5): mysqli_result
{
    global $conn;
    $limit = max(1, min($limit, 20));
    return $conn->query("SELECT b.*, p.nama posyandu_nama FROM balita b JOIN posyandu p ON p.id = b.posyandu_id ORDER BY b.created_at DESC LIMIT {$limit}");
}

function growth_series(int $id_balita): array
{
    global $conn;
    $series = ['labels' => [], 'weight' => [], 'height' => []];
    $statement = $conn->prepare('SELECT tanggal, berat, tinggi FROM pengukuran WHERE id_balita = ? ORDER BY tanggal ASC, id ASC');
    $statement->bind_param('i', $id_balita);
    $statement->execute();
    $result = $statement->get_result();
    while ($row = $result->fetch_assoc()) {
        $series['labels'][] = date('d M', strtotime($row['tanggal']));
        $series['weight'][] = (float) $row['berat'];
        $series['height'][] = (float) $row['tinggi'];
    }
    $statement->close();
    return $series;
}
