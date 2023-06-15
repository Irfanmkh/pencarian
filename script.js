var button = document.getElementById('btn');
var popup = document.getElementById('popupWindow');
var closeButton = document.getElementById('closeButton');

// Fungsi untuk menampilkan jendela popup
function showPopup() {
  popup.style.display = 'block';
}

// Fungsi untuk menyembunyikan jendela popup
function hidePopup() {
  popup.style.display = 'none';
}

// Menambahkan event listener saat button diklik
button.addEventListener('click', popupWindow);

// Menambahkan event listener saat tombol tutup diklik
closeButton.addEventListener('click', hidePopup);

// Menyembunyikan jendela popup saat halaman dimuat
window.onload = function() {
  popup.style.display = 'none';
};