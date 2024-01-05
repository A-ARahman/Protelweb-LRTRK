<?php
// Include the database connection
include 'config.php';
date_default_timezone_set('Asia/Jakarta');
$dateSelected = date('Y-m-d');

$healthStatusByProfile = [];

// Fetch the latest health status entries for each profile ID
try {
    $stmt = $pdo->query("SELECT profile_id, lora_noseries, HR, SPO2, Temp, date, time FROM health_status ORDER BY date DESC, time DESC");

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $healthStatusByProfile[$row['profile_id']]['records'][] = $row;
        $healthStatusByProfile[$row['profile_id']]['lora_noseries'] = $row['lora_noseries'];
    }

    // Keep only the last 20 entries for each profile ID and reverse them to show the latest first
    foreach ($healthStatusByProfile as $profileId => $data) {
        $records = array_slice($data['records'], 0, 20);
        $healthStatusByProfile[$profileId]['records'] = array_reverse($records);
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
    exit('Error connecting to database');
}

// Function to generate a simple sparkline SVG
function generateSparkline($values, $color) {
    $maxValue = max($values);
    $points = array_map(function($value, $index) use ($maxValue) {
        $x = $index * 5; // 5 pixels per data point for width
        $y = (1 - $value / $maxValue) * 30; // 30 pixels for height
        return "{$x},{$y}";
    }, $values, array_keys($values));

    return '<svg width="100" height="30" xmlns="http://www.w3.org/2000/svg">'
           . '<polyline fill="none" stroke="' . $color . '" stroke-width="2" points="' . implode(' ', $points) . '"/>'
           . '</svg>';
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LORETRACK Health Dashboard</title>
    <!-- Tailwind CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-blue-900 p-3">

    <div class="grid grid-cols-4 gap-6">

        <!-- Sidebar -->
        <div class="bg-blue-600 text-white p-4 rounded-lg h-screen">
            <h1 class="text-2xl font-bold mb-4">LORETRACK</h1>
            <nav>
                <a href="dashboard.php" class="block py-2 px-2 hover:bg-blue-700 rounded-lg">Dashboard</a>
                <a href="regis.php" class="block py-2 px-2 hover:bg-blue-700 rounded-lg">Registration</a>
                <a href="history.php" class="block py-2 px-2 hover:bg-blue-700 rounded-lg">History</a>
                <a href="live-tracking.php" class="block py-2 px-2 hover:bg-blue-700 rounded-lg">Live Tracking</a>
                <a href="health-status.php" class="block py-2 px-2 bg-blue-900 hover:bg-blue-700 rounded-lg">Health Status</a>
                <a href="notification.php" class="block py-2 px-2 hover:bg-blue-700 rounded-lg">Status</a>
                <a href="profile.php" class="block py-2 px-2 hover:bg-blue-700 rounded-lg">Climbers Profile</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="col-span-3 space-y-6">
            <div class="flex justify-between items-center">
            <form action="" method="post">
            <input type="date" name="selectedDate" value="<?= $dateSelected ?>" class="p-2 border rounded-md" onchange="this.form.submit()">
            </form>
                <div class="flex items-center">
                    <input type="search" placeholder="Search" class="p-2 border rounded-md">
                    <!-- Notification, Profile, and Settings Icons -->
                    <a href="notification.php" class="ml-3 bg-gray-300 rounded-full h-8 w-8 flex items-center justify-center text-gray-600">N</a>
                    <!-- ... -->
                    <div class="ml-3 relative">
                        <div class="bg-gray-300 rounded-full h-8 w-8 flex items-center justify-center text-gray-600 cursor-pointer profile-icon">P</div>
                        <!-- Profile Dropdown -->
                        <div class="absolute right-0 w-48 mt-2 py-2 bg-white border rounded shadow-xl profile-dropdown hidden">
                            <a href="user-setting.php" class="block px-4 py-2 text-gray-800 hover:bg-indigo-500 hover:text-white">Account Settings</a>
                            <a href="login.php" class="block px-4 py-2 text-gray-800 hover:bg-indigo-500 hover:text-white">Logout</a>
                        </div>
                    </div>
                        <!-- ... -->

                    <a href="#settings" class="ml-3 bg-gray-300 rounded-full h-8 w-8 flex items-center justify-center text-gray-600">S</a>
                </div>
            </div>


            <!-- Health Status Entries per Profile ID -->
                <?php foreach ($healthStatusByProfile as $profileId => $data): ?>
                    <div class="bg-white p-6 rounded-lg shadow overflow-hidden mb-6">
                        <h3 class="text-lg font-semibold mb-4">Profile ID: <?= htmlspecialchars($profileId) ?> - LORA Series: <?= htmlspecialchars($data['lora_noseries']) ?></h3>
                        <div class="grid grid-cols-4 gap-4">
                            <div>
                                <div>HR: <?= htmlspecialchars(end($data['records'])['HR']) ?> bpm</div>
                                <?= generateSparkline(array_column($data['records'], 'HR'), 'red') ?>
                            </div>
                            <div>
                                <div>SPO2: <?= htmlspecialchars(end($data['records'])['SPO2']) ?>%</div>
                                <?= generateSparkline(array_column($data['records'], 'SPO2'), 'blue') ?>
                            </div>
                            <div>
                                <div>Temp: <?= htmlspecialchars(end($data['records'])['Temp']) ?>°C</div>
                                <?= generateSparkline(array_column($data['records'], 'Temp'), 'orange') ?>
                            </div>
                            <div>
                                <div>Date: <?= htmlspecialchars(end($data['records'])['date']) ?></div>
                                <div>Time: <?= htmlspecialchars(end($data['records'])['time']) ?></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>
