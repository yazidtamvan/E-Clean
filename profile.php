<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Clean - User Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <script>
        function changeLanguage(language) {
            const elements = document.querySelectorAll('[data-en]');
            elements.forEach(element => {
                if (language === 'en') {
                    element.textContent = element.getAttribute('data-en');
                } else if (language === 'id') {
                    element.textContent = element.getAttribute('data-id');
                }
            });
            document.getElementById('languageDropdown').querySelector('span').textContent = language.toUpperCase();
        }

        function toggleMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }
    </script>
</head>
<body class="bg-gray-100 font-roboto">
    <!-- Navbar (Copied from original page) -->
   <nav class="bg-white shadow-lg border-b border-gray-100">
        <div class="container mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <a class="flex items-center space-x-3" href="#">
                    <svg class="w-8 h-8 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20.437 17.328a9.97 9.97 0 0 0 1.563-5.328c0-5.523-4.477-10-10-10S2 6.477 2 12s4.477 10 10 10a9.967 9.967 0 0 0 6.192-2.166c.577.737 1.444 1.166 2.363 1.166a3 3 0 0 0 3-3c0-1.038-.67-1.924-1.616-2.272zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8z"/>
                    </svg>
                    <span class="text-xl font-bold text-gray-800">E-Clean</span>
                </a>
                <a href="dashboard_user.php" class="flex items-center space-x-2 text-gray-600 hover:text-blue-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Back to Dashboard</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-8 sm:py-12">
        <div class="container mx-auto max-w-screen-lg px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-lg p-6 sm:p-8">
                <h1 class="text-2xl sm:text-3xl font-bold mb-6 text-blue-900" data-en="User Profile" data-id="Profil Pengguna">User Profile</h1>
                
                <form id="profileForm" class="space-y-6">
                    <!-- Personal Information Section -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 font-bold mb-2" for="fullName" data-en="Full Name" data-id="Nama Lengkap">Full Name</label>
                            <input type="text" id="fullName" name="fullName" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your full name">
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 font-bold mb-2" for="email" data-en="Email Address" data-id="Alamat Email">Email Address</label>
                            <input type="email" id="email" name="email" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your email">
                        </div>
                    </div>

                    <!-- Date of Birth and Phone -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 font-bold mb-2" for="dob" data-en="Date of Birth" data-id="Tanggal Lahir">Date of Birth</label>
                            <input type="date" id="dob" name="dob" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 font-bold mb-2" for="phone" data-en="Phone Number" data-id="Nomor Telepon">Phone Number</label>
                            <input type="tel" id="phone" name="phone" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your phone number">
                        </div>
                    </div>

                    <!-- Address Section -->
                    <div>
                        <label class="block text-gray-700 font-bold mb-2" for="address" data-en="Full Address" data-id="Alamat Lengkap">Full Address</label>
                        <textarea id="address" name="address" rows="3" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your full address"></textarea>
                    </div>

                

                    <!-- Save Changes Button -->
                    <div class="mt-6">
                        <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition duration-300 transform hover:scale-105" data-en="Save Changes" data-id="Simpan Perubahan">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- Footer -->
  
    <script>
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                fullName: document.getElementById('fullName').value,
                email: document.getElementById('email').value,
                dob: document.getElementById('dob').value,
                phone: document.getElementById('phone').value,
                address: document.getElementById('address').value,
                currentPassword: document.getElementById('currentPassword').value,
                newPassword: document.getElementById('newPassword').value
            };

            console.log('Profile update submitted:', formData);
            alert('Profile updated successfully!');
        });
    </script>
</body>
</html>