<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Inventory Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        .login-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="login-gradient min-h-screen">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="w-full max-w-md">
            <!-- Back to Landing -->
            <a href="{{ route('landing') }}" class="text-white/80 hover:text-white transition inline-flex items-center mb-4">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Beranda
            </a>
            
            <div class="bg-white rounded-2xl shadow-2xl p-8">
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-purple-100 rounded-full mb-4">
                        <i class="fas fa-boxes text-3xl text-purple-600"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Selamat Datang</h2>
                    <p class="text-gray-600 text-sm mt-1">Silakan login untuk melanjutkan</p>
                </div>
                
                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {{ $errors->first() }}
                    </div>
                @endif
                
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-medium mb-2">Email</label>
                        <div class="relative">
                            <i class="fas fa-envelope absolute left-3 top-3 text-gray-400"></i>
                            <input type="email" name="email" value="admin@yayasan.com" 
                                   class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition"
                                   placeholder="admin@yayasan.com" required>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-medium mb-2">Password</label>
                        <div class="relative">
                            <i class="fas fa-lock absolute left-3 top-3 text-gray-400"></i>
                            <input type="password" name="password" value="password"
                                   class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition"
                                   placeholder="********" required>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember" class="mr-2 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                            <span class="text-sm text-gray-600">Ingat saya</span>
                        </label>
                    </div>
                    
                    <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-blue-500 text-white py-3 rounded-lg font-semibold hover:shadow-lg transition transform hover:scale-[1.02]">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </button>
                </form>
                
                <div class="mt-6 text-center text-sm text-gray-500 border-t pt-6">
                    <p>Demo Account:</p>
                    <div class="grid grid-cols-3 gap-2 mt-2 text-xs">
                        <div class="bg-gray-50 rounded p-2">
                            <span class="font-medium text-purple-600">Admin</span>
                            <p class="text-gray-500">admin@yayasan.com</p>
                        </div>
                        <div class="bg-gray-50 rounded p-2">
                            <span class="font-medium text-blue-600">Manager</span>
                            <p class="text-gray-500">manager@smp.com</p>
                        </div>
                        <div class="bg-gray-50 rounded p-2">
                            <span class="font-medium text-green-600">User</span>
                            <p class="text-gray-500">user@yayasan.com</p>
                        </div>
                    </div>
                    <p class="text-gray-400 mt-2">Password: <span class="font-mono">password</span></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>