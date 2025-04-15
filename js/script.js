/**
 * Ürün Tablosu JavaScript İşlevleri
 */
document.addEventListener('DOMContentLoaded', function() {
    // Tüm ürün tablolarını seç
    const productTables = document.querySelectorAll('.urun-tablosu');
    
    // Her tablo için işlevsellik ekle
    productTables.forEach(function(table) {
        // AJAX sepete ekleme işlevi
        const addToCartButtons = table.querySelectorAll('.add_to_cart_button');
        
        addToCartButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const productId = this.getAttribute('data-product_id');
                const quantity = this.closest('form').querySelector('.qty').value || 1;
                const button = this;
                
                // Sayfa yenileme olmadan AJAX ile sepete ekle
                button.classList.add('loading');
                
                fetch('/?wc-ajax=add_to_cart', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'product_id=' + productId + '&quantity=' + quantity
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        // Hata mesajı göster
                        showNotification(data.message, 'error');
                    } else {
                        // Başarılı mesajı göster
                        showNotification(data.message || 'Ürün sepete eklendi.', 'success');
                        
                        // Mini sepeti güncelle (eğer varsa)
                        updateMiniCart();
                    }
                    button.classList.remove('loading');
                })
                .catch(error => {
                    showNotification('Bir hata oluştu, lütfen tekrar deneyin.', 'error');
                    button.classList.remove('loading');
                });
            });
        });
        
        // Tablo responsiveness kontrolü
        function setupResponsiveTables() {
            const mode = table.closest('.urun-tablosu-wrapper').classList.contains('responsive-collapse') ? 'collapse' : null;
            
            if (mode === 'collapse' && window.innerWidth <= 768) {
                const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent.trim());
                
                table.querySelectorAll('tbody tr').forEach(row => {
                    row.querySelectorAll('td').forEach((cell, index) => {
                        if (headers[index]) {
                            cell.setAttribute('data-title', headers[index]);
                        }
                    });
                });
            }
        }
        
        setupResponsiveTables();
        window.addEventListener('resize', setupResponsiveTables);
    });
    
    // Bildirim gösterme işlevi
    function showNotification(message, type = 'success') {
        // Varsa eski bildirimleri temizle
        const existingNotifications = document.querySelectorAll('.urun-tablosu-notification');
        existingNotifications.forEach(notification => {
            notification.remove();
        });
        
        // Yeni bildirim oluştur
        const notification = document.createElement('div');
        notification.className = `urun-tablosu-notification ${type}`;
        notification.innerHTML = message;
        
        // Body'ye ekle
        document.body.appendChild(notification);
        
        // CSS ekle
        notification.style.position = 'fixed';
        notification.style.top = '20px';
        notification.style.right = '20px';
        notification.style.padding = '12px 20px';
        notification.style.borderRadius = '4px';
        notification.style.color = '#fff';
        notification.style.fontSize = '14px';
        notification.style.fontWeight = 'bold';
        notification.style.zIndex = '9999';
        notification.style.boxShadow = '0 2px 10px rgba(0,0,0,0.2)';
        notification.style.transition = 'opacity 0.3s ease';
        
        if (type === 'success') {
            notification.style.backgroundColor = '#4CAF50';
        } else if (type === 'error') {
            notification.style.backgroundColor = '#F44336';
        }
        
        // Belirli bir süre sonra kaldır
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }
    
    // Mini sepeti güncelleme işlevi
    function updateMiniCart() {
        // WooCommerce sepet parçalarını güncelle
        if (typeof wc_cart_fragments_params !== 'undefined') {
            jQuery(document.body).trigger('wc_fragment_refresh');
        }
    }
});

// Admin sayfası için JavaScript
if (typeof jQuery !== 'undefined' && document.querySelector('.urun-tablosu-admin')) {
    jQuery(document).ready(function($) {
        // Sekmeleri işle
        $('.nav-tab').on('click', function(e) {
            e.preventDefault();
            
            // Aktif sekmeyi değiştir
            $('.nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            
            // İçeriği göster/gizle
            $('.tab-content').hide();
            $($(this).attr('href')).show();
        });
    });
}