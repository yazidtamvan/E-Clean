<?php
// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "regisnlogin";

$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Cek jika form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $preferred_date = $_POST['date'];
    $preferred_time = $_POST['time'];
    $cleaning_type = $_POST['cleaning_type'];
    $duration = $_POST['duration'];
    $total_cost = $_POST['total_cost'];
    $payment_method = $_POST['payment_method'];
    $payment_number = $_POST['payment_number'] ?? null;

    // Masukkan data ke database
    $sql = "INSERT INTO orders (name, email, phone, address, date, time, cleaning_type, duration, total_cost, pay_method, pay_num)
            VALUES ('$name', '$email', '$phone', '$address', '$preferred_date', '$preferred_time', '$cleaning_type', $duration, $total_cost, '$payment_method', '$payment_number')";
    echo $preferred_date;
    echo $cleaning_type;
    if ($conn->query($sql) === TRUE) {
        // Jika berhasil, jalankan JavaScript untuk memperbarui localStorage dan pindah ke dashboard
        echo "<script>
            let orderHistory = JSON.parse(localStorage.getItem('orderHistory')) || [];
            orderHistory.push({
                date: '$preferred_date',
                service: '$cleaning_type',
                amount: 'Rp ' + parseInt('$total_cost').toLocaleString(),
                paymentMethod: '$payment_method',
                status: 'Completed'
            });
            localStorage.setItem('orderHistory', JSON.stringify(orderHistory));
            window.location.href = 'dashboard_user.html'; // Redirect setelah submit
        </script>";
    } else {
        echo "<p class='text-red-500 text-center'>Error: " . $sql . "<br>" . $conn->error . "</p>";
    }
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Cleaning Service</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-gray-800 shadow-lg text-white">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a class="text-xl font-bold" href="index.html">
                E-Clean
            </a>
        </div>
        <nav class="absolute top-4 right-4">
            <a href="dashboard_user.html" class="text-white text-lg font-semibold hover:text-gray-300">Back</a>
        </nav>
    </nav>

    <header class="bg-blue-800 text-white text-center py-12">
        <h1 class="text-4xl font-bold">Order Cleaning Service</h1>
        <p class="mt-4 text-lg">Fill out the form below to place your order.</p>
    </header>

    <main class="py-12">
        <div class="container mx-auto px-4">
            <form class="bg-white shadow-lg rounded-lg p-8" method="POST" action="">
                <!-- Customer Information -->
                <h2 class="text-2xl font-bold mb-6">Customer Information</h2>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="name">Full Name</label>
                    <input class="border border-gray-300 p-3 w-full rounded" type="text" id="name" name="name" placeholder="Enter your name" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="email">Email</label>
                    <input class="border border-gray-300 p-3 w-full rounded" type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="phone">Phone Number</label>
                    <input class="border border-gray-300 p-3 w-full rounded" type="text" id="phone" name="phone" placeholder="Enter your phone number" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="address">Address</label>
                    <textarea class="border border-gray-300 p-3 w-full rounded" id="address" name="address" rows="3" placeholder="Enter your address" required></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="date">Preferred Date</label>
                    <input class="border border-gray-300 p-3 w-full rounded" type="date" id="date" name="date" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="time">Preferred Time</label>
                    <input class="border border-gray-300 p-3 w-full rounded" type="time" id="time" name="time" required>
                </div>

                <!-- Cleaning Type Selection -->
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="cleaningType">Select Cleaning Type</label>
                    <select class="border border-gray-300 p-3 w-full rounded" id="cleaningType" name="cleaning_type" required onchange="calculateTotal()">
                        <option value="" disabled selected>Select a cleaning type</option>
                        <option value="Residential Cleaning" data-rate="30000">Residential Cleaning (Rp 30,000 per hour)</option>
                        <option value="Commercial Cleaning" data-rate="40000">Commercial Cleaning (Rp 40,000 per hour)</option>
                        <option value="Friendly Cleaning" data-rate="50000">Eco-Friendly Cleaning (Rp 50,000 per hour)</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="duration">Duration (in hours)</label>
                    <input class="border border-gray-300 p-3 w-full rounded" type="number" id="duration" name="duration" placeholder="Enter duration in hours" required min="1" oninput="calculateTotal()">
                </div>

                <!-- Cost Information -->
                <div class="mb-4">
                    <p class="text-gray-600">Estimated Total: <span id="totalCost" class="font-bold">Rp 0</span></p>
                    <input type="hidden" id="total_cost" name="total_cost" value="0">
                </div>

                <!-- Payment Information -->
                <h2 class="text-2xl font-bold mb-6">Payment Information</h2>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="paymentMethod">Select Payment Method</label>
                    <select class="border border-gray-300 p-3 w-full rounded" id="paymentMethod" name="payment_method" required>
                        <option value="" disabled selected>Select a payment method</option>
                        <option value="cash">Cash</option>
                        <option value="gopay">GoPay</option>
                        <option value="dana">Dana</option>
                    </select>
                </div>

                <!-- GoPay Payment -->
                <div id="gopayDetails" class="hidden">
                    <label class="block text-gray-700 mb-2" for="gopayNumber">GoPay Number</label>
                    <input class="border border-gray-300 p-3 w-full rounded" type="text" id="gopayNumber" name="payment_number" placeholder="Enter your GoPay number">
                </div>

                <!-- Dana Payment -->
                <div id="danaDetails" class="hidden">
                    <label class="block text-gray-700 mb-2" for="danaNumber">Dana Number</label>
                    <input class="border border-gray-300 p-3 w-full rounded" type="text" id="danaNumber" name="payment_number" placeholder="Enter your Dana number">
                </div>

                <button class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition" type="submit">
                    Submit Order
                </button>
            </form>
        </div>
    </main>

    <footer class="bg-gray-800 text-white text-center py-4">
        <p>&copy; 2024 E-Clean. All rights reserved.</p>
    </footer>

    <script>
        const paymentMethodSelect = document.getElementById('paymentMethod');
        const gopayDetails = document.getElementById('gopayDetails');
        const danaDetails = document.getElementById('danaDetails');
        const totalCostElement = document.getElementById('totalCost');
        const durationInput = document.getElementById('duration');
        const cleaningTypeSelect = document.getElementById('cleaningType');
        const totalCostInput = document.getElementById('total_cost');

        paymentMethodSelect.addEventListener('change', function() {
            gopayDetails.classList.add('hidden');
            danaDetails.classList.add('hidden');

            if (this.value === 'gopay') {
                gopayDetails.classList.remove('hidden');
            } else if (this.value === 'dana') {
                danaDetails.classList.remove('hidden');
            }
        });

        function calculateTotal() {
            const cleaningTypeSelect = document.getElementById('cleaningType');
    const selectedOption = cleaningTypeSelect.options[cleaningTypeSelect.selectedIndex];
    const ratePerHour = parseInt(selectedOption.getAttribute('data-rate')) || 0; // Ambil nilai dari data-rate
    const durationInput = document.getElementById('duration');
    const duration = parseInt(durationInput.value) || 0; 
            const total = duration * ratePerHour;
            totalCostElement.textContent = `Rp ${total.toLocaleString()}`;
            totalCostInput.value = total;
        }
    </script>
</body>
</html>
