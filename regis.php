<?php
include 'config.php';

date_default_timezone_set('Asia/Jakarta');
$dateSelected = date('Y-m-d');
// Fetch lora_noseries options from the lora table
$loraOptions = [];
try {
    $stmt = $pdo->query("SELECT lora_noseries FROM lora");
    $loraOptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log($e->getMessage());
}

// Handle the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lora_noseries = $_POST['lora_noseries'];
    $nik = $_POST['nik'];
    $name = $_POST['name'];
    $address = $_POST['address'];
    $phone_number = $_POST['phone_number'];
    $time = date('H:i:s'); // Current time
    $date = date('Y-m-d'); // Current date

    // Start transaction
    $pdo->beginTransaction();

    try {
        // Insert the new profile
        $sqlProfile = "INSERT INTO profile (lora_noseries, NIK, name, address, phone_number, time, date) 
                       VALUES (:lora_noseries, :nik, :name, :address, :phone_number, :time, :date)";
        $stmtProfile = $pdo->prepare($sqlProfile);
        $stmtProfile->execute([
            ':lora_noseries' => $lora_noseries,
            ':nik' => $nik,
            ':name' => $name,
            ':address' => $address,
            ':phone_number' => $phone_number,
            ':time' => $time,
            ':date' => $date
        ]);
        $profileId = $pdo->lastInsertId();

        // Insert "Starting" status into notification
        $sqlNotification = "INSERT INTO notification (lora_noseries, status, time, date, profile_id) 
                            VALUES (:lora_noseries, 'Starting', :time, :date, :profile_id)";
        $stmtNotification = $pdo->prepare($sqlNotification);
        $stmtNotification->execute([
            ':lora_noseries' => $lora_noseries,
            ':time' => $time,
            ':date' => $date,
            ':profile_id' => $profileId
        ]);

        // Add history entry for POS 1
        // $sqlHistory = "INSERT INTO history (lora_noseries, pos_node, time, date, profile_id) 
        //                VALUES (:lora_noseries, 'POS 1', :time, :date, :profile_id)";
        // $stmtHistory = $pdo->prepare($sqlHistory);
        // $stmtHistory->execute([
        //     ':lora_noseries' => $lora_noseries,
        //     ':time' => $time,
        //     ':date' => $date,
        //     ':profile_id' => $profileId
        // ]);

        // Add default user position
        $sqlUserPosition = "INSERT INTO user_position (lora_noseries, latitude, longitude, time, date, profile_id) 
                            VALUES (:lora_noseries, -7.284531, 112.796682, :time, :date, :profile_id)";
        $stmtUserPosition = $pdo->prepare($sqlUserPosition);
        $stmtUserPosition->execute([
            ':lora_noseries' => $lora_noseries,
            ':time' => $time,
            ':date' => $date,
            ':profile_id' => $profileId
        ]);

        // Commit transaction
        $pdo->commit();
        echo "New profile registered successfully with initial status, history, and position.";
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LORETRACK - Registration</title>
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
                <a href="regis.php" class="block py-2 px-2 bg-blue-900 hover:bg-blue-700 rounded-lg">Registration</a>
                <a href="history.php" class="block py-2 px-2 hover:bg-blue-700 rounded-lg">History</a>
                <a href="live-tracking.php" class="block py-2 px-2 hover:bg-blue-700 rounded-lg">Live Tracking</a>
                <a href="health-status.php" class="block py-2 px-2 hover:bg-blue-700 rounded-lg">Health Status</a>
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
            <!-- Registration Form -->
            <div class="w-full  mx-auto bg-blue-500 p-10 border rounded-md">
                <form action="regis.php" method="post">
                    <div class="mb-4">
                        <label for="lora_noseries" class="block text-white mb-2">LORA No Series</label>
                        <select name="lora_noseries" id="lora_noseries" class="w-full p-2 border rounded-md">
                            <?php foreach ($loraOptions as $option): ?>
                                <option value="<?= htmlspecialchars($option['lora_noseries']) ?>">
                                    <?= htmlspecialchars($option['lora_noseries']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="nik" class="block text-white mb-2">NIK</label>
                        <input type="text" name="nik" id="nik" class="w-full p-2 border rounded-md">
                    </div>
                    <div class="mb-4">
                        <label for="name" class="block text-white mb-2">Name</label>
                        <input type="text" name="name" id="name" class="w-full p-2 border rounded-md">
                    </div>
                    <div class="mb-4">
                        <label for="address" class="block text-white mb-2">Address</label>
                        <input type="text" name="address" id="address" class="w-full p-2 border rounded-md">
                    </div>
                    <div class="mb-4">
                        <label for="phone_number" class="block text-white mb-2">Phone Number</label>
                        <input type="text" name="phone_number" id="phone_number" class="w-full p-2 border rounded-md">
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-900 text-white py-2 px-4 rounded-md hover:bg-blue-600">Submit</button>
                    </div>
                </form>
            </div>

<script src="script.js"></script>
</body>

</html>
