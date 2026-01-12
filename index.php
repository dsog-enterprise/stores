<?php
require_once 'config.php';
require_once 'functions.php';

$pageTitle = 'Home';
$currentFranchisee = getCurrentFranchisee();

// Get featured products
$featuredProducts = getProducts(null, 8);
$collections = getCollections();

// Hero slides data
$heroSlides = [
    [
        'image' => 'https://i.postimg.cc/B6NZCQfY/White-and-Blue-Modern-Minimalist-Shoes-New-Arrival-Instagram-Post.webp',
        'title' => '',
        'subtitle' => ''
    ],
    [
        'image' => 'https://i.postimg.cc/nhpfy6qX/Red-and-White-Modern-Sport-Shoes-Fashion-Instagram-Post.webp',
        'title' => '',
        'subtitle' => ''
    ],
    [
        'image' => 'https://i.postimg.cc/65nHZ8Tz/Whats-App-Image-2025-11-27-at-17-32-18-1.jpg',
        'title' => '',
        'subtitle' => ''
    ]
];

// Gifts data
$giftsData = [
    [
        'icon' => 'fas fa-gift',
        'title' => 'Gift Box Sets',
        'description' => 'Curated fashion gift packages for special occasions'
    ],
    [
        'icon' => 'fas fa-tshirt',
        'title' => 'Custom Bundles',
        'description' => 'Create your own gift bundle with multiple items'
    ],
    [
        'icon' => 'fas fa-shipping-fast',
        'title' => 'Express Delivery',
        'description' => 'Special gift wrapping and fast delivery options'
    ],
    [
        'icon' => 'fas fa-star',
        'title' => 'Premium Packaging',
        'description' => 'Luxury packaging for the perfect presentation'
    ]
];

include 'includes/header.php';
?>

<!-- DSOG Hero Slideshow -->
<section class="hero" id="home">
    <div class="hero-slides" id="heroSlides">
        <?php foreach ($heroSlides as $index => $slide): ?>
        <div class="hero-slide <?php echo $index === 0 ? 'active' : ''; ?>">
            <img src="<?php echo $slide['image']; ?>" alt="<?php echo $slide['title']; ?>" loading="lazy">
            <div class="hero-overlay">
                <h1 class="hero-title"><?php echo $slide['title']; ?></h1>
                <p class="hero-subtitle"><?php echo $slide['subtitle']; ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="hero-dots" id="heroDots">
        <?php foreach ($heroSlides as $index => $slide): ?>
        <div class="dot <?php echo $index === 0 ? 'active' : ''; ?>" onclick="showSlide(<?php echo $index; ?>)"></div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Collections Section -->
<section class="collections" id="collections">
    <div class="section-title">
        <h2>Our Collections</h2>
        <p>Curated fashion collections for every style</p>
    </div>
    
    <div class="collections-grid" id="collectionsContainer">
        <?php foreach ($collections as $collection): ?>
        <a href="<?php echo SITE_URL; ?>/pages/<?php echo strtolower($collection['slug']); ?>.php" class="collection-card">
            <img src="<?php echo $collection['image_url']; ?>" alt="<?php echo $collection['name']; ?>" class="collection-image" loading="lazy">
            <div class="collection-overlay">
                <h3 class="collection-title"><?php echo $collection['name']; ?></h3>
                <p class="collection-description"><?php echo $collection['description']; ?></p>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- Products Section -->
<section class="products" id="products">
    <div class="section-title">
        <h2>Featured Products</h2>
        <p>Premium fashion items with direct WhatsApp ordering</p>
    </div>
    
    <!-- Product Filters -->
    <div class="product-filters" id="productFilters">
        <button class="filter-btn active" data-filter="all">All Products</button>
        <button class="filter-btn" data-filter="mens">Men's</button>
        <button class="filter-btn" data-filter="womens">Women's</button>
        <button class="filter-btn" data-filter="kids">Kids</button>
        <button class="filter-btn" data-filter="accessories">Accessories</button>
        <button class="filter-btn" data-filter="gifts">Gifts</button>
    </div>
    
    <!-- Products Grid -->
    <div class="products-grid" id="productsContainer">
        <?php if (empty($featuredProducts)): ?>
        <div class="skeleton-grid" id="skeletonLoader">
            <?php for ($i = 0; $i < 4; $i++): ?>
            <div class="skeleton-card">
                <div class="skeleton-image"></div>
                <div class="skeleton-info">
                    <div class="skeleton-text short"></div>
                    <div class="skeleton-text medium"></div>
                    <div class="skeleton-text" style="width: 40%;"></div>
                    <div class="skeleton-btn"></div>
                </div>
            </div>
            <?php endfor; ?>
        </div>
        <?php else: ?>
        <?php foreach ($featuredProducts as $product): ?>
        <div class="product-card" onclick="openProductModal(<?php echo htmlspecialchars(json_encode($product)); ?>)">
            <div class="product-image-wrapper">
                <img src="<?php echo $product['images_array'][0]; ?>" alt="<?php echo $product['name']; ?>" class="product-image" loading="lazy">
                <button class="view-image-btn" onclick="event.stopPropagation(); openFullImageView('<?php echo implode(',', $product['images_array']); ?>', '<?php echo addslashes($product['name']); ?>')">
                    <i class="fas fa-expand"></i>
                </button>
            </div>
            <div class="product-info">
                <div class="product-badge"><?php echo ucfirst($product['category']); ?></div>
                <h3 class="product-title"><?php echo $product['name']; ?></h3>
                <div class="product-price">KSh <?php echo number_format($product['price']); ?></div>
                <p class="product-description"><?php echo substr($product['description'], 0, 100) . '...'; ?></p>
                <button class="whatsapp-btn" onclick="event.stopPropagation(); orderViaWhatsApp('<?php echo addslashes($product['name']); ?>', <?php echo $product['price']; ?>)">
                    <i class="fab fa-whatsapp"></i> Order via WhatsApp
                </button>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <!-- Load More Button -->
    <?php if (count($featuredProducts) >= 8): ?>
    <div id="loadMoreContainer" style="text-align: center; margin-top: 3rem;">
        <button id="loadMoreBtn" style="padding: 1rem 2.5rem; background: var(--primary-red); color: white; border: none; border-radius: 30px; font-weight: 600; cursor: pointer; transition: var(--transition); font-size: 1rem;">
            <i class="fas fa-spinner fa-spin" style="margin-right: 0.5rem; display: none;"></i>
            Load More Products
        </button>
    </div>
    <?php endif; ?>
</section>

<!-- Gifts Section -->
<section class="gifts" id="gifts">
    <div class="section-title">
        <h2>Gifts & Specials</h2>
        <p>Perfect presents for every occasion</p>
    </div>
    
    <div class="gifts-grid" id="giftsContainer">
        <?php foreach ($giftsData as $gift): ?>
        <div class="gift-card" onclick="window.location.href='<?php echo SITE_URL; ?>/pages/gifts.php'">
            <div class="gift-icon">
                <i class="<?php echo $gift['icon']; ?>"></i>
            </div>
            <h3 class="gift-title"><?php echo $gift['title']; ?></h3>
            <p class="gift-description"><?php echo $gift['description']; ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Product Modal -->
<div class="product-modal" id="productModal">
    <div class="modal-content">
        <button class="modal-close" id="modalClose">
            <i class="fas fa-times"></i>
        </button>
        <div class="modal-body" id="modalBody">
            <!-- Modal content will be populated by JavaScript -->
        </div>
    </div>
</div>

<!-- Full Image View Modal -->
<div class="full-image-modal" id="fullImageModal">
    <button class="full-image-close" id="fullImageClose">
        <i class="fas fa-times"></i>
    </button>
    
    <div class="full-image-nav">
        <button class="image-nav-btn" id="prevImageBtn">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="image-nav-btn" id="nextImageBtn">
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>
    
    <div class="full-image-content">
        <div class="full-image-container" id="fullImageContainer">
            <img src="" alt="" class="full-image" id="fullImage">
        </div>
        
        <div class="full-image-controls">
            <button class="image-control-btn" id="zoomInBtn">
                <i class="fas fa-search-plus"></i>
            </button>
            <button class="image-control-btn" id="zoomOutBtn">
                <i class="fas fa-search-minus"></i>
            </button>
            <button class="image-control-btn" id="resetZoomBtn">
                <i class="fas fa-expand"></i>
            </button>
            <button class="image-control-btn" id="rotateBtn">
                <i class="fas fa-redo"></i>
            </button>
            <button class="image-control-btn" id="downloadBtn">
                <i class="fas fa-download"></i>
            </button>
        </div>
        
        <div class="image-counter" id="imageCounter">1 / 1</div>
    </div>
</div>

<?php
$additionalJS = <<<JS
// DSOG Hero Slideshow
let currentSlide = 0;
const slideInterval = 5000;

function showSlide(index) {
    const slides = document.querySelectorAll('.hero-slide');
    const dots = document.querySelectorAll('.dot');
    
    slides.forEach((slide, i) => {
        slide.classList.toggle('active', i === index);
    });
    
    dots.forEach((dot, i) => {
        dot.classList.toggle('active', i === index);
    });
    
    currentSlide = index;
}

// Auto-advance slides
setInterval(() => {
    currentSlide = (currentSlide + 1) % document.querySelectorAll('.hero-slide').length;
    showSlide(currentSlide);
}, slideInterval);

// Product modal functionality
function openProductModal(product) {
    const modal = document.getElementById('productModal');
    const modalBody = document.getElementById('modalBody');
    
    const images = product.images_array || [product.image_url];
    const imageUrls = images.join(',');
    
    modalBody.innerHTML = \`
        <div class="modal-image-section">
            <div class="modal-image-container" onclick="openFullImageView('\${imageUrls.replace(/'/g, "\\\\'")}', '\${product.name.replace(/'/g, "\\\\'")}')">
                <img src="\${images[0]}" alt="\${product.name}" class="modal-main-image" loading="lazy">
                <button class="image-zoom-btn">
                    <i class="fas fa-expand"></i>
                    Click to view full image
                </button>
            </div>
            \${images.length > 1 ? \`
                <div class="image-gallery" id="imageGallery">
                    \${images.map((img, index) => \`
                        <div class="gallery-thumbnail \${index === 0 ? 'active' : ''}" data-index="\${index}" onclick="switchModalImage('\${img.replace(/'/g, "\\\\'")}', \${index})">
                            <img src="\${img}" alt="\${product.name} - View \${index + 1}" loading="lazy">
                        </div>
                    \`).join('')}
                </div>
            \` : ''}
        </div>
        <div class="modal-details">
            <h2 class="modal-title">\${product.name}</h2>
            <div class="modal-price">KSh \${parseInt(product.price).toLocaleString()}</div>
            <div class="modal-category">\${product.category ? product.category.charAt(0).toUpperCase() + product.category.slice(1) : 'Featured'}</div>
            <p class="modal-description">\${product.description || 'Premium quality product from DSOG STORES. Crafted with attention to detail and using the finest materials.'}</p>
            
            <div class="product-specs">
                <h3 class="specs-title">Product Details</h3>
                <ul class="specs-list">
                    <li>
                        <span class="spec-label">Category:</span>
                        <span class="spec-value">\${product.category || product.collection || 'General'}</span>
                    </li>
                    <li>
                        <span class="spec-label">Material:</span>
                        <span class="spec-value">Premium Quality</span>
                    </li>
                    <li>
                        <span class="spec-label">Availability:</span>
                        <span class="spec-value">In Stock</span>
                    </li>
                </ul>
            </div>
            
            <button class="modal-whatsapp-btn" onclick="orderViaWhatsApp('\${product.name.replace(/'/g, "\\\\'")}', \${product.price})">
                <i class="fab fa-whatsapp"></i> Order via WhatsApp
            </button>
        </div>
    \`;
    
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function switchModalImage(imageSrc, index) {
    const mainImage = document.querySelector('.modal-main-image');
    const thumbnails = document.querySelectorAll('.gallery-thumbnail');
    
    if (mainImage) {
        mainImage.src = imageSrc;
    }
    
    thumbnails.forEach(thumb => {
        thumb.classList.remove('active');
        if (parseInt(thumb.dataset.index) === index) {
            thumb.classList.add('active');
        }
    });
}

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('productModal');
        if (modal && modal.classList.contains('active')) {
            closeModal();
        }
        
        const fullImageModal = document.getElementById('fullImageModal');
        if (fullImageModal && fullImageModal.classList.contains('active')) {
            closeFullImageModal();
        }
    }
});
JS;

include 'includes/footer.php';
?>
