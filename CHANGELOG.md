# Changelog

Semua perubahan penting pada theme ini dicatat di file ini.

## [2.0.0] - 2026-02-27

### Added
- Repeater slider `home_slider_repeater` di Customizer.
- Repeater layanan `home_services_repeater` di Customizer.
- Field WYSIWYG untuk teks layanan pada repeater.
- Helper thumbnail dengan fallback `img/no-image.webp`.
- Helper icon Bootstrap untuk layanan.

### Changed
- Customizer child dipindah ke API native WordPress (tanpa ketergantungan Kirki).
- Output icon layanan child beralih dari Font Awesome ke Bootstrap Icons.
- Home News thumbnail memakai Bootstrap 5 ratio (`4:3`) dan selalu link ke post.
- Search form disesuaikan ke struktur Bootstrap 5.
- Override warna/background child yang bentrok dibersihkan agar ikut parent theme.

### Removed
- Ketergantungan file `inc/fontawesome.php`.
- Shortcode `resize-thumbnail`.
- Penggunaan `aq_resize` di child theme.
