<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LPK Jepang - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-up': 'slideUp 0.5s ease-out'
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': {
                                transform: 'translateY(0px) rotate(0deg)'
                            },
                            '50%': {
                                transform: 'translateY(-20px) rotate(180deg)'
                            }
                        },
                        fadeIn: {
                            '0%': {
                                opacity: '0'
                            },
                            '100%': {
                                opacity: '1'
                            }
                        },
                        slideUp: {
                            '0%': {
                                transform: 'translateY(20px)',
                                opacity: '0'
                            },
                            '100%': {
                                transform: 'translateY(0)',
                                opacity: '1'
                            }
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="min-h-screen bg-gradient-to-br from-blue-500 via-purple-600 to-blue-800 relative overflow-hidden">
    <!-- Floating background elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-1/4 left-1/4 w-20 h-20 bg-white/10 rounded-full animate-float"></div>
        <div class="absolute top-1/3 right-1/4 w-16 h-16 bg-white/10 rounded-full animate-float"
            style="animation-delay: 2s;"></div>
        <div class="absolute bottom-1/4 left-1/6 w-24 h-24 bg-white/10 rounded-full animate-float"
            style="animation-delay: 4s;"></div>
        <div class="absolute bottom-1/3 right-1/6 w-18 h-18 bg-white/10 rounded-full animate-float"
            style="animation-delay: 1s;"></div>
    </div>

    <!-- Main content -->
    <div class="min-h-screen flex items-center justify-center p-4 relative z-10">
        <div class="w-full max-w-md">
            <!-- Auth Card -->
            <div class="bg-white/95 backdrop-blur-sm rounded-2xl shadow-2xl border border-white/20 animate-slide-up">
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white text-center py-8 rounded-t-2xl">
                    <div class="mb-4">
                        <i class="fas fa-torii-gate text-3xl"></i>
                    </div>
                    <h1 class="text-2xl font-bold">LPK Jepang</h1>
                    <p class="text-blue-100 mt-2">@yield('subtitle', 'Portal Pendaftaran')</p>
                </div>

                <!-- Body -->
                <div class="p-8">
                    <!-- Error Messages -->
                    @if ($errors->any())
                        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                                <span class="text-red-800 font-medium">Terjadi kesalahan:</span>
                            </div>
                            <ul class="text-red-700 text-sm space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>• {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-6">
                <p class="text-white/80 text-sm">
                    © {{ date('Y') }} LPK Jepang. Semua hak dilindungi.
                </p>
            </div>
        </div>
    </div>

    <script>
        // Add some interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            // Form validation feedback
            const inputs = document.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('ring-2', 'ring-blue-500');
                });

                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('ring-2', 'ring-blue-500');

                    // Add validation styling
                    if (this.value && this.checkValidity()) {
                        this.classList.add('border-green-300');
                        this.classList.remove('border-red-300');
                    } else if (this.value && !this.checkValidity()) {
                        this.classList.add('border-red-300');
                        this.classList.remove('border-green-300');
                    }
                });
            });

            // Smooth form submission
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.innerHTML =
                            '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
                        submitBtn.disabled = true;
                    }
                });
            });
        });
    </script>
</body>

</html>
