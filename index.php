<!DOCTYPE html>
<html>

<head>
    <title>Timers Intervals</title>
    <link rel="stylesheet" type="text/css" href="styles/styles.css">
    <script>
        function addColon(input, nextInputId) {
            if (input.value.length == 2) {
                input.value += ":";
            }
            if (input.value.length >= 5) {
                const nextInput = document.getElementById(nextInputId);
                nextInput.focus();
                if (nextInput.value.length > 0) {
                    nextInput.select();
                }
            }
        }

        function validateInput(event) {
            const regex = /^[0-9:]*$/;
            if (!regex.test(event.target.value)) {
                event.target.value = event.target.value.replace(/[^0-9:]/g, '');
            }
            const colonCount = (event.target.value.match(/:/g) || []).length;
            if (colonCount > 1) {
                event.target.value = event.target.value.replace(/:/g, '');
                event.target.value = event.target.value.slice(0, 2) + ':' + event.target.value.slice(2);
            }
            if (event.target.value.length === 2 && event.target.value[1] === ':') {
                event.target.value = '0' + event.target.value;
            }
            if (event.target.value.length === 4 && event.target.value[3] === ':') {
                event.target.value = event.target.value.slice(0, 3) + '0' + event.target.value.slice(3);
            }
        }

        function formatTime(input) {
            const parts = input.value.split(':');
            if (parts.length === 2) {
                if (parts[0].length === 1) {
                    parts[0] = '0' + parts[0];
                }
                if (parts[1].length === 1) {
                    parts[1] = '0' + parts[1];
                }
                input.value = parts.join(':');
            }
            if (input.value && !/^\d{2}:\d{2}$/.test(input.value)) {
                document.getElementById('message').innerHTML = '<p>Time must be in the format HH:MM</p>';
                input.focus();
            }
        }

        function updateDeleteButton() {
            const deleteBtn = document.getElementById('deleteSelected');
            const selected = document.querySelectorAll('.selected');
            if (selected.length > 0) {
                deleteBtn.disabled = false;
            } else {
                deleteBtn.disabled = true;
            }
        }

        function selectInterval(event) {
            event.target.classList.toggle('selected');
            updateDeleteButton();
        }

        function deleteInterval() {
            const selected = document.querySelectorAll('.selected');
            if (selected.length > 0) {
                const intervals = Array.from(selected).map(item => item.textContent);
                const formData = new FormData();
                formData.append('intervals', JSON.stringify(intervals));

                fetch('delete_interval.php', {
                    method: 'POST',
                    body: formData
                }).then(response => response.text()).then(data => {
                    if (data === 'success') {
                        selected.forEach(item => item.remove());
                        updateDeleteButton();
                        calculateNewList();
                    } else {
                        document.getElementById('message').innerHTML = '<p>Failed to delete intervals</p>';
                        setTimeout(() => { document.getElementById('message').innerHTML = ''; }, 5000);
                    }
                });
            } else {
                document.getElementById('message').innerHTML = '<p>Please select intervals to delete</p>';
                setTimeout(() => { document.getElementById('message').innerHTML = ''; }, 5000);
            }
        }

        function calculateNewList() {
            const intervalsList = document.getElementById('intervals-list');
            const calculatedIntervalsList = document.getElementById('calculated-intervals-list');
            calculatedIntervalsList.innerHTML = ''; // Очистим список перед добавлением новых элементов

            const intervals = Array.from(intervalsList.getElementsByTagName('li'));
            if (intervals.length > 0) {
                let currentInterval = intervals[0].textContent.split(' ');
                let currentStartTime = currentInterval[0];
                let currentEndTime = currentInterval[1];
                let rememberedEndTime = currentEndTime;

                for (let i = 1; i < intervals.length; i++) {
                    const nextInterval = intervals[i].textContent.split(' ');
                    const nextStartTime = nextInterval[0];
                    const nextEndTime = nextInterval[1];

                    if (currentEndTime >= nextStartTime) {
                        rememberedEndTime = nextEndTime > rememberedEndTime ? nextEndTime : rememberedEndTime;
                        currentEndTime = rememberedEndTime;
                    } else {
                        const li = document.createElement('li');
                        li.textContent = `${currentStartTime} ${currentEndTime}`;
                        calculatedIntervalsList.appendChild(li);
                        currentStartTime = nextStartTime;
                        currentEndTime = nextEndTime;
                        rememberedEndTime = currentEndTime;
                    }
                }

                const li = document.createElement('li');
                li.textContent = `${currentStartTime} ${currentEndTime}`;

                calculatedIntervalsList.appendChild(li);
            }

            console.log('Calculating new list...');
            updateSaveButton();
        }

        function addInterval(event) {
            event.preventDefault();
            const startTimeInput = document.getElementById('start_time');
            const endTimeInput = document.getElementById('end_time');
            const startTime = startTimeInput.value;
            const endTime = endTimeInput.value;

            if (!/^\d{2}:\d{2}$/.test(startTime)) {
                document.getElementById('message').innerHTML = '<p>Start Time must be in the format HH:MM</p>';
                setTimeout(() => { document.getElementById('message').innerHTML = ''; }, 5000);
                startTimeInput.focus();
                startTimeInput.select();
                return;
            }

            if (!/^\d{2}:\d{2}$/.test(endTime)) {
                document.getElementById('message').innerHTML = '<p>End Time must be in the format HH:MM</p>';
                setTimeout(() => { document.getElementById('message').innerHTML = ''; }, 5000);
                startTimeInput.focus();
                startTimeInput.select();
                return;
            }

            const formData = new FormData();
            formData.append('start_time', startTime);
            formData.append('end_time', endTime);

            fetch('save_interval.php', {
                method: 'POST',
                body: formData
            }).then(response => response.text()).then(data => {
                if (data === 'success') {
                    const intervalsList = document.getElementById('intervals-list');
                    const li = document.createElement('li');
                    li.textContent = `${startTime} ${endTime}`;
                    li.addEventListener('click', selectInterval);
                    intervalsList.appendChild(li);

                    // Сортировка списка
                    const items = Array.from(intervalsList.getElementsByTagName('li'));
                    items.sort((a, b) => {
                        const [startA, endA] = a.textContent.split(' ');
                        const [startB, endB] = b.textContent.split(' ');
                        const startATime = new Date(`1970-01-01T${startA}Z`).getTime();
                        const startBTime = new Date(`1970-01-01T${startB}Z`).getTime();
                        if (startATime === startBTime) {
                            const endATime = new Date(`1970-01-01T${endA}Z`).getTime();
                            const endBTime = new Date(`1970-01-01T${endB}Z`).getTime();
                            return endATime - endBTime;
                        }
                        return startATime - startBTime;
                    });
                    intervalsList.innerHTML = '';
                    items.forEach(item => intervalsList.appendChild(item));

                    calculateNewList();
                    document.getElementById('message').innerHTML = `<p>Timer intervals added successfully: StartTime: ${startTime}, EndTime: ${endTime}</p>`;
                    setTimeout(() => { document.getElementById('message').innerHTML = ''; }, 5000);
                    startTimeInput.value = '';
                    endTimeInput.value = '';
                    startTimeInput.focus();
                } else {
                    document.getElementById('message').innerHTML = `<p>${data}</p>`;
                    setTimeout(() => { document.getElementById('message').innerHTML = ''; }, 5000);
                    startTimeInput.focus();
                    startTimeInput.select();
                }
            });
        }

        function updateSaveButton() {
            const saveBtn = document.getElementById('saveFile');
            const calculatedIntervals = document.querySelectorAll('#calculated-intervals-list li');
            if (calculatedIntervals.length > 0) {
                saveBtn.disabled = false;
            } else {
                saveBtn.disabled = true;
            }
        }

        function saveToFile() {
            const calculatedIntervals = document.querySelectorAll('#calculated-intervals-list li');
            const intervals = Array.from(calculatedIntervals).map(item => item.textContent);
            const blob = new Blob([intervals.join('\n')], { type: 'text/plain' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'timerintervals.txt';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }

        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById("start_time").focus();
            document.querySelectorAll('#intervals-list li').forEach(li => {
                li.addEventListener('click', selectInterval);
            });

            document.querySelector('form').addEventListener('submit', addInterval);

            document.querySelectorAll('input[type="text"]').forEach(input => {
                input.addEventListener('keydown', function (event) {
                    if (event.key === 'ArrowLeft' || event.key === 'ArrowUp') {
                        const position = input.selectionStart;
                        if (position > 0) {
                            setTimeout(() => {
                                input.setSelectionRange(0, position);
                            }, 0);
                        }
                        validateInput(event);
                    } else if (event.key === 'ArrowRight' || event.key === 'ArrowDown') {
                        const position = input.selectionStart;
                        if (position < input.value.length) {
                            setTimeout(() => {
                                input.setSelectionRange(position, input.value.length);
                            }, 0);
                        }
                        validateInput(event);
                    }
                });

                input.addEventListener('input', function (event) {
                    validateInput(event);
                });

                input.addEventListener('blur', function (event) {
                    if (event.target.value.length > 5) {
                        event.target.value = event.target.value.slice(0, 5);
                    }
                });
            });

            calculateNewList();
            updateSaveButton();
        });
    </script>
</head>

<body>
    <div class="container">
        <div class="interval-list">
            <h3>Saved<br>Intervals:</h3>
            <ul id="intervals-list">
                <?php
                $file = "TimerIntervals.json";
                if (file_exists($file))
                {
                    $data = json_decode(file_get_contents($file), true);
                    $intervals = $data['intervals'];
                    usort($intervals, function ($a, $b)
                    {
                        $startA = strtotime($a[0]);
                        $startB = strtotime($b[0]);
                        if ($startA === $startB)
                        {
                            $endA = strtotime($a[1]);
                            $endB = strtotime($b[1]);
                            return $endA - $endB;
                        }
                        return $startA - $startB;
                    });
                    foreach ($intervals as $interval)
                    {
                        echo "<li>{$interval[0]} {$interval[1]}</li>";
                    }
                } else
                {
                    echo "<li>No intervals saved yet.</li>";
                }
                ?>
            </ul>
            <button id="deleteSelected" disabled onclick="deleteInterval()">Delete Selected Intervals</button>
        </div>

        <div class="interval-list">
            <h3>Calculated<br>Intervals:</h3>
            <ul id="calculated-intervals-list">
                <!-- Здесь будет выводиться список, полученный из interval-list -->
            </ul>
            <button id="saveFile" disabled onclick="saveToFile()">Save to file</button>
        </div>

        <div class="form-panel">
            <h2>Add timer intervals</h2>
            <form method="post" action="">
                <div class="form-group">
                    <label for="start_time">Start Time:</label>
                    <input type="text" id="start_time" name="start_time"
                        oninput="addColon(this, 'end_time'); validateInput(event);" onblur="formatTime(this);">
                </div>
                <div class="form-group">
                    <label for="end_time">End Time:</label>
                    <input type="text" id="end_time" name="end_time"
                        oninput="addColon(this, 'submit_button'); validateInput(event);" onblur="formatTime(this);">
                </div>
                <div class="submit-button">
                    <input type="submit" id="submit_button" value="Add timer intervals">
                </div>
            </form>
            <div id="message"></div>
        </div>
    </div>
    <?php
    function saveTimeInterval($start_time, $end_time)
    {
        $file = "TimerIntervals.json";
        if (!file_exists($file))
        {
            file_put_contents($file, json_encode(['intervals' => []])); // Create file if it doesn't exist
        }
        $data = json_decode(file_get_contents($file), true);
        $intervals = $data['intervals'];
        $newInterval = [$start_time, $end_time];
        if (in_array($newInterval, $intervals))
        {
            echo "<script>document.getElementById('message').innerHTML = '<p>Intervals already exists in the file: Start: $start_time, End: $end_time</p>';</script>";
            echo "<script>setTimeout(() => { document.getElementById('message').innerHTML = ''; }, 5000);</script>";
        } else
        {
            $intervals[] = $newInterval;
            $data['intervals'] = $intervals;
            file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
            sortIntervals($file);
            echo "<script>document.getElementById('message').innerHTML = '<p>Timer intervals added: Start: $start_time, End: $end_time</p>';</script>";
            echo "<script>setTimeout(() => { document.getElementById('message').innerHTML = ''; }, 5000);</script>";
            echo "<script>
                var intervalsList = document.getElementById('intervals-list');
                intervalsList.innerHTML = '';
                var data = " . json_encode(json_decode(file_get_contents($file), true)) . ";
                var intervals = data.intervals;
                intervals.sort(function(a, b) {
                    var startA = new Date('1970-01-01T' + a[0] + 'Z');
                    var startB = new Date('1970-01-01T' + b[0] + 'Z');
                    if (startA.getTime() === startB.getTime()) {
                        var endA = new Date('1970-01-01T' + a[1] + 'Z');
                        var endB = new Date('1970-01-01T' + b[1] + 'Z');
                        return endA - endB;
                    }
                    return startA - startB;
                });
                intervals.forEach(function(interval) {
                    var li = document.createElement('li');
                    li.textContent = interval[0] + ' ' + interval[1];
                    li.addEventListener('click', selectInterval);
                    intervalsList.appendChild(li);
                });
            </script>";
        }
    }

    function sortIntervals($file)
    {
        $data = json_decode(file_get_contents($file), true);
        $intervals = $data['intervals'];
        usort($intervals, function ($a, $b)
        {
            $startA = strtotime($a[0]);
            $startB = strtotime($b[0]);
            if ($startA === $startB)
            {
                $endA = strtotime($a[1]);
                $endB = strtotime($b[1]);
                return $endA - $endB;
            }
            return $startA - $startB;
        });
        $data['intervals'] = $intervals;
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
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

    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        $start_time = $_POST["start_time"];
        $end_time = $_POST["end_time"];
        if (!empty($start_time) && !empty($end_time))
        {
            if (isValidTimeFormat($start_time) && isValidTimeFormat($end_time))
            {
                if (isValidTimeInterval($start_time, $end_time))
                {
                    saveTimeInterval($start_time, $end_time);
                } else
                {
                    echo "<script>
                            document.getElementById('message').innerHTML = '<p>End time ($end_time) must be greater than start time ($start_time).</p>';
                            setTimeout(function() {
                                document.getElementById('message').innerHTML = '';
                            }, 5000);
                          </script>";
                }
            } else
            {
                echo "<script>
                        document.getElementById('message').innerHTML = '<p>Time must be in the format HH:MM and within the range 00:00 to 24:00.</p>';
                        setTimeout(function() {
                            document.getElementById('message').innerHTML = '';
                        }, 5000);
                      </script>";
            }
        } else
        {
            echo "<script>
                    document.getElementById('message').innerHTML = '<p>Please fill in both time fields.</p>';
                    setTimeout(function() {
                        document.getElementById('message').innerHTML = '';
                    }, 5000);
                  </script>";
        }
    }
    ?>
</body>

</html>