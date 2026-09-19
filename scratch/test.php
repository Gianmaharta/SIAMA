<?php
$conn = new mysqli('localhost', 'root', '', 'db_siama');
$tables = ['access_log', 'activity_log', 'access_logs', 'activity_logs'];
foreach ($tables as $t) {
    $res = $conn->query("DESCRIBE $t");
    if ($res) {
        echo "Table: $t EXISTS\n";
        while($row = $res->fetch_assoc()) {
            echo "  " . $row['Field'] . " - " . $row['Type'] . "\n";
        }
    } else {
        echo "Table: $t - NOT FOUND\n";
    }
    echo "------------------\n";
}
