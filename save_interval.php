<?php
function saveTimeInterval($start_time, $end_time)
{
    $file = "TimerIntervals.json";
    if (!file_exists($file)) {
        file_put_contents($file, json_encode(['intervals' => []]));
    }
    $data = json_decode(file_get_contents($file), true);
    $intervals = $data['intervals'];
    $newInterval = [$start_time, $end_time];
    if (in_array($newInterval, $intervals)) {
        echo "Intervals already exists in the file: Start: $start_time, End: $end_time";
    } else {
        $intervals[] = $newInterval;
        $data['intervals'] = $intervals;
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
        echo "success";
    }
}

function isOverlapping($interval1, $interval2)
{
    $start1 = strtotime($interval1[0]);
    $end1 = strtotime($interval1[1]);
    $start2 = strtotime($interval2[0]);
    $end2 = strtotime($interval2[1]);
    return ($start1 < $end2 && $start2 < $end1);
}

function isValidTimeInterval($start_time, $end_time)
{
    $start = strtotime($start_time);
    $end = strtotime($end_time);
    return $end > $start;
}

function isValidTimeFormat($time)
{
    return preg_match('/^(?:[01]\d|2[0-4]):[0-5]\d$/', $time);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $start_time = $_POST["start_time"];
    $end_time = $_POST["end_time"];
    if (!empty($start_time) && !empty($end_time)) {
        if (isValidTimeFormat($start_time) && isValidTimeFormat($end_time)) {
            if (isValidTimeInterval($start_time, $end_time)) {
                saveTimeInterval($start_time, $end_time);
            } else {
                echo "End time ($end_time) must be greater than start time ($start_time).";
            }
        } else {
            echo "Time must be in the format HH:MM and within the range 00:00 to 24:00.";
        }
    } else {
        echo "Please fill in both time fields.";
    }
}
?>