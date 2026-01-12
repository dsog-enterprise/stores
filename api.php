<?php
require_once 'config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

$action = $_GET['action'] ?? '';

try {
    $db = getDBConnection();
    
    switch ($action) {
        case 'getProducts':
            $collection = $_GET['collection'] ?? '';
            
            $sql = "SELECT p.*, 
                    GROUP_CONCAT(pi.image_url ORDER BY pi.is_primary DESC, pi.display_order ASC) as images,
                    c.name as category_name
                    FROM products p
                    LEFT JOIN categories c ON p.category_id = c.id
                    LEFT JOIN product_images pi ON p.id = pi.product_id
                    WHERE p.is_active = 1";
            
            if ($collection) {
                $sql .= " AND (p.collection_name = ? OR c.slug = ?)";
                $params = [$collection, $collection];
            }
            
            $sql .= " GROUP BY p.id ORDER BY p.is_featured DESC, p.created_at DESC";
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params ?? []);
            $products = $stmt->fetchAll();
            
            // Format products
            $formattedProducts = [];
            foreach ($products as $product) {
                $formattedProducts[] = [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'description' => $product['description'],
                    'basePrice' => floatval($product['base_price']),
                    'category' => $product['category_name'],
                    'collectionName' => $product['collection_name'],
                    'images' => $product['images'] ? explode(',', $product['images']) : [$product['image'] ?? ''],
                    'image' => $product['images'] ? explode(',', $product['images'])[0] : '',
                    'isFeatured' => (bool)$product['is_featured'],
                    'stock' => intval($product['stock_quantity'])
                ];
            }
            
            echo json_encode([
                'success' => true,
                'data' => $formattedProducts
            ]);
            break;
            
        case 'getCategories':
            $stmt = $db->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY display_order");
            $categories = $stmt->fetchAll();
            
            echo json_encode([
                'success' => true,
                'data' => $categories
            ]);
            break;
            
        case 'submitOrder':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data || !isset($data['customerName'], $data['customerPhone'], $data['customerAddress'])) {
                throw new Exception('Missing required fields');
            }
            
            // Generate order number
            $orderNumber = 'DSOG-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));
            
            // Calculate totals
            $subtotal = 0;
            $items = [];
            
            foreach ($data['items'] as $item) {
                $itemTotal = $item['price'] * $item['quantity'];
                $subtotal += $itemTotal;
                $items[] = $item;
            }
            
            $deliveryFee = $data['deliveryOption'] === 'express' ? 300 : 150;
            $total = $subtotal + $deliveryFee;
            
            // Save order to database
            $stmt = $db->prepare("INSERT INTO orders (order_number, customer_name, customer_phone, customer_address, customer_notes, franchisee_id, franchisee_name, subtotal, delivery_fee, total_amount, delivery_option, whatsapp_message) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $orderNumber,
                $data['customerName'],
                $data['customerPhone'],
                $data['customerAddress'],
                $data['customerNotes'] ?? '',
                $data['franchiseeId'] ?? '',
                $data['franchiseeName'] ?? '',
                $subtotal,
                $deliveryFee,
                $total,
                $data['deliveryOption'],
                $data['whatsappMessage'] ?? ''
            ]);
            
            $orderId = $db->lastInsertId();
            
            // Save order items
            foreach ($items as $item) {
                $stmt = $db->prepare("INSERT INTO order_items (order_id, product_id, product_name, product_price, quantity, total_price) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $orderId,
                    $item['id'] ?? null,
                    $item['name'],
                    $item['price'],
                    $item['quantity'],
                    $item['price'] * $item['quantity']
                ]);
            }
            
            echo json_encode([
                'success' => true,
                'orderNumber' => $orderNumber,
                'orderId' => $orderId
            ]);
            break;
            
        case 'getSettings':
            $stmt = $db->query("SELECT setting_key, setting_value FROM settings");
            $settings = [];
            
            while ($row = $stmt->fetch()) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
            
            echo json_encode([
                'success' => true,
                'data' => $settings
            ]);
            break;
            
        default:
            throw new Exception('Invalid action');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
