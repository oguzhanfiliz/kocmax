#!/bin/bash

# Laravel + Nuxt Development Workflow
# Bu script hem Laravel hem Nuxt'i development mode'da çalıştırır

echo "🚀 Starting Laravel + Nuxt Development Servers..."

# Function to run commands in background
run_in_background() {
    echo "Starting: $1"
    $1 &
    pid=$!
    echo "PID: $pid"
}

# Start Laravel backend
echo "🔧 Starting Laravel backend (Port 8000)..."
php artisan serve --port=8000 &
LARAVEL_PID=$!

# Wait for Laravel to start
echo "⏳ Waiting for Laravel to start..."
sleep 3

# Check if Laravel is running
if curl -s http://127.0.0.1:8000 > /dev/null; then
    echo "✅ Laravel backend started successfully!"
else
    echo "❌ Laravel backend failed to start!"
    kill $LARAVEL_PID
    exit 1
fi

# Start Nuxt frontend
echo "🎨 Starting Nuxt frontend (Port 3000)..."
cd frontend
npm run dev &
NUXT_PID=$!

# Wait for user to stop
echo ""
echo "🎉 Both servers are running:"
echo "📍 Laravel Backend: http://127.0.0.1:8000"
echo "📍 Nuxt Frontend:   http://localhost:3000"
echo "📍 API Docs:        http://127.0.0.1:8000/docs/api"
echo ""
echo "Press [CTRL+C] to stop all servers..."

# Wait for interrupt
trap 'echo "🛑 Stopping servers..."; kill $LARAVEL_PID $NUXT_PID; exit 0' INT

# Wait for processes to finish
wait $LARAVEL_PID $NUXT_PID