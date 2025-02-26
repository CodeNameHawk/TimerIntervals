<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $intervalsToDelete = json_decode($_POST['intervals'], true);
    $file = "TimerIntervals.json";
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
        $intervals = $data['intervals'];
        $newIntervals = array_filter($intervals, function($interval) use ($intervalsToDelete) {
            return !in_array("{$interval[0]} {$interval[1]}", $intervalsToDelete);
        });
        $data['intervals'] = array_values($newIntervals);
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
        echo "success";
    } else {
        echo "file not found";
    }
}
?>
