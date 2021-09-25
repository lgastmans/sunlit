<?php
/**
 * Error language file
 */

return [
    'unauthorized' => 'What are you doing my dude!?',
    'form_invalid_field' => 'Please provide a valid :field',
    'record_added' => ':Field couldn\'t be added',
    'record_edited' => ':Field couldn\'t be  modified',
    'record_deleted' => ':Field couldn\'t be  deleted',
    'resource_doesnt_exist' => ':Field doesn\'t exist',

    'supplier_has_purchase_order' => 'Cannot delete supplier because of existing purchase orders',

    'product_has_purchase_order_item' => 'Cannot delete product because of exisiting purchase order items',

    'warehouse_has_purchase_order' => 'Cannot delete warehouse because of exisiting purchase orders',

    'tax_has_product' => 'Cannot delete tax because of existing products',

    'category_has_product' => 'Cannot delete category because of existing products',

    'state_has_product' => 'Cannot delete state because of existing products',
    
];
