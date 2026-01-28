<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Cropper - Quickies</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.1/dist/cropper.min.css">
</head>
<body class="bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <header class="mb-12">
            <div class="flex items-center gap-4 mb-6">
                <a href="/" class="text-purple-300 hover:text-purple-200 transition-colors">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-orange-400 via-amber-400 to-orange-400">
                        Image Cropper
                    </h1>
                    <p class="text-purple-300 mt-1">Crop PNG images while preserving transparency</p>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <div class="max-w-5xl mx-auto">
            <div class="bg-white/10 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-8">
                
                <!-- Upload Section -->
                <div id="uploadSection">
                    <div class="border-2 border-dashed border-orange-400/50 rounded-2xl p-12 text-center hover:border-orange-400 transition-all cursor-pointer bg-white/5 hover:bg-white/10"
                         ondrop="handleDrop(event)" 
                         ondragover="handleDragOver(event)"
                         ondragleave="handleDragLeave(event)"
                         onclick="document.getElementById('fileInput').click()">
                        <input type="file" id="fileInput" accept="image/png" class="hidden" onchange="handleFileSelect(event)">
                        
                        <svg class="w-20 h-20 mx-auto mb-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        
                        <h3 class="text-2xl font-bold text-white mb-2">Drop PNG Image Here</h3>
                        <p class="text-purple-300 mb-4">or click to browse</p>
                        <p class="text-purple-400/60 text-sm">Supports PNG files only (preserves transparency)</p>
                    </div>
                </div>

                <!-- Cropping Section -->
                <div id="croppingSection" class="hidden">
                    
                    <!-- Crop Options -->
                    <div class="bg-white/5 rounded-2xl p-6 border border-white/10 mb-6">
                        <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                            <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                            </svg>
                            Crop Settings
                        </h3>
                        
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                            <button onclick="setAspectRatio(NaN)" class="aspect-btn px-4 py-3 bg-orange-500/20 hover:bg-orange-500/40 text-orange-300 rounded-xl transition-all text-sm font-semibold border border-orange-400/30 active">
                                Free
                            </button>
                            <button onclick="setAspectRatio(1)" class="aspect-btn px-4 py-3 bg-white/5 hover:bg-orange-500/40 text-purple-300 hover:text-orange-300 rounded-xl transition-all text-sm font-semibold border border-white/10 hover:border-orange-400/30">
                                1:1
                            </button>
                            <button onclick="setAspectRatio(16/9)" class="aspect-btn px-4 py-3 bg-white/5 hover:bg-orange-500/40 text-purple-300 hover:text-orange-300 rounded-xl transition-all text-sm font-semibold border border-white/10 hover:border-orange-400/30">
                                16:9
                            </button>
                            <button onclick="setAspectRatio(4/3)" class="aspect-btn px-4 py-3 bg-white/5 hover:bg-orange-500/40 text-purple-300 hover:text-orange-300 rounded-xl transition-all text-sm font-semibold border border-white/10 hover:border-orange-400/30">
                                4:3
                            </button>
                            <button onclick="setAspectRatio(3/2)" class="aspect-btn px-4 py-3 bg-white/5 hover:bg-orange-500/40 text-purple-300 hover:text-orange-300 rounded-xl transition-all text-sm font-semibold border border-white/10 hover:border-orange-400/30">
                                3:2
                            </button>
                            <button onclick="setAspectRatio(2/3)" class="aspect-btn px-4 py-3 bg-white/5 hover:bg-orange-500/40 text-purple-300 hover:text-orange-300 rounded-xl transition-all text-sm font-semibold border border-white/10 hover:border-orange-400/30">
                                2:3
                            </button>
                            <button onclick="setAspectRatio(9/16)" class="aspect-btn px-4 py-3 bg-white/5 hover:bg-orange-500/40 text-purple-300 hover:text-orange-300 rounded-xl transition-all text-sm font-semibold border border-white/10 hover:border-orange-400/30">
                                9:16
                            </button>
                            <button onclick="setAspectRatio(21/9)" class="aspect-btn px-4 py-3 bg-white/5 hover:bg-orange-500/40 text-purple-300 hover:text-orange-300 rounded-xl transition-all text-sm font-semibold border border-white/10 hover:border-orange-400/30">
                                21:9
                            </button>
                        </div>

                        <!-- Rotation & Flip Controls -->
                        <div class="flex flex-wrap gap-3 mb-6">
                            <button onclick="rotate(-90)" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-purple-300 rounded-xl transition-all text-sm font-semibold border border-white/10 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                </svg>
                                Rotate Left
                            </button>
                            <button onclick="rotate(90)" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-purple-300 rounded-xl transition-all text-sm font-semibold border border-white/10 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10h-10a8 8 0 00-8 8v2M21 10l-6 6m6-6l-6-6"></path>
                                </svg>
                                Rotate Right
                            </button>
                            <button onclick="flipHorizontal()" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-purple-300 rounded-xl transition-all text-sm font-semibold border border-white/10 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12M8 12h12m-12 5h12M4 7v10"></path>
                                </svg>
                                Flip H
                            </button>
                            <button onclick="flipVertical()" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-purple-300 rounded-xl transition-all text-sm font-semibold border border-white/10 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 12h10M7 8h10m-5-4v16"></path>
                                </svg>
                                Flip V
                            </button>
                            <button onclick="resetCrop()" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-purple-300 rounded-xl transition-all text-sm font-semibold border border-white/10 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Reset
                            </button>
                        </div>

                        <!-- Crop Dimensions Display -->
                        <div class="bg-white/5 rounded-xl p-4 mb-6">
                            <div class="flex items-center justify-center gap-8 text-sm">
                                <div class="text-purple-300">
                                    <span class="text-purple-400/60">Width:</span> 
                                    <span id="cropWidth" class="font-mono font-bold text-white">0</span>px
                                </div>
                                <div class="text-purple-300">
                                    <span class="text-purple-400/60">Height:</span> 
                                    <span id="cropHeight" class="font-mono font-bold text-white">0</span>px
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <button onclick="cropAndDownload()" 
                                    class="flex-1 px-6 py-3 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                                Crop & Download PNG
                            </button>
                        </div>
                    </div>

                    <!-- Cropper Container -->
                    <div class="bg-white/5 rounded-2xl p-6 border border-white/10">
                        <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Crop Area
                            <span class="text-purple-400/60 text-sm font-normal ml-2">(Drag to adjust selection)</span>
                        </h3>
                        <div class="cropper-container bg-black/20 rounded-xl overflow-hidden" style="max-height: 500px;">
                            <img id="cropImage" class="max-w-full" style="display: block; max-height: 500px;">
                        </div>
                    </div>

                    <!-- Reset Button -->
                    <div class="mt-6 text-center">
                        <button onclick="resetCropper()" 
                                class="px-6 py-2 text-purple-300 hover:text-white border border-purple-400/50 hover:border-purple-400 rounded-xl transition-all">
                            Upload Different Image
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.1/dist/cropper.min.js"></script>
    <script>
        let cropper = null;
        let originalFileName = '';
        let scaleX = 1;
        let scaleY = 1;

        function handleDragOver(e) {
            e.preventDefault();
            e.currentTarget.classList.add('border-orange-400', 'bg-white/20');
        }

        function handleDragLeave(e) {
            e.currentTarget.classList.remove('border-orange-400', 'bg-white/20');
        }

        function handleDrop(e) {
            e.preventDefault();
            e.currentTarget.classList.remove('border-orange-400', 'bg-white/20');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                processFile(files[0]);
            }
        }

        function handleFileSelect(e) {
            const files = e.target.files;
            if (files.length > 0) {
                processFile(files[0]);
            }
        }

        function processFile(file) {
            if (file.type !== 'image/png') {
                showNotification('Please select a PNG image', 'error');
                return;
            }

            originalFileName = file.name.replace('.png', '');
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const image = document.getElementById('cropImage');
                image.src = e.target.result;
                
                document.getElementById('uploadSection').classList.add('hidden');
                document.getElementById('croppingSection').classList.remove('hidden');
                
                // Initialize cropper after image loads
                image.onload = function() {
                    if (cropper) {
                        cropper.destroy();
                    }
                    
                    cropper = new Cropper(image, {
                        viewMode: 1,
                        dragMode: 'move',
                        autoCropArea: 0.8,
                        restore: false,
                        guides: true,
                        center: true,
                        highlight: true,
                        cropBoxMovable: true,
                        cropBoxResizable: true,
                        toggleDragModeOnDblclick: false,
                        background: true,
                        responsive: true,
                        crop: function(event) {
                            document.getElementById('cropWidth').textContent = Math.round(event.detail.width);
                            document.getElementById('cropHeight').textContent = Math.round(event.detail.height);
                        }
                    });
                };
            };
            
            reader.readAsDataURL(file);
        }

        function setAspectRatio(ratio) {
            if (cropper) {
                cropper.setAspectRatio(ratio);
            }
            
            // Update button styles
            document.querySelectorAll('.aspect-btn').forEach(btn => {
                btn.classList.remove('bg-orange-500/20', 'text-orange-300', 'active');
                btn.classList.add('bg-white/5', 'text-purple-300');
            });
            event.target.classList.remove('bg-white/5', 'text-purple-300');
            event.target.classList.add('bg-orange-500/20', 'text-orange-300', 'active');
        }

        function rotate(degree) {
            if (cropper) {
                cropper.rotate(degree);
            }
        }

        function flipHorizontal() {
            if (cropper) {
                scaleX = -scaleX;
                cropper.scaleX(scaleX);
            }
        }

        function flipVertical() {
            if (cropper) {
                scaleY = -scaleY;
                cropper.scaleY(scaleY);
            }
        }

        function resetCrop() {
            if (cropper) {
                cropper.reset();
                scaleX = 1;
                scaleY = 1;
            }
        }

        function cropAndDownload() {
            if (!cropper) {
                showNotification('Please load an image first', 'error');
                return;
            }

            showNotification('Processing...', 'info');

            // Get cropped canvas with PNG format (preserves transparency)
            const canvas = cropper.getCroppedCanvas({
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high'
            });

            // Convert to PNG blob
            canvas.toBlob(function(blob) {
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = originalFileName + '-cropped.png';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
                
                showNotification('PNG downloaded successfully!', 'success');
            }, 'image/png');
        }

        function resetCropper() {
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
            
            document.getElementById('uploadSection').classList.remove('hidden');
            document.getElementById('croppingSection').classList.add('hidden');
            document.getElementById('fileInput').value = '';
            document.getElementById('cropImage').src = '';
            scaleX = 1;
            scaleY = 1;
        }

        function showNotification(message, type = 'info') {
            const colors = {
                success: 'bg-green-500',
                error: 'bg-red-500',
                info: 'bg-orange-500'
            };
            
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in`;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.classList.add('animate-fade-out');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }
    </script>

    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fade-out {
            from {
                opacity: 1;
                transform: translateY(0);
            }
            to {
                opacity: 0;
                transform: translateY(-10px);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }

        .animate-fade-out {
            animation: fade-out 0.3s ease-out;
        }

        /* Cropper.js custom styling */
        .cropper-view-box,
        .cropper-face {
            border-radius: 0;
        }

        .cropper-line {
            background-color: rgba(249, 115, 22, 0.8);
        }

        .cropper-point {
            background-color: rgb(249, 115, 22);
            width: 10px;
            height: 10px;
        }

        .cropper-view-box {
            outline: 2px solid rgba(249, 115, 22, 0.9);
        }

        .cropper-dashed {
            border-color: rgba(255, 255, 255, 0.5);
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
