/**
 * Ürün Tablosu Admin JavaScript
 */
jQuery(document).ready(function($) {
    // Renk seçici
    if (typeof $.fn.wpColorPicker === 'function') {
        $('.color-field').wpColorPicker();
    }
    
    // Sekme sistemi
    $('.nav-tab').on('click', function(e) {
        e.preventDefault();
        
        // Aktif sekmeyi değiştir
        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        
        // İçeriği göster/gizle
        $('.tab-content').hide();
        $($(this).attr('href')).show();
    });
    
    // Sütun ekleme/silme
    $('.add-column').on('click', function() {
        var row = '<tr>';
        row += '<td><input type="text" name="column_key[]" value="" class="regular-text" /></td>';
        row += '<td><input type="text" name="column_name[]" value="" class="regular-text" /></td>';
        row += '<td><button type="button" class="button remove-column">Kaldır</button></td>';
        row += '</tr>';
        
        $('#urun-tablosu-columns tbody').append(row);
    });
    
    // Sütun kaldırma
    $('#urun-tablosu-columns').on('click', '.remove-column', function() {
        if ($('#urun-tablosu-columns tbody tr').length > 1) {
            $(this).closest('tr').remove();
        } else {
            alert('En az bir sütun olmalıdır!');
        }
    });
    
    // Ayarları dışa aktarma
    $('#export-settings').on('click', function() {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'urun_tablosu_export_settings',
                nonce: $('#export_nonce').val()
            },
            success: function(response) {
                if (response.success) {
                    // Ayarları JSON dosyası olarak indir
                    var blob = new Blob([JSON.stringify(response.data, null, 2)], {type: 'application/json'});
                    var link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.download = 'urun-tablosu-ayarlari.json';
                    link.click();
                    URL.revokeObjectURL(link.href);
                } else {
                    alert('Ayarlar dışa aktarılırken bir hata oluştu.');
                }
            }
        });
    });
    
    // Örnek tablo oluştur
    if ($('.urun-tablosu-preview').length) {
        function updatePreview() {
            var columns = $('input[name="urun_tablosu_default_columns"]').val().split(',');
            var widths = $('input[name="urun_tablosu_default_widths"]').val().split(',');
            
            var $previewTable = $('.urun-tablosu-preview table');
            var $previewHead = $previewTable.find('thead tr');
            var $previewBody = $previewTable.find('tbody tr');
            
            // Tabloyu temizle
            $previewHead.empty();
            $previewBody.empty();
            
            // Başlıkları oluştur
            for (var i = 0; i < columns.length; i++) {
                var column = columns[i].trim();
                var width = (widths[i] || 'auto').trim();
                
                $previewHead.append('<th style="width:' + width + '">' + column + '</th>');
                $previewBody.append('<td>' + getPreviewContent(column) + '</td>');
            }
        }
        
        function getPreviewContent(column) {
            switch (column) {
                case 'image':
                    return '<img src="' + urunTablosuAdmin.placeholder_image + '" width="50" height="50">';
                case 'name':
                    return '<a href="#">Örnek Ürün</a>';
                case 'sku':
                    return 'ORN-001';
                case 'price':
                    return '<span class="price">₺99,99</span>';
                case 'stock':
                    return '<span class="in-stock">Stokta</span>';
                case 'add_to_cart':
                    return '<button class="button">Sepete Ekle</button>';
                default:
                    return column;
            }
        }
        
        // İlk yükleme ve değişiklikler
        updatePreview();
        $('input[name="urun_tablosu_default_columns"], input[name="urun_tablosu_default_widths"]').on('change', updatePreview);
    }
});