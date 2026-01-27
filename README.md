# ✨ Quickies

A personal utility dashboard built with Laravel, featuring a collection of browser-based tools for quick tasks. No authentication needed—just fast, beautiful utilities.

## 🎯 About

Quickies is a sleek, modern dashboard that houses various utility tools for everyday tasks. Built with Laravel and styled with a stunning purple gradient theme, each tool runs entirely in the browser for instant results.

## 🚀 Available Tools

### 🎨 Color Palette Viewer
View and compare color codes to see how different colors look together:
- Enter multiple color codes (hex, rgb, or hsl)
- See each color displayed against all other colors
- Interactive color combination matrix
- Copy color codes to clipboard
- Perfect for designers and developers choosing color schemes

### 🖼️ PNG to SVG Converter
Convert PNG images to scalable vector graphics:
- Drag & drop PNG files
- Adjustable conversion settings (color limit, detail level, blur)
- Side-by-side preview comparison
- Instant download of SVG files
- Great for creating scalable icons and graphics

### 📦 Image Compressor
Compress JPG and PNG images while maintaining quality:
- Support for multiple image uploads
- Adjustable quality slider (10-100%)
- Optional image resizing with max width
- Real-time compression preview
- Shows file size reduction percentage
- Download individually or batch download all

## 🛠️ Installation

1. Clone the repository
```bash
git clone <your-repo-url>
cd quickies
```

2. Install dependencies
```bash
composer install
npm install
```

3. Set up environment
```bash
cp .env.example .env
php artisan key:generate
```

4. Start the development servers
```bash
npm run dev
php artisan serve
```

5. Open your browser to `http://localhost:8000`

## 🎨 Features

- **Beautiful UI**: Glassmorphic design with gradient backgrounds
- **No Auth Required**: Simple, instant access to all tools
- **Browser-Based**: All processing happens client-side
- **Responsive**: Works on desktop and mobile devices
- **Fast**: No server processing or uploads needed
- **Extensible**: Easy to add new utility tools

## 🔧 Tech Stack

- **Backend**: Laravel 11
- **Frontend**: Tailwind CSS 4, Vite
- **Libraries**: 
  - ImageTracer.js (PNG to SVG conversion)
  - browser-image-compression (Image compression)

## 📝 Adding New Quickies

1. Create a new controller in `app/Http/Controllers/`
2. Create a view in `resources/views/quickies/`
3. Add a route in `routes/web.php`
4. Add a card to the dashboard in `resources/views/dashboard.blade.php`

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
