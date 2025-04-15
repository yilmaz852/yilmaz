<?php
/**
 * Ürün Tablosu Ana Şablonu
 * 
 * Bu şablon dosyası, eklenti tarafından kullanılabilecek farklı tablo temaları için kullanılabilir.
 */

if (!defined('ABSPATH')) exit; // Doğrudan erişimi engelle
?>

<div class="wc-product-table-container">
    <?php if (!empty($args['show_search'])) : ?>
    <div class="wc-product-table-search">
        <input type="text" class="search-input" placeholder="<?php echo esc_attr__('Ürünleri ara...', 'urun-tablosu'); ?>" />
    </div>
    <?php endif; ?>
    
    <?php if (!empty($args['show_filters'])) : ?>
    <div class="wc-product-table-filters">
        <?php if (!empty($args['categories'])) : ?>
        <div class="filter-item filter-category">
            <label><?php esc_html_e('Kategori:', 'urun-tablosu'); ?></label>
            <select class="category-filter">
                <option value=""><?php esc_html_e('Tümü', 'urun-tablosu'); ?></option>
                <?php foreach ($args['categories'] as $cat) : ?>
                <option value="<?php echo esc_attr($cat->slug); ?>"><?php echo esc_html($cat->name); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($args['attributes'])) : ?>
            <?php foreach ($args['attributes'] as $attribute) : ?>
            <div class="filter-item filter-attribute">
                <label><?php echo esc_html($attribute['label']); ?>:</label>
                <select class="attribute-filter" data-attribute="<?php echo esc_attr($attribute['name']); ?>">
                    <option value=""><?php esc_html_e('Tümü