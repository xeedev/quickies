<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quickies Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <header class="mb-16 text-center">
            <h1 class="text-7xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 via-pink-400 to-purple-400 mb-3 animate-gradient">
                Quickies
            </h1>
            <p class="text-purple-300 text-xl">Your personal utility dashboard</p>
        </header>

        <!-- Quickies Grid -->
        <div class="max-w-6xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                <!-- Color Palette Card -->
                <a href="/color-palette" class="group block">
                    <div class="relative bg-gradient-to-br from-white/10 to-white/5 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-8 hover:border-purple-400/50 transition-all duration-300 hover:scale-105 hover:shadow-purple-500/25 h-full">
                        <!-- Icon -->
                        <div class="mb-6 flex justify-center">
                            <div class="w-20 h-20 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300 group-hover:rotate-3">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                                </svg>
                            </div>
                        </div>
                        
                        <!-- Title -->
                        <h3 class="text-2xl font-bold text-white mb-3 text-center group-hover:text-purple-300 transition-colors">
                            Color Palette
                        </h3>
                        
                        <!-- Description -->
                        <p class="text-purple-200/80 text-center text-sm leading-relaxed mb-6">
                            View and compare color codes to see how they look together
                        </p>
                        
                        <!-- Color Preview -->
                        <div class="flex gap-2 justify-center">
                            <div class="w-8 h-8 rounded-lg shadow-md" style="background: #FF6B9D;"></div>
                            <div class="w-8 h-8 rounded-lg shadow-md" style="background: #C44569;"></div>
                            <div class="w-8 h-8 rounded-lg shadow-md" style="background: #FFA07A;"></div>
                            <div class="w-8 h-8 rounded-lg shadow-md" style="background: #4A90E2;"></div>
                            <div class="w-8 h-8 rounded-lg shadow-md" style="background: #50C878;"></div>
                        </div>
                        
                        <!-- Hover Arrow -->
                        <div class="absolute bottom-6 right-6 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-x-2 group-hover:translate-x-0">
                            <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </div>
                    </div>
                </a>

                <!-- PNG to SVG Card -->
                <a href="/png-to-svg" class="group block">
                    <div class="relative bg-gradient-to-br from-white/10 to-white/5 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-8 hover:border-green-400/50 transition-all duration-300 hover:scale-105 hover:shadow-green-500/25 h-full">
                        <!-- Icon -->
                        <div class="mb-6 flex justify-center">
                            <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300 group-hover:rotate-3">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                        
                        <!-- Title -->
                        <h3 class="text-2xl font-bold text-white mb-3 text-center group-hover:text-green-300 transition-colors">
                            PNG to SVG
                        </h3>
                        
                        <!-- Description -->
                        <p class="text-purple-200/80 text-center text-sm leading-relaxed mb-6">
                            Convert PNG images to scalable vector graphics
                        </p>
                        
                        <!-- Format Preview -->
                        <div class="flex gap-3 justify-center items-center">
                            <span class="px-3 py-1 bg-blue-500/20 text-blue-300 rounded-lg text-xs font-semibold border border-blue-400/30">PNG</span>
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                            <span class="px-3 py-1 bg-green-500/20 text-green-300 rounded-lg text-xs font-semibold border border-green-400/30">SVG</span>
                        </div>
                        
                        <!-- Hover Arrow -->
                        <div class="absolute bottom-6 right-6 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-x-2 group-hover:translate-x-0">
                            <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </div>
                    </div>
                </a>

                <!-- Image Compressor Card -->
                <a href="/image-compressor" class="group block">
                    <div class="relative bg-gradient-to-br from-white/10 to-white/5 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-8 hover:border-cyan-400/50 transition-all duration-300 hover:scale-105 hover:shadow-cyan-500/25 h-full">
                        <!-- Icon -->
                        <div class="mb-6 flex justify-center">
                            <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300 group-hover:rotate-3">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                            </div>
                        </div>
                        
                        <!-- Title -->
                        <h3 class="text-2xl font-bold text-white mb-3 text-center group-hover:text-cyan-300 transition-colors">
                            Image Compressor
                        </h3>
                        
                        <!-- Description -->
                        <p class="text-purple-200/80 text-center text-sm leading-relaxed mb-6">
                            Compress JPG and PNG images to reduce file size
                        </p>
                        
                        <!-- Format Preview -->
                        <div class="flex gap-2 justify-center items-center">
                            <span class="px-3 py-1 bg-orange-500/20 text-orange-300 rounded-lg text-xs font-semibold border border-orange-400/30">JPG</span>
                            <span class="px-3 py-1 bg-blue-500/20 text-blue-300 rounded-lg text-xs font-semibold border border-blue-400/30">PNG</span>
                            <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                            </svg>
                        </div>
                        
                        <!-- Hover Arrow -->
                        <div class="absolute bottom-6 right-6 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-x-2 group-hover:translate-x-0">
                            <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </div>
                    </div>
                </a>

                <!-- Image Cropper Card -->
                <a href="/image-cropper" class="group block">
                    <div class="relative bg-gradient-to-br from-white/10 to-white/5 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-8 hover:border-orange-400/50 transition-all duration-300 hover:scale-105 hover:shadow-orange-500/25 h-full">
                        <!-- Icon -->
                        <div class="mb-6 flex justify-center">
                            <div class="w-20 h-20 bg-gradient-to-br from-orange-500 to-amber-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300 group-hover:rotate-3">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h3M3 4v3m18-3h-3m3 0v3M3 20h3m-3 0v-3m18 3h-3m3 0v-3"></path>
                                </svg>
                            </div>
                        </div>
                        
                        <!-- Title -->
                        <h3 class="text-2xl font-bold text-white mb-3 text-center group-hover:text-orange-300 transition-colors">
                            Image Cropper
                        </h3>
                        
                        <!-- Description -->
                        <p class="text-purple-200/80 text-center text-sm leading-relaxed mb-6">
                            Crop PNG images while preserving transparency
                        </p>
                        
                        <!-- Format Preview -->
                        <div class="flex gap-2 justify-center items-center">
                            <span class="px-3 py-1 bg-blue-500/20 text-blue-300 rounded-lg text-xs font-semibold border border-blue-400/30">PNG</span>
                            <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                            <span class="px-3 py-1 bg-orange-500/20 text-orange-300 rounded-lg text-xs font-semibold border border-orange-400/30">PNG</span>
                        </div>
                        
                        <!-- Hover Arrow -->
                        <div class="absolute bottom-6 right-6 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-x-2 group-hover:translate-x-0">
                            <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </div>
                    </div>
                </a>

                <!-- Placeholder for more quickies -->
                <div class="group block cursor-not-allowed">
                    <div class="relative bg-white/5 backdrop-blur-lg rounded-3xl shadow-lg border border-white/10 border-dashed p-8 h-full flex flex-col items-center justify-center opacity-50">
                        <svg class="w-16 h-16 text-purple-400/50 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <p class="text-purple-300/50 text-center font-semibold">More Quickies</p>
                        <p class="text-purple-300/30 text-center text-sm mt-2">Coming Soon</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <style>
        @keyframes gradient {
            0%, 100% {
                background-size: 200% 200%;
                background-position: left center;
            }
            50% {
                background-size: 200% 200%;
                background-position: right center;
            }
        }

        .animate-gradient {
            animation: gradient 8s ease infinite;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(168, 85, 247, 0.4);
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(168, 85, 247, 0.6);
        }
    </style>
</body>
</html>
