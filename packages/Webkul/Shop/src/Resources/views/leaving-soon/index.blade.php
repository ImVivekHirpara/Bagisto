<x-shop::layouts>
    <div class="main">
        {!! view_render_event('bagisto.shop.leaving_soon.index.before') !!}

        <div class="container-fluid">
            <!-- Page Header -->
            <div class="row">
                <div class="col-12">
                    <div class="page-header">
                        <h1 class="page-title text-3xl font-bold text-gray-700 flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                            {{ __('Leaving Soon') }}
                        </h1>
                        <p class="page-subtitle text-sm text-gray-500 text-center mt-2">
                            {{ __('Products with limited stock - Get them before they\'re gone!') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Stock Alert Banner -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="alert alert-warning flex items-center p-4 bg-yellow-100 border border-yellow-400 rounded-lg" role="alert">
                        <strong class="flex items-center mr-2"><i class="fas fa-hourglass-half text-yellow-600"></i></strong>
                        <span class="text-yellow-800">Limited Stock Alert! These products have less than 50 items remaining. Order now to avoid disappointment!</span>
                        <button type="button" class="close ml-auto text-yellow-600 hover:text-yellow-800" data-dismiss="alert">
                            <span>×</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            @if($products->count())
                <div class="row">
                    @foreach($products->unique('id') as $product) <!-- Added unique('id') to avoid duplicates -->
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-6">
                            <div class="product-card relative bg-white rounded-lg shadow-md overflow-hidden transition-transform duration-300 hover:-translate-y-2 hover:shadow-lg">
                                <!-- Stock Badge -->
                                <div class="stock-badge absolute top-2 right-2 z-10">
                                    @if($product->stock_quantity <= 10)
                                        <span class="badge bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                                            {{ $product->stock_quantity }} left
                                        </span>
                                    @else
                                        <span class="badge bg-blue-500 text-white text-xs px-2 py-1 rounded-full">
                                            New
                                        </span>
                                    @endif
                                </div>

                                <!-- Product Image -->
                                <div class="product-image-container relative">
                                    <a href="{{ url('products/' . $product->url_key) }}">
                                        @if($product->image_path)
                                            <img src="{{ asset('storage/' . $product->image_path) }}" 
                                                 class="w-full h-64 object-cover"
                                                 alt="{{ $product->name }}">
                                        @else
                                            <div class="no-image-placeholder flex items-center justify-center w-full h-64 bg-gray-100">
                                                <i class="fas fa-image text-gray-400 text-4xl"></i>
                                            </div>
                                        @endif
                                    </a>
                                </div>

                                <!-- Product Details -->
                                <div class="card-body p-4">
                                    <h5 class="card-title text-lg font-semibold text-gray-800">
                                        <a href="{{ url('products/' . $product->url_key) }}" 
                                           class="text-gray-800 hover:text-blue-500">
                                            {{ $product->name }}
                                        </a>
                                    </h5>
                                    
                                    <p class="text-gray-500 text-sm mt-1">
                                        SKU: {{ $product->sku }}
                                    </p>

                                    @if($product->short_description)
                                        <p class="text-gray-600 text-sm mt-1">
                                            {{ Str::limit($product->short_description, 80) }}
                                        </p>
                                    @endif

                                    <!-- Stock Information -->
                                    <div class="stock-info mt-2">
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            @php
                                                $stockPercentage = min(($product->stock_quantity / 50) * 100, 100);
                                                $progressClass = $stockPercentage > 20 ? 'bg-yellow-500' : 'bg-red-500';
                                            @endphp
                                            <div class="h-2 rounded-full" style="width: {{ $stockPercentage }}%; background-color: {{ $progressClass }}"></div>
                                        </div>
                                        <small class="text-gray-600 text-xs mt-1 flex items-center">
                                            <i class="fas fa-box mr-1"></i> 
                                            Only {{ $product->stock_quantity }} items remaining
                                        </small>
                                    </div>

                                    <!-- Price Information -->
                                    <div class="price-section mt-3">
                                        @php
                                            $currentPrice = $product->min_price ?? $product->price;
                                            $regularPrice = $product->regular_min_price ?? $product->price;
                                            $hasDiscount = $currentPrice && $regularPrice && $currentPrice < $regularPrice;

                                            if ($product->special_price && 
                                                ($product->special_price < ($product->price ?? $regularPrice)) &&
                                                (!$product->special_price_from || now() >= $product->special_price_from) &&
                                                (!$product->special_price_to || now() <= $product->special_price_to)) {
                                                $currentPrice = $product->special_price;
                                                $regularPrice = $product->price ?? $regularPrice;
                                                $hasDiscount = true;
                                            }
                                        @endphp
                                        
                                        @if($currentPrice)
                                            <div class="price-container flex items-center flex-wrap gap-2">
                                                <span class="current-price text-xl font-bold text-blue-600">
                                                    {{ core()->currency($currentPrice) }}
                                                </span>
                                                
                                                @if($hasDiscount)
                                                    <span class="original-price text-gray-500 line-through text-sm">
                                                        {{ core()->currency($regularPrice) }}
                                                    </span>
                                                    @php
                                                        $discount = round((($regularPrice - $currentPrice) / $regularPrice) * 100);
                                                    @endphp
                                                    <span class="discount-badge bg-green-500 text-white text-xs px-2 py-1 rounded">
                                                        {{ $discount }}% OFF
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="action-buttons mt-4">
                                        <div class="flex space-x-2">
                                            <button class="btn bg-blue-500 text-white px-4 py-2 rounded-lg w-full hover:bg-blue-600" 
                                                    data-product-id="{{ $product->id }}">
                                                <i class="fas fa-shopping-cart mr-2"></i> Add to Cart
                                            </button>
                                            <button class="btn bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300" 
                                                    data-product-id="{{ $product->id }}">
                                                <i class="fas fa-heart"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="row">
                    <div class="col-12">
                        <div class="pagination-wrapper flex justify-center mt-4">
                            {{ $products->links() }}
                        </div>
                    </div>
                </div>
            @else
                <!-- No Products Found -->
                <div class="row">
                    <div class="col-12">
                        <div class="no-products-found text-center py-10">
                            <div class="empty-state">
                                <i class="fas fa-check-circle text-green-500 text-4xl mb-3"></i>
                                <h3 class="text-gray-600 text-lg">Great News!</h3>
                                <p class="text-gray-500 mt-2">
                                    All products are well-stocked at the moment. 
                                    Check back later or browse our full catalog.
                                </p>
                                <a href="{{ route('shop.home.index') }}" class="btn bg-blue-500 text-white px-4 py-2 mt-4 inline-block hover:bg-blue-600">
                                    <i class="fas fa-home mr-2"></i> Continue Shopping
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {!! view_render_event('bagisto.shop.leaving_soon.index.after') !!}
    </div>

    <style>
        .page-header {
            padding: 2rem 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .product-card {
            height: 100%;
        }

        .product-image-container img {
            transition: opacity 0.3s;
        }

        .product-image-container:hover img {
            opacity: 0.8;
        }

        .stock-badge .badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        .price-container {
            gap: 0.5rem;
        }

        .action-buttons .btn {
            font-size: 0.875rem;
        }

        .empty-state {
            padding: 3rem;
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 1.5rem;
            }
            .product-card {
                margin-bottom: 1rem;
            }
        }
    </style>
</x-shop::layouts>