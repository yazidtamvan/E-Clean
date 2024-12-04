<?php
ini_set('display_errors',1);
ini_set('display_setup_errors',1);
error_reporting(E_ALL);
session_start();

// Connect to the database
include "database.php";
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Clean - Home</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }

        /* Mobile menu styles */
        @media (max-width: 768px) {
            #mobile-menu {
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: #1f2937;
                padding: 1rem;
                z-index: 40;
            }

            #mobile-menu a {
                display: block;
                padding: 0.75rem;
                margin: 0.5rem 0;
                text-align: center;
                color: #e5e7eb;
                text-decoration: none;
            }

            #mobile-menu a:hover {
                background-color: #374151;
            }

            #mobile-menu .btn-login {
                background-color: #3b82f6;
                padding: 0.75rem;
                color: white;
                border-radius: 0.375rem;
                display: block;
                margin-top: 1rem;
                text-align: center;
            }
            #mobile-menu .btn-login:hover {
                background-color: #2563eb;
            }
        }
    </style>
</head>
<body class="bg-gray-100 font-roboto">
    <nav class="bg-gray-800 text-white relative">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <!-- Logo -->
                <div class="flex items-center">
                    <svg class="w-8 h-8 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20.437 17.328a9.97 9.97 0 0 0 1.563-5.328c0-5.523-4.477-10-10-10S2 6.477 2 12s4.477 10 10 10a9.967 9.967 0 0 0 6.192-2.166c.577.737 1.444 1.166 2.363 1.166a3 3 0 0 0 3-3c0-1.038-.67-1.924-1.616-2.272zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8z"/>
                    </svg>
                    <span class="text-xl font-bold">E-Clean</span>
                </div>

                <!-- Hamburger Menu Button -->
                <button class="md:hidden focus:outline-none" onclick="toggleMenu()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                    </svg>
                </button>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="index.html" class="text-gray-300 hover:text-white">Home</a>
                    <a href="aboutus.html" class="text-gray-300 hover:text-white">About Us</a>
                    <a href="services.html" class="text-gray-300 hover:text-white">Services</a>
                    <a href="mailto:eclean601@gmail.com" class="text-gray-300 hover:text-white">Contact us</a>
                    <a href="logout.php" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md">Logout</a>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden">
                <a href="index.html">Home</a>
                <a href="aboutus.html">About Us</a>
                <a href="services.html">Services</a>
                <a href="mailto:eclean601@gmail.com">Contact us</a>
                <a href="logout.php" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
</head>
  <!-- Main Content -->
   <!-- Main Content -->
<main class="py-8 sm:py-12">
    <div class="container mx-auto max-w-screen-lg px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Residential Cleaning Card -->
            <div class="bg-white rounded-lg shadow-lg text-center overflow-hidden transform hover:scale-105 hover:shadow-xl transition-transform duration-300 group">
                <div class="relative overflow-hidden">
                    <img alt="Residential Cleaning" class="w-full h-48 sm:h-56 md:h-48 lg:h-52 object-cover transition-transform duration-300 group-hover:scale-110" src="https://storage.googleapis.com/a1aa/image/1ptGoWteeXiB9k1AW6Hg47BMka4CGvQGe8HvbhjnSzRkqtGnA.jpg"/>
                </div>
                <div class="p-4 sm:p-6">
                    <h2 class="text-lg sm:text-xl lg:text-2xl font-bold mb-2 text-blue-900" data-en="Residential Cleaning" data-id="Pembersihan Perumahan">Residential Cleaning</h2>
                    <p class="text-gray-700 text-sm sm:text-base mb-4" data-en="Keep your home sparkling clean with our expert residential services." data-id="Jagalah rumah Anda tetap bersih berkilau dengan layanan perumahan ahli kami.">Keep your home sparkling clean with our expert residential services.</p>
                    <a class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition text-sm sm:text-base inline-block" href="Order.php" aria-label="Order Residential Cleaning" data-en="Order Now" data-id="Pesan Sekarang">
                        Order Now
                    </a>
                </div>
            </div>

            <!-- Commercial Cleaning Card -->
            <div class="bg-white rounded-lg shadow-lg text-center overflow-hidden transform hover:scale-105 hover:shadow-xl transition-transform duration-300 group">
                <div class="relative overflow-hidden">
                    <img alt="Commercial Cleaning" class="w-full h-48 sm:h-56 md:h-48 lg:h-52 object-cover transition-transform duration-300 group-hover:scale-110" src="https://storage.googleapis.com/a1aa/image/f858ZoMmMF2iMykSX5c34VJwFD2q5Udbhl0RP8jM2T6parxJA.jpg"/>
                </div>
                <div class="p-4 sm:p-6">
                    <h2 class="text-lg sm:text-xl lg:text-2xl font-bold mb-2 text-blue-900" data-en="Commercial Cleaning" data-id="Pembersihan Komersial">Commercial Cleaning</h2>
                    <p class="text-gray-700 text-sm sm:text-base mb-4" data-en="Maintain a professional and hygienic workspace with our tailored commercial cleaning." data-id="Jaga ruang kerja tetap profesional dan higienis dengan layanan pembersihan komersial khusus kami.">Maintain a professional and hygienic workspace with our tailored commercial cleaning.</p>
                    <a class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition text-sm sm:text-base inline-block" href="Order.php" aria-label="Order Commercial Cleaning" data-en="Order Now" data-id="Pesan Sekarang">
                        Order Now
                    </a>
                </div>
            </div>

            <!-- Eco Friendly Cleaning Card -->
            <div class="bg-white rounded-lg shadow-lg text-center overflow-hidden transform hover:scale-105 hover:shadow-xl transition-transform duration-300 group">
                <div class="relative overflow-hidden">
                    <img alt="Eco Friendly Cleaning" class="w-full h-48 sm:h-56 md:h-48 lg:h-52 object-cover transition-transform duration-300 group-hover:scale-110" src="https://storage.googleapis.com/a1aa/image/kQAAWFHdgjJDAZffTWFkHpjRYoAIzGmbY8NCkrOMjrhQ1WjTA.jpg"/>
                </div>
                <div class="p-4 sm:p-6">
                    <h2 class="text-lg sm:text-xl lg:text-2xl font-bold mb-2 text-blue-900" data-en="Eco Friendly Cleaning" data-id="Pembersihan Ramah Lingkungan">Eco Friendly Cleaning</h2>
                    <p class="text-gray-700 text-sm sm:text-base mb-4" data-en="Our sustainable cleaning products for a cleaner, greener future." data-id="Produk pembersih berkelanjutan kami untuk masa depan yang lebih bersih dan lebih hijau.">Our sustainable cleaning products for a cleaner, greener future.</p>
                    <a class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition text-sm sm:text-base inline-block" href="Order.php" aria-label="Order Eco Friendly Cleaning" data-en="Order Now" data-id="Pesan Sekarang">
                        Order Now
                    </a>
                </div>
                </div>
            </div>
        </div>


            <!-- Order History Section -->
            <div class="mt-10 flex justify-center">
                <a href="history.php?user_id=<?php echo $_SESSION['user_id']; ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-md text-sm sm:text-base transition duration-300 transform hover:scale-105" data-en="View Order History" data-id="Lihat Riwayat Pesanan">
                    View Order History
                </a>
            </div>
        </div>
    </main>

    <!-- Other sections go here -->

    <!-- Footer -->


    <script>
        // Toggle the mobile menu visibility when the hamburger icon is clicked
        function toggleMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden'); // Toggle the 'hidden' class to show/hide the menu
        }
    </script>
</body>
</html>
