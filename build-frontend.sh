#!/bin/bash

# Laravel + Nuxt Frontend Build Script
# Bu script frontend'i build alıp Laravel'in public klasörüne koyar

echo "🚀 Laravel + Nuxt Frontend Build Started..."

# Check if frontend directory exists
if [ ! -d "frontend" ]; then
    echo "❌ Frontend directory not found!"
    exit 1
fi

cd frontend

echo "📦 Installing frontend dependencies..."
npm install

echo "🔨 Building frontend for production..."
NODE_ENV=production npm run generate

# Check if build was successful
if [ ! -d ".output/public" ]; then
    echo "❌ Frontend build failed!"
    exit 1
fi

echo "📁 Copying build files to Laravel public..."
# Create frontend directory in Laravel public
mkdir -p ../public/frontend

# Copy all files from .output/public to Laravel public/frontend
cp -r .output/public/* ../public/frontend/

echo "🧹 Cleaning up..."
cd ..

# Optional: Clear Laravel cache
echo "🔄 Clearing Laravel cache..."
php artisan config:clear
php artisan route:clear
php artisan cache:clear

echo "✅ Frontend build completed successfully!"
echo "📍 Files are ready in: public/frontend/"
echo "🌐 Test: http://your-domain.com"

# Optional: Run Laravel optimization
echo "⚡ Optimizing Laravel..."
php artisan config:cache
php artisan route:cache

echo "🎉 Build process finished!"