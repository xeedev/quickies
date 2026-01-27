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
        <header class="mb-12 text-center">
            <h1 class="text-6xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 via-pink-400 to-purple-400 mb-2">
                Quickies
            </h1>
            <p class="text-purple-300 text-lg">Your personal utility dashboard</p>
        </header>

        <!-- Color Palette Viewer -->
        <div class="max-w-5xl mx-auto">
            <div class="bg-white/10 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-8">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-3xl font-bold text-white flex items-center gap-3">
                        <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                        </svg>
                        Color Palette Viewer
                    </h2>
                </div>

                <!-- Input Section -->
                <div class="mb-8">
                    <label class="block text-purple-200 text-sm font-semibold mb-3">
                        Enter Color Codes (one per line or comma-separated)
                    </label>
                    <textarea 
                        id="colorInput" 
                        rows="4" 
                        class="w-full px-4 py-3 bg-white/5 border border-purple-500/30 rounded-xl text-white placeholder-purple-300/50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                        placeholder="e.g., #FF6B9D, #C44569, #FFA07A or enter hex, rgb, hsl codes..."
                    >#FF6B9D
#C44569
#FFA07A
#4A90E2
#50C878</textarea>
                    <button 
                        onclick="generatePalette()" 
                        class="mt-4 px-8 py-3 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200"
                    >
                        Generate Palette
                    </button>
                </div>

                <!-- Color Grid Display -->
                <div id="colorGrid" class="grid grid-cols-1 gap-6">
                    <!-- Colors will be generated here -->
                </div>

                <!-- Matrix View -->
                <div id="matrixView" class="mt-8 hidden">
                    <h3 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                        </svg>
                        Color Combination Matrix
                    </h3>
                    <div id="matrixGrid" class="overflow-x-auto">
                        <!-- Matrix will be generated here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize with default colors on page load
        window.addEventListener('DOMContentLoaded', () => {
            generatePalette();
        });

        function generatePalette() {
            const input = document.getElementById('colorInput').value;
            const colorGrid = document.getElementById('colorGrid');
            const matrixView = document.getElementById('matrixView');
            
            // Parse colors (support comma, newline, or space separated)
            const colors = input
                .split(/[\n,\s]+/)
                .map(c => c.trim())
                .filter(c => c.length > 0);
            
            if (colors.length === 0) {
                colorGrid.innerHTML = '<p class="text-purple-300 text-center py-8">Please enter at least one color code</p>';
                matrixView.classList.add('hidden');
                return;
            }

            // Generate individual color cards
            colorGrid.innerHTML = colors.map((color, index) => {
                const textColor = getContrastColor(color);
                return `
                    <div class="group relative bg-white/5 rounded-2xl overflow-hidden border border-white/10 hover:border-purple-400/50 transition-all duration-300 hover:shadow-2xl hover:shadow-purple-500/20">
                        <div class="flex items-stretch">
                            <!-- Color Preview -->
                            <div class="w-32 flex-shrink-0 relative" style="background: ${color};">
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-all"></div>
                            </div>
                            
                            <!-- Color Info -->
                            <div class="flex-grow p-6 flex flex-col justify-center">
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-lg font-mono font-bold text-white">${color.toUpperCase()}</span>
                                    <button 
                                        onclick="copyToClipboard('${color}')" 
                                        class="px-3 py-1 bg-purple-500/20 hover:bg-purple-500/40 text-purple-300 rounded-lg text-sm transition-all"
                                        title="Copy to clipboard"
                                    >
                                        Copy
                                    </button>
                                </div>
                                
                                <!-- Color against all other colors -->
                                <div class="flex flex-wrap gap-3">
                                    ${colors.map((bgColor, bgIndex) => {
                                        if (index === bgIndex) return '';
                                        return `
                                            <div class="flex items-center gap-2 px-4 py-2 rounded-lg border border-white/10" style="background: ${bgColor};">
                                                <span class="font-semibold text-sm" style="color: ${color};">Aa</span>
                                                <span class="text-xs opacity-75" style="color: ${color};">${bgColor}</span>
                                            </div>
                                        `;
                                    }).join('')}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            // Generate matrix view
            if (colors.length > 1) {
                matrixView.classList.remove('hidden');
                generateMatrix(colors);
            } else {
                matrixView.classList.add('hidden');
            }
        }

        function generateMatrix(colors) {
            const matrixGrid = document.getElementById('matrixGrid');
            
            let matrixHTML = '<table class="w-full border-collapse">';
            
            // Header row
            matrixHTML += '<tr><th class="p-3 bg-white/5 border border-white/10"></th>';
            colors.forEach(color => {
                matrixHTML += `<th class="p-3 bg-white/5 border border-white/10">
                    <div class="w-16 h-16 rounded-lg shadow-lg mx-auto" style="background: ${color};" title="${color}"></div>
                </th>`;
            });
            matrixHTML += '</tr>';
            
            // Data rows
            colors.forEach((fgColor, rowIndex) => {
                matrixHTML += '<tr>';
                matrixHTML += `<td class="p-3 bg-white/5 border border-white/10">
                    <div class="w-16 h-16 rounded-lg shadow-lg mx-auto" style="background: ${fgColor};" title="${fgColor}"></div>
                </td>`;
                
                colors.forEach((bgColor, colIndex) => {
                    const isMatch = rowIndex === colIndex;
                    matrixHTML += `<td class="p-3 border border-white/10 ${isMatch ? 'bg-white/5' : 'bg-black/20'}">
                        ${isMatch ? 
                            '<span class="text-purple-300 text-xs">—</span>' : 
                            `<div class="text-center py-2 px-3 rounded-lg" style="background: ${bgColor}; color: ${fgColor};">
                                <div class="font-bold text-lg mb-1">Aa</div>
                                <div class="text-xs opacity-75">Sample</div>
                            </div>`
                        }
                    </td>`;
                });
                matrixHTML += '</tr>';
            });
            
            matrixHTML += '</table>';
            matrixGrid.innerHTML = matrixHTML;
        }

        function getContrastColor(hexColor) {
            // Convert hex to RGB
            const rgb = hexColor.match(/^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i);
            if (!rgb) return '#000000';
            
            const r = parseInt(rgb[1], 16);
            const g = parseInt(rgb[2], 16);
            const b = parseInt(rgb[3], 16);
            
            // Calculate luminance
            const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
            
            return luminance > 0.5 ? '#000000' : '#FFFFFF';
        }

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                // Show a brief notification
                const notification = document.createElement('div');
                notification.className = 'fixed top-4 right-4 bg-purple-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in';
                notification.textContent = `Copied: ${text}`;
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.classList.add('animate-fade-out');
                    setTimeout(() => notification.remove(), 300);
                }, 2000);
            });
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
