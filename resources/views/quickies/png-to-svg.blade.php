<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PNG to SVG Converter - Quickies</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
                    <h1 class="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 via-pink-400 to-purple-400">
                        PNG to SVG Converter
                    </h1>
                    <p class="text-purple-300 mt-1">Convert your PNG images to scalable SVG format</p>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <div class="max-w-5xl mx-auto">
            <div class="bg-white/10 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-8">
                
                <!-- Upload Section -->
                <div id="uploadSection" class="mb-8">
                    <div class="border-2 border-dashed border-purple-400/50 rounded-2xl p-12 text-center hover:border-purple-400 transition-all cursor-pointer bg-white/5 hover:bg-white/10"
                         ondrop="handleDrop(event)" 
                         ondragover="handleDragOver(event)"
                         ondragleave="handleDragLeave(event)"
                         onclick="document.getElementById('fileInput').click()">
                        <input type="file" id="fileInput" accept="image/png" class="hidden" onchange="handleFileSelect(event)">
                        
                        <svg class="w-20 h-20 mx-auto mb-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        
                        <h3 class="text-2xl font-bold text-white mb-2">Drop PNG Image Here</h3>
                        <p class="text-purple-300 mb-4">or click to browse</p>
                        <p class="text-purple-400/60 text-sm">Supports PNG files</p>
                    </div>
                </div>

                <!-- Processing Section -->
                <div id="processingSection" class="hidden">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        
                        <!-- Original Image -->
                        <div class="bg-white/5 rounded-2xl p-6 border border-white/10">
                            <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                                <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Original PNG
                            </h3>
                            <div class="bg-white/10 rounded-xl p-4 flex items-center justify-center min-h-[300px]">
                                <img id="originalImage" class="max-w-full max-h-[400px] object-contain" />
                            </div>
                            <div class="mt-4 text-purple-200 text-sm">
                                <div id="originalSize"></div>
                            </div>
                        </div>

                        <!-- Converted SVG -->
                        <div class="bg-white/5 rounded-2xl p-6 border border-white/10">
                            <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                                <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Converted SVG
                            </h3>
                            <div class="bg-white/10 rounded-xl p-4 flex items-center justify-center min-h-[300px]">
                                <div id="svgPreview"></div>
                            </div>
                            <div class="mt-4 text-purple-200 text-sm">
                                <div id="svgSize"></div>
                            </div>
                        </div>

                    </div>

                    <!-- Conversion Options -->
                    <div class="mt-8 bg-white/5 rounded-2xl p-6 border border-white/10">
                        <h3 class="text-xl font-bold text-white mb-6">Conversion Settings</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-purple-200 text-sm font-semibold mb-3">
                                    Color Limit
                                </label>
                                <input type="range" id="colorLimit" min="2" max="32" value="16" 
                                       class="w-full accent-purple-500"
                                       onchange="updateSettings()">
                                <div class="text-purple-300 text-sm mt-2 text-center">
                                    <span id="colorLimitValue">16</span> colors
                                </div>
                            </div>

                            <div>
                                <label class="block text-purple-200 text-sm font-semibold mb-3">
                                    Detail Level
                                </label>
                                <input type="range" id="threshold" min="0" max="255" value="128" 
                                       class="w-full accent-purple-500"
                                       onchange="updateSettings()">
                                <div class="text-purple-300 text-sm mt-2 text-center">
                                    <span id="thresholdValue">128</span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-purple-200 text-sm font-semibold mb-3">
                                    Blur Radius
                                </label>
                                <input type="range" id="blur" min="0" max="10" value="1" 
                                       class="w-full accent-purple-500"
                                       onchange="updateSettings()">
                                <div class="text-purple-300 text-sm mt-2 text-center">
                                    <span id="blurValue">1</span>px
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-4 mt-8">
                            <button onclick="convertImage()" 
                                    class="flex-1 px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                                Convert to SVG
                            </button>
                            <button onclick="downloadSVG()" 
                                    id="downloadBtn"
                                    class="flex-1 px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                    disabled>
                                Download SVG
                            </button>
                        </div>
                    </div>

                    <!-- Reset Button -->
                    <div class="mt-6 text-center">
                        <button onclick="resetConverter()" 
                                class="px-6 py-2 text-purple-300 hover:text-white border border-purple-400/50 hover:border-purple-400 rounded-xl transition-all">
                            Upload Different Image
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/imagetracerjs@1.2.6/imagetracer_v1.2.6.js"></script>
    <script>
        let originalFile = null;
        let svgString = null;

        function handleDragOver(e) {
            e.preventDefault();
            e.currentTarget.classList.add('border-purple-400', 'bg-white/20');
        }

        function handleDragLeave(e) {
            e.currentTarget.classList.remove('border-purple-400', 'bg-white/20');
        }

        function handleDrop(e) {
            e.preventDefault();
            e.currentTarget.classList.remove('border-purple-400', 'bg-white/20');
            
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
            if (!file.type.match('image/png')) {
                showNotification('Please select a PNG image', 'error');
                return;
            }

            originalFile = file;
            const reader = new FileReader();
            
            reader.onload = function(e) {
                document.getElementById('originalImage').src = e.target.result;
                document.getElementById('originalSize').textContent = `Size: ${formatFileSize(file.size)}`;
                
                document.getElementById('uploadSection').classList.add('hidden');
                document.getElementById('processingSection').classList.remove('hidden');
                
                // Auto-convert on load
                setTimeout(() => convertImage(), 500);
            };
            
            reader.readAsDataURL(file);
        }

        function updateSettings() {
            document.getElementById('colorLimitValue').textContent = document.getElementById('colorLimit').value;
            document.getElementById('thresholdValue').textContent = document.getElementById('threshold').value;
            document.getElementById('blurValue').textContent = document.getElementById('blur').value;
        }

        function convertImage() {
            showNotification('Converting... Please wait', 'info');
            
            const img = document.getElementById('originalImage');
            const options = {
                colorsampling: 1,
                numberofcolors: parseInt(document.getElementById('colorLimit').value),
                mincolorratio: 0,
                colorquantcycles: 3,
                ltres: parseInt(document.getElementById('threshold').value) / 255,
                qtres: 1,
                pathomit: 8,
                blur: parseInt(document.getElementById('blur').value),
                blurradius: parseInt(document.getElementById('blur').value),
                blurdelta: 20,
                scale: 1
            };

            ImageTracer.imageToSVG(
                img.src,
                function(svgstr) {
                    svgString = svgstr;
                    document.getElementById('svgPreview').innerHTML = svgstr;
                    
                    const svgSize = new Blob([svgstr]).size;
                    document.getElementById('svgSize').textContent = `Size: ${formatFileSize(svgSize)}`;
                    
                    document.getElementById('downloadBtn').disabled = false;
                    showNotification('Conversion complete!', 'success');
                },
                options
            );
        }

        function downloadSVG() {
            if (!svgString) {
                showNotification('Please convert the image first', 'error');
                return;
            }

            const blob = new Blob([svgString], { type: 'image/svg+xml' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = originalFile.name.replace('.png', '') + '.svg';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
            
            showNotification('SVG downloaded successfully!', 'success');
        }

        function resetConverter() {
            document.getElementById('uploadSection').classList.remove('hidden');
            document.getElementById('processingSection').classList.add('hidden');
            document.getElementById('fileInput').value = '';
            document.getElementById('svgPreview').innerHTML = '';
            document.getElementById('downloadBtn').disabled = true;
            originalFile = null;
            svgString = null;
        }

        function formatFileSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            else if (bytes < 1048576) return (bytes / 1024).toFixed(2) + ' KB';
            else return (bytes / 1048576).toFixed(2) + ' MB';
        }

        function showNotification(message, type = 'info') {
            const colors = {
                success: 'bg-green-500',
                error: 'bg-red-500',
                info: 'bg-purple-500'
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
