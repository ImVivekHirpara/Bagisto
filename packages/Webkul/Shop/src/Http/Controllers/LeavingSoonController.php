<?php

namespace Webkul\Shop\Http\Controllers;

use Illuminate\Http\Request;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Inventory\Repositories\InventorySourceRepository;
use Illuminate\Support\Facades\DB;

class LeavingSoonController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * ProductRepository object
     *
     * @var \Webkul\Product\Repositories\ProductRepository
     */
    protected $productRepository;

    /**
     * InventorySourceRepository object
     *
     * @var \Webkul\Inventory\Repositories\InventorySourceRepository
     */
    protected $inventorySourceRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Product\Repositories\ProductRepository  $productRepository
     * @param  \Webkul\Inventory\Repositories\InventorySourceRepository  $inventorySourceRepository
     * @return void
     */
    public function __construct(
        ProductRepository $productRepository,
        InventorySourceRepository $inventorySourceRepository
    ) {
        $this->middleware('theme');

        $this->_config = request('_config');

        $this->productRepository = $productRepository;

        $this->inventorySourceRepository = $inventorySourceRepository;
    }

    /**
     * Display leaving soon products page
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Get products with low stock (less than 50)
        $products = $this->getLowStockProducts();

        return view($this->_config['view'], compact('products'));
    }

    /**
     * Get products with low stock
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    private function getLowStockProducts()
    {
        $products = DB::table('products')
            ->join('product_flat', 'products.id', '=', 'product_flat.product_id')
            ->join('product_inventories', 'products.id', '=', 'product_inventories.product_id')
            ->leftJoin('product_images', function($join) {
                $join->on('products.id', '=', 'product_images.product_id')
                     ->where('product_images.type', '=', 'base');
            })
            ->leftJoin('product_price_indices', 'products.id', '=', 'product_price_indices.product_id')
            ->select(
                'products.id',
                'products.sku',
                'product_flat.name',
                'product_flat.short_description',
                'product_flat.description',
                'product_flat.url_key',
                'product_flat.price',
                'product_flat.special_price',
                'product_flat.special_price_from',
                'product_flat.special_price_to',
                'product_inventories.qty as stock_quantity',
                'product_images.path as image_path',
                'product_price_indices.min_price',
                'product_price_indices.regular_min_price'
            )
            ->where('product_flat.status', 1)
            ->where('product_flat.visible_individually', 1)
            ->where('product_inventories.qty', '<', 50)
            ->where('product_inventories.qty', '>', 0)
            ->where('product_flat.channel', core()->getCurrentChannel()->code)
            ->where('product_flat.locale', core()->getCurrentLocale()->code)
            ->orderBy('product_inventories.qty', 'asc')
            ->paginate(12);

        return $products;
    }
}