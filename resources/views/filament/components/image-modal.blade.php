{{-- Resim büyütme modalı için JavaScript ve CSS --}}
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden flex items-center justify-center p-4">
    <div class="relative max-w-4xl max-h-full">
        <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white text-2xl font-bold hover:text-gray-300 z-10 bg-black bg-opacity-50 rounded-full w-10 h-10 flex items-center justify-center">
            ×
        </button>
        <img id="modalImage" src="" alt="Büyük görsel" class="max-w-full max-h-full object-contain rounded-lg">
    </div>
</div>

<script>
function openImageModal(imageUrl) {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    
    modalImage.src = imageUrl;
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden'; // Sayfa kaydırmayı engelle
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto'; // Sayfa kaydırmayı geri aç
}

// ESC tuşu ile kapatma
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeImageModal();
    }
});

// Modal dışına tıklayınca kapatma
document.getElementById('imageModal').addEventListener('click', function(event) {
    if (event.target === this) {
        closeImageModal();
    }
});
</script>

<style>
#imageModal {
    backdrop-filter: blur(4px);
}

#modalImage {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}
</style>
