<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Compressor - Quickies</title>
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
                    <h1 class="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-cyan-400 to-blue-400">
                        Image Compressor
                    </h1>
                    <p class="text-purple-300 mt-1">Compress JPG and PNG images while maintaining quality</p>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <div class="max-w-5xl mx-auto">
            <div class="bg-white/10 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-8">
                
                <!-- Upload Section -->
                <div id="uploadSection">
                    <div class="border-2 border-dashed border-blue-400/50 rounded-2xl p-12 text-center hover:border-blue-400 transition-all cursor-pointer bg-white/5 hover:bg-white/10"
                         ondrop="handleDrop(event)" 
                         ondragover="handleDragOver(event)"
                         ondragleave="handleDragLeave(event)"
                         onclick="document.getElementById('fileInput').click()">
                        <input type="file" id="fileInput" accept="image/jpeg,image/jpg,image/png" multiple class="hidden" onchange="handleFileSelect(event)">
                        
                        <svg class="w-20 h-20 mx-auto mb-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        
                        <h3 class="text-2xl font-bold text-white mb-2">Drop Images Here</h3>
                        <p class="text-purple-300 mb-4">or click to browse</p>
                        <p class="text-purple-400/60 text-sm">Supports JPG and PNG files (multiple files allowed)</p>
                    </div>
                </div>

                <!-- Processing Section -->
                <div id="processingSection" class="hidden mt-8">
                    
                    <!-- Compression Settings -->
                    <div class="bg-white/5 rounded-2xl p-6 border border-white/10 mb-8">
                        <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                            <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                            </svg>
                            Compression Settings
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-purple-200 text-sm font-semibold mb-3">
                                    Quality (Higher = Better Quality, Larger Size)
                                </label>
                                <input type="range" id="quality" min="10" max="100" value="80" 
                                       class="w-full accent-blue-500"
                                       onchange="updateQuality()">
                                <div class="text-purple-300 text-sm mt-2 text-center">
                                    <span id="qualityValue">80</span>%
                                </div>
                            </div>

                            <div>
                                <label class="block text-purple-200 text-sm font-semibold mb-3">
                                    Max Width (0 = No Resize)
                                </label>
                                <input type="number" id="maxWidth" value="0" min="0" step="100"
                                       class="w-full px-4 py-2 bg-white/5 border border-blue-500/30 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       onchange="updateSettings()">
                                <div class="text-purple-300/60 text-xs mt-2 text-center">
                                    Aspect ratio will be preserved
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <button onclick="compressAll()" 
                                    class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                                Compress All Images
                            </button>
                            <button onclick="downloadAll()" 
                                    id="downloadAllBtn"
                                    class="flex-1 px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                    disabled>
                                Download All
                            </button>
                        </div>
                    </div>

                    <!-- Images Grid -->
                    <div id="imagesGrid" class="grid grid-cols-1 gap-6">
                        <!-- Image cards will be generated here -->
                    </div>

                    <!-- Reset Button -->
                    <div class="mt-6 text-center">
                        <button onclick="resetCompressor()" 
                                class="px-6 py-2 text-purple-300 hover:text-white border border-purple-400/50 hover:border-purple-400 rounded-xl transition-all">
                            Upload Different Images
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.2/dist/browser-image-compression.js"></script>
    <script>
        let uploadedFiles = [];
        let compressedImages = [];

        function handleDragOver(e) {
            e.preventDefault();
            e.currentTarget.classList.add('border-blue-400', 'bg-white/20');
        }

        function handleDragLeave(e) {
            e.currentTarget.classList.remove('border-blue-400', 'bg-white/20');
        }

        function handleDrop(e) {
            e.preventDefault();
            e.currentTarget.classList.remove('border-blue-400', 'bg-white/20');
            
            const files = Array.from(e.dataTransfer.files);
            processFiles(files);
        }

        function handleFileSelect(e) {
            const files = Array.from(e.target.files);
            processFiles(files);
        }

        function processFiles(files) {
            const validFiles = files.filter(file => 
                file.type === 'image/jpeg' || file.type === 'image/jpg' || file.type === 'image/png'
            );

            if (validFiles.length === 0) {
                showNotification('Please select valid JPG or PNG images', 'error');
                return;
            }

            uploadedFiles = validFiles;
            compressedImages = new Array(validFiles.length).fill(null);
            
            document.getElementById('uploadSection').classList.add('hidden');
            document.getElementById('processingSection').classList.remove('hidden');
            
            displayImages();
        }

        function displayImages() {
            const grid = document.getElementById('imagesGrid');
            grid.innerHTML = '';

            uploadedFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const card = createImageCard(file, e.target.result, index);
                    grid.appendChild(card);
                };
                reader.readAsDataURL(file);
            });
        }

        function createImageCard(file, dataUrl, index) {
            const div = document.createElement('div');
            div.className = 'bg-white/5 rounded-2xl p-6 border border-white/10';
            div.id = `image-${index}`;
            
            div.innerHTML = `
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Original -->
                    <div>
                        <h4 class="text-lg font-bold text-white mb-3 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Original
                        </h4>
                        <div class="bg-white/10 rounded-xl p-4 mb-3">
                            <img src="${dataUrl}" class="w-full h-48 object-contain" />
                        </div>
                        <div class="text-purple-200 text-sm space-y-1">
                            <div><strong>Name:</strong> ${file.name}</div>
                            <div><strong>Size:</strong> ${formatFileSize(file.size)}</div>
                            <div><strong>Type:</strong> ${file.type.split('/')[1].toUpperCase()}</div>
                        </div>
                    </div>

                    <!-- Compressed -->
                    <div>
                        <h4 class="text-lg font-bold text-white mb-3 flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Compressed
                        </h4>
                        <div id="compressed-preview-${index}" class="bg-white/10 rounded-xl p-4 mb-3 flex items-center justify-center h-48">
                            <span class="text-purple-300/50">Not compressed yet</span>
                        </div>
                        <div id="compressed-info-${index}" class="text-purple-200 text-sm space-y-1">
                            <div class="text-purple-300/50">Compress to see results</div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex gap-3">
                    <button onclick="compressImage(${index})" 
                            class="flex-1 px-4 py-2 bg-blue-500/20 hover:bg-blue-500/40 text-blue-300 rounded-lg transition-all text-sm font-semibold">
                        Compress This Image
                    </button>
                    <button onclick="downloadImage(${index})" 
                            id="download-${index}"
                            class="flex-1 px-4 py-2 bg-green-500/20 hover:bg-green-500/40 text-green-300 rounded-lg transition-all text-sm font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
                            disabled>
                        Download
                    </button>
                </div>
            `;

            return div;
        }

        async function compressImage(index) {
            const file = uploadedFiles[index];
            const quality = parseInt(document.getElementById('quality').value) / 100;
            const maxWidth = parseInt(document.getElementById('maxWidth').value);

            showNotification(`Compressing ${file.name}...`, 'info');

            const options = {
                maxSizeMB: 10,
                maxWidthOrHeight: maxWidth > 0 ? maxWidth : undefined,
                useWebWorker: true,
                quality: quality
            };

            try {
                const compressedFile = await imageCompression(file, options);
                compressedImages[index] = compressedFile;

                // Display compressed image
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById(`compressed-preview-${index}`);
                    preview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-contain" />`;

                    const info = document.getElementById(`compressed-info-${index}`);
                    const reduction = ((1 - compressedFile.size / file.size) * 100).toFixed(1);
                    info.innerHTML = `
                        <div><strong>Size:</strong> ${formatFileSize(compressedFile.size)}</div>
                        <div class="text-green-400"><strong>Saved:</strong> ${reduction}% (${formatFileSize(file.size - compressedFile.size)})</div>
                    `;

                    document.getElementById(`download-${index}`).disabled = false;
                    updateDownloadAllButton();
                };
                reader.readAsDataURL(compressedFile);

                showNotification(`${file.name} compressed successfully!`, 'success');
            } catch (error) {
                showNotification(`Error compressing ${file.name}`, 'error');
                console.error(error);
            }
        }

        async function compressAll() {
            for (let i = 0; i < uploadedFiles.length; i++) {
                if (!compressedImages[i]) {
                    await compressImage(i);
                }
            }
        }

        function downloadImage(index) {
            if (!compressedImages[index]) {
                showNotification('Please compress the image first', 'error');
                return;
            }

            const url = URL.createObjectURL(compressedImages[index]);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'compressed-' + uploadedFiles[index].name;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }

        function downloadAll() {
            compressedImages.forEach((img, index) => {
                if (img) {
                    setTimeout(() => downloadImage(index), index * 100);
                }
            });
            showNotification('Downloading all compressed images...', 'success');
        }

        function updateDownloadAllButton() {
            const allCompressed = compressedImages.every(img => img !== null);
            document.getElementById('downloadAllBtn').disabled = !allCompressed;
        }

        function updateQuality() {
            document.getElementById('qualityValue').textContent = document.getElementById('quality').value;
        }

        function updateSettings() {
            // Settings updated
        }

        function resetCompressor() {
            document.getElementById('uploadSection').classList.remove('hidden');
            document.getElementById('processingSection').classList.add('hidden');
            document.getElementById('fileInput').value = '';
            document.getElementById('imagesGrid').innerHTML = '';
            uploadedFiles = [];
            compressedImages = [];
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
                info: 'bg-blue-500'
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
