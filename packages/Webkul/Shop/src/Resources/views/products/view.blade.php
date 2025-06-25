@inject ('reviewHelper', 'Webkul\Product\Helpers\Review')
@inject ('productViewHelper', 'Webkul\Product\Helpers\View')

@php
    $avgRatings = $reviewHelper->getAverageRating($product);
    $percentageRatings = $reviewHelper->getPercentageRating($product);
    $customAttributeValues = $productViewHelper->getAdditionalData($product);
    $attributeData = collect($customAttributeValues)->filter(fn ($item) => ! empty($item['value']));

    // Helper function to format attribute values with units
    function formatAttributeValue($attributeValue) {
        $code = strtolower($attributeValue['code']);
        $label = strtolower($attributeValue['label']);
        $value = $attributeValue['value'];
        
        // Check for weight attributes (net weight, gross weight)
        if (str_contains($code, 'weight') || str_contains($label, 'weight')) {
            return $value . ' Gm';
        }
        
        // Check for purity attribute
        if (str_contains($code, 'purity') || str_contains($label, 'purity')) {
            return $value . 'K';
        }
        
        // Return original value for other attributes
        return $value;
    }
@endphp

<!-- SEO Meta Content -->
@push('meta')
    <meta name="description" content="{{ trim($product->meta_description) != "" ? $product->meta_description : \Illuminate\Support\Str::limit(strip_tags($product->description), 120, '') }}"/>
    <meta name="keywords" content="{{ $product->meta_keywords }}"/>

    @if (core()->getConfigData('catalog.rich_snippets.products.enable'))
        <script type="application/ld+json">
            {!! app('Webkul\Product\Helpers\SEO')->getProductJsonLd($product) !!}
        </script>
    @endif

    <?php $productBaseImage = product_image()->getProductBaseImage($product); ?>

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ $product->name }}" />
    <meta name="twitter:description" content="{!! htmlspecialchars(trim(strip_tags($product->description))) !!}" />
    <meta name="twitter:image:alt" content="" />
    <meta name="twitter:image" content="{{ $productBaseImage['medium_image_url'] }}" />
    <meta property="og:type" content="og:product" />
    <meta property="og:title" content="{{ $product->name }}" />
    <meta property="og:image" content="{{ $productBaseImage['medium_image_url'] }}" />
    <meta property="og:description" content="{!! htmlspecialchars(trim(strip_tags($product->description))) !!}" />
    <meta property="og:url" content="{{ route('shop.product_or_category.index', $product->url_key) }}" />
@endpush

<!-- Page Layout -->
<x-shop::layouts>
    <!-- Page Title -->
    <x-slot:title>
        {{ trim($product->meta_title) != "" ? $product->meta_title : $product->name }}
    </x-slot>
    
    {!! view_render_event('bagisto.shop.products.view.before', ['product' => $product]) !!}

    <!-- Breadcrumbs -->
    @if ((core()->getConfigData('general.general.breadcrumbs.shop')))
        <div class="flex justify-center px-7 max-lg:hidden">
            <x-shop::breadcrumbs
                name="product"
                :entity="$product"
            />
        </div>
    @endif

    <!-- Product Information Vue Component -->
    <v-product>
        <x-shop::shimmer.products.view />
        <div class="container mt-[60px] max-1180:px-5">
            <div class="mt-8 grid max-w-max grid-cols-[auto_1fr] gap-4">
                @foreach ($customAttributeValues as $customAttributeValue)
                    @if (! empty($customAttributeValue['value']) && in_array(strtolower($customAttributeValue['label']), ['purity', 'gross weight', 'net weight']))
                        <div class="grid">
                            <p class="text-base text-black">
                                {!! $customAttributeValue['label'] !!}
                            </p>
                        </div>

                        @if ($customAttributeValue['type'] == 'file')
                            <a
                                href="{{ Storage::url($product[$customAttributeValue['code']]) }}"
                                download="{{ $customAttributeValue['label'] }}"
                            >
                                <span class="icon-download text-2xl"></span>
                            </a>
                        @elseif ($customAttributeValue['type'] == 'image')
                            <a
                                href="{{ Storage::url($product[$customAttributeValue['code']]) }}"
                                download="{{ $customAttributeValue['label'] }}"
                            >
                                <img
                                    class="h-5 min-h-5 w-5 min-w-5"
                                    src="{{ Storage::url($customAttributeValue['value']) }}"
                                />
                            </a>
                        @else
                            <div class="grid">
                                <p class="text-base text-zinc-500">
                                    {!! formatAttributeValue($customAttributeValue) !!}
                                </p>
                            </div>
                        @endif
                    @endif
                @endforeach
            </div>
        </div>
    </v-product>

    <!-- Information Section -->
    <div class="1180:mt-20">
        <div class="max-1180:hidden">
            <x-shop::tabs
                position="center"
                ref="productTabs"
            >
                <!--Custom Detail Tab -->
                <x-shop::tabs.item
                    id="detail-tab"
                    class="container mt-6 !p-0"
                    :title="trans('shop::app.products.view.detail')"
                    :is-selected="true"
                >
                    <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <ul class="space-y-3 text-gray-700 text-sm sm:text-base">
                            @foreach ($customAttributeValues as $customAttributeValue)
                                @if (! empty($customAttributeValue['value']) && ! in_array(strtolower($customAttributeValue['label']), ['purity', 'gross weight', 'net weight']))
                                    <li class="flex items-center p-3 bg-white border-b border-gray-100">
                                        <div class="font-medium text-gray-900 w-1/3">
                                            {{ $customAttributeValue['label'] }}:
                                        </div>
                                        <div class="w-2/3 text-gray-800">
                                            @if ($customAttributeValue['type'] == 'file')
                                                <a href="{{ Storage::url($product[$customAttributeValue['code']]) }}" download="{{ $customAttributeValue['label'] }}" class="text-blue-600 hover:text-blue-700">
                                                    <span class="icon-download text-lg mr-1"></span> Download
                                                </a>
                                            @elseif ($customAttributeValue['type'] == 'image')
                                                <a href="{{ Storage::url($product[$customAttributeValue['code']]) }}" download="{{ $customAttributeValue['label'] }}" class="inline-block">
                                                    <img src="{{ Storage::url($customAttributeValue['value']) }}" alt="{{ $customAttributeValue['label'] }}" class="h-8 w-8 object-cover rounded">
                                                </a>
                                            @else
                                                <span class="text-gray-800">{!! formatAttributeValue($customAttributeValue) !!}</span>
                                            @endif
                                        </div>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </x-shop::tabs.item>

                <!-- Description Tab -->
                {!! view_render_event('bagisto.shop.products.view.description.before', ['product' => $product]) !!}

                <x-shop::tabs.item
                    id="description-tab"
                    class="container mt-[60px] !p-0"
                    :title="trans('shop::app.products.view.description')"
                    :is-selected="false"
                >
                    <div class="container mt-[60px] max-1180:px-5">
                        <p class="text-lg text-zinc-500 max-1180:text-sm">
                            {!! $product->description !!}
                        </p>
                    </div>
                </x-shop::tabs.item>

                {!! view_render_event('bagisto.shop.products.view.description.after', ['product' => $product]) !!}

                <!-- Reviews Tab -->
                <x-shop::tabs.item
                    id="review-tab"
                    class="container mt-[60px] !p-0"
                    :title="trans('shop::app.products.view.review')"
                    :is-selected="false"
                >
                    @include('shop::products.view.reviews')
                </x-shop::tabs.item>
            </x-shop::tabs>
        </div>
    </div>

    <!-- Information Section -->
    <div class="container mt-6 grid gap-3 !p-0 max-1180:px-5 1180:hidden">
        <!-- Description Accordion -->
        <x-shop::accordion
            class="max-md:border-none"
            :is-active="true"
        >
            <x-slot:header class="bg-gray-100 max-md:!py-3 max-sm:!py-2">
                <p class="text-base font-medium 1180:hidden">
                    @lang('shop::app.products.view.description')
                </p>
            </x-slot>

            <x-slot:content class="max-sm:px-0">
                <div class="mb-5 text-lg text-zinc-500 max-1180:text-sm max-md:mb-1 max-md:px-4">
                    {!! $product->description !!}
                </div>
            </x-slot>
        </x-shop::accordion>

        <!-- Reviews Accordion -->
        <x-shop::accordion
            class="max-md:border-none"
            :is-active="false"
        >
            <x-slot:header
                class="bg-gray-100 max-md:!py-3 max-sm:!py-2"
                id="review-accordian-button"
            >
                <p class="text-base font-medium">
                    @lang('shop::app.products.view.review')
                </p>
            </x-slot>

            <x-slot:content>
                @include('shop::products.view.reviews')
            </x-slot>
        </x-shop::accordion>
    </div>

    <!-- Featured Products -->
    <x-shop::products.carousel
        :title="trans('shop::app.products.view.related-product-title')"
        :src="route('shop.api.products.related.index', ['id' => $product->id])"
    />

    <!-- Up-sell Products -->
    <x-shop::products.carousel
        :title="trans('shop::app.products.view.up-sell-title')"
        :src="route('shop.api.products.up-sell.index', ['id' => $product->id])"
    />

    {!! view_render_event('bagisto.shop.products.view.after', ['product' => $product]) !!}

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-product-template"
        >
            <x-shop::form
                v-slot="{ meta, errors, handleSubmit }"
                as="div"
            >
                <form
                    ref="formData"
                    @submit="handleSubmit($event, addToCart)"
                >
                    <input
                        type="hidden"
                        name="product_id"
                        value="{{ $product->id }}"
                    >

                    <input
                        type="hidden"
                        name="is_buy_now"
                        v-model="is_buy_now"
                    >

                    <div class="container px-[60px] max-1180:px-0">
                        <div class="mt-12 flex gap-9 max-1180:flex-wrap max-lg:mt-0 max-sm:gap-y-4">
                            <!-- Gallery Blade Inclusion -->
                            @include('shop::products.view.gallery')

                            <!-- Details -->
                            <div class="relative max-w-[590px] max-1180:w-full max-1180:max-w-full max-1180:px-5 max-sm:px-4">
                                {!! view_render_event('bagisto.shop.products.name.before', ['product' => $product]) !!}

                                <div class="flex justify-between gap-4">
                                    <h1 class="break-words text-3xl font-medium max-sm:text-xl">
                                        {{ $product->name }}
                                    </h1>

                                    @if (core()->getConfigData('customer.settings.wishlist.wishlist_option'))
                                        <div
                                            class="flex max-h-[46px] min-h-[46px] min-w-[46px] cursor-pointer items-center justify-center rounded-full border bg-white text-2xl transition-all hover:opacity-[0.8] max-sm:max-h-7 max-sm:min-h-7 max-sm:min-w-7 max-sm:text-base"
                                            role="button"
                                            aria-label="@lang('shop::app.products.view.add-to-wishlist')"
                                            tabindex="0"
                                            :class="isWishlist ? 'icon-heart-fill text-red-600' : 'icon-heart'"
                                            @click="addToWishlist"
                                        >
                                        </div>
                                    @endif
                                </div>

                                {!! view_render_event('bagisto.shop.products.name.after', ['product' => $product]) !!}

                                <!-- Stock Status Indicator -->
                                <div class="mt-2 flex items-center">
                                    <span class="inline-block h-3 w-3 rounded-full mr-2" 
                                          :class="{'bg-green-500': isInStock, 'bg-red-500': !isInStock}"></span>
                                    <span class="text-sm font-medium" v-text="isInStock ? 'In Stock' : 'Out of Stock'"></span>
                                </div>

                                <!-- Rating -->
                                {!! view_render_event('bagisto.shop.products.rating.before', ['product' => $product]) !!}

                                @if ($totalRatings = $reviewHelper->getTotalFeedback($product))
                                    <!-- Scroll To Reviews Section and Activate Reviews Tab -->
                                    <div
                                        class="mt-1 w-max cursor-pointer max-sm:mt-1.5"
                                        role="button"
                                        tabindex="0"
                                        @click="scrollToReview"
                                    >
                                        <x-shop::products.ratings
                                            class="transition-all hover:border-gray-400 max-sm:px-3 max-sm:py-1"
                                            :average="$avgRatings"
                                            :total="$totalRatings"
                                            ::rating="true"
                                        />
                                    </div>
                                @endif

                                {!! view_render_event('bagisto.shop.products.rating.after', ['product' => $product]) !!}

                                <!-- Pricing -->
                                {!! view_render_event('bagisto.shop.products.price.before', ['product' => $product]) !!}

                                <p class="mt-[22px] flex items-center gap-2.5 text-2xl !font-medium max-sm:mt-2 max-sm:gap-x-2.5 max-sm:gap-y-0 max-sm:text-lg">
                                    {!! $product->getTypeInstance()->getPriceHtml() !!}
                                </p>

                                @if (\Webkul\Tax\Facades\Tax::isInclusiveTaxProductPrices())
                                    <span class="text-sm font-normal text-zinc-500 max-sm:text-xs">
                                        (@lang('shop::app.products.view.tax-inclusive'))
                                    </span>
                                @endif

                                @if (count($product->getTypeInstance()->getCustomerGroupPricingOffers()))
                                    <div class="mt-2.5 grid gap-1.5">
                                        @foreach ($product->getTypeInstance()->getCustomerGroupPricingOffers() as $offer)
                                            <p class="text-zinc-500 [&>*]:text-black">
                                                {!! $offer !!}
                                            </p>
                                        @endforeach
                                    </div>
                                @endif

                                {!! view_render_event('bagisto.shop.products.price.after', ['product' => $product]) !!}

                                {!! view_render_event('bagisto.shop.products.short_description.before', ['product' => $product]) !!}

                                <p class="mt-6 text-lg text-zinc-500 max-sm:mt-1.5 max-sm:text-sm">
                                    {!! $product->short_description !!}
                                </p>

                                <!-- Pincode Availability Checker -->
                                <div class="mt-8 p-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-100 shadow-sm">
                                    <div class="flex items-start gap-3 mb-4">
                                        <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold text-gray-900 mb-1">Check Delivery Availability</h3>
                                            <p class="text-sm text-gray-600">Enter your pincode to check if we deliver to your area</p>
                                        </div>
                                    </div>

                                    <div class="space-y-4">
                                        <!-- Pincode Input Section -->
                                        <div class="flex gap-3">
                                            <div class="flex-1 relative">
                                                <input 
                                                    type="text" 
                                                    v-model="pincode" 
                                                    placeholder="Enter 6-digit pincode (e.g. 560001)" 
                                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 placeholder-gray-400"
                                                    @input="validatePincode"
                                                    @keypress.enter="checkPincode"
                                                    maxlength="6"
                                                >
                                            </div>
                                            <button 
                                                type="button" 
                                                @click="checkPincode" 
                                                class="px-6 py-3 bg-blue-600 text-black font-medium rounded-lg border-2 border-blue-700 hover:bg-blue-700 hover:border-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-blue-600 disabled:hover:border-blue-700"
                                                :disabled="pincode.length !== 6 || isCheckingPincode"
                                            >
                                                <span v-if="!isCheckingPincode" class="flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                                    </svg>
                                                    Check
                                                </span>
                                                <span v-else class="flex items-center gap-2">
                                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                    Checking...
                                                </span>
                                            </button>
                                        </div>

                                        <!-- Results Section -->
                                        <div v-if="pincodeMessage" class="transition-all duration-300 ease-in-out">
                                            <!-- Success Message -->
                                            <div v-if="pincodeAvailable" class="p-4 bg-green-50 border border-green-200 rounded-lg">
                                                <div class="flex items-start gap-3">
                                                    <div class="flex-shrink-0 w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    </div>
                                                    <div class="flex-1">
                                                        <h4 class="text-sm font-semibold text-green-800 mb-1">Great! We deliver to your area</h4>
                                                        <p class="text-sm text-green-700">@{{ pincodeMessage }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Error Message -->
                                            <div v-else class="p-4 bg-red-50 border border-red-200 rounded-lg">
                                                <div class="flex items-start gap-3">
                                                    <div class="flex-shrink-0 w-6 h-6 bg-red-100 rounded-full flex items-center justify-center">
                                                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                        </svg>
                                                    </div>
                                                    <div class="flex-1">
                                                        <h4 class="text-sm font-semibold text-red-800 mb-1">Sorry, we don't deliver here yet</h4>
                                                        <p class="text-sm text-red-700">@{{ pincodeMessage }}</p>
                                                        <p class="text-sm text-red-600 mt-2">We're expanding our delivery network. Check back soon!</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="w-full mx-auto pr-[90px] mt-[30px] max-[1180px]:px-5">
                                    <div class="grid grid-cols-3 gap-x-8 mt-4 max-w-max">
                                        @foreach ($customAttributeValues as $customAttributeValue)
                                            @if (!empty($customAttributeValue['value']) && in_array(strtolower($customAttributeValue['label']), ['purity', 'gross weight', 'net weight']))
                                                <div class="space-y-1">
                                                    <p class="text-base font-medium text-black">
                                                        {!! $customAttributeValue['label'] !!}
                                                    </p>

                                                    @if ($customAttributeValue['type'] == 'file')
                                                        <a
                                                            href="{{ Storage::url($product[$customAttributeValue['code']]) }}"
                                                            download="{{ $customAttributeValue['label'] }}"
                                                        >
                                                            <span class="icon-download text-2xl text-zinc-500"></span>
                                                        </a>
                                                    @elseif ($customAttributeValue['type'] == 'image')
                                                        <a
                                                            href="{{ Storage::url($product[$customAttributeValue['code']]) }}"
                                                            download="{{ $customAttributeValue['label'] }}"
                                                        >
                                                            <img
                                                                class="h-5 min-h-5 w-5 min-w-5"
                                                                src="{{ Storage::url($customAttributeValue['value']) }}"
                                                            />
                                                        </a>
                                                    @else
                                                        <p class="text-base text-zinc-500">
                                                            {!! formatAttributeValue($customAttributeValue) !!}
                                                        </p>
                                                    @endif
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>

                                {!! view_render_event('bagisto.shop.products.short_description.after', ['product' => $product]) !!}

                                @include('shop::products.view.types.simple')

                                @include('shop::products.view.types.configurable')

                                @include('shop::products.view.types.grouped')

                                @include('shop::products.view.types.bundle')

                                @include('shop::products.view.types.downloadable')

                                @include('shop::products.view.types.booking')

                                <!-- Product Actions and Quantity Box -->
                                <div class="mt-8 flex max-w-[470px] gap-4 max-sm:mt-4">

                                    {!! view_render_event('bagisto.shop.products.view.quantity.before', ['product' => $product]) !!}

                                    @if ($product->getTypeInstance()->showQuantityBox())
                                        <x-shop::quantity-changer
                                            name="quantity"
                                            value="1"
                                            class="gap-x-4 rounded-xl px-7 py-4 max-md:py-3 max-sm:gap-x-5 max-sm:rounded-lg max-sm:px-4 max-sm:py-1.5"
                                        />
                                    @endif

                                    {!! view_render_event('bagisto.shop.products.view.quantity.after', ['product' => $product]) !!}

                                    @if (core()->getConfigData('sales.checkout.shopping_cart.cart_page'))
                                        <!-- Add To Cart Button -->
                                        {!! view_render_event('bagisto.shop.products.view.add_to_cart.before', ['product' => $product]) !!}

                                        <x-shop::button
                                            type="submit"
                                            class="secondary-button w-full max-w-full max-md:py-3 max-sm:rounded-lg max-sm:py-1.5"
                                            button-type="secondary-button"
                                            :loading="false"
                                            :title="trans('shop::app.products.view.add-to-cart')"
                                            :disabled="! $product->isSaleable(1)"
                                            ::loading="isStoring.addToCart"
                                            ::disabled="isStoring.addToCart || !isInStock"
                                            @click="is_buy_now=0;"
                                        />

                                        {!! view_render_event('bagisto.shop.products.view.add_to_cart.after', ['product' => $product]) !!}
                                    @endif
                                </div>

                                <!-- Buy Now Button -->
                                @if (core()->getConfigData('sales.checkout.shopping_cart.cart_page'))
                                    {!! view_render_event('bagisto.shop.products.view.buy_now.before', ['product' => $product]) !!}

                                    @if (core()->getConfigData('catalog.products.storefront.buy_now_button_display'))
                                        <x-shop::button
                                            type="submit"
                                            class="primary-button mt-5 w-full max-w-[470px] max-md:py-3 max-sm:mt-3 max-sm:rounded-lg max-sm:py-1.5"
                                            button-type="primary-button"
                                            :title="trans('shop::app.products.view.buy-now')"
                                            :disabled="! $product->isSaleable(1)"
                                            ::loading="isStoring.buyNow"
                                            @click="is_buy_now=1;"
                                            ::disabled="isStoring.buyNow || !isInStock"
                                        />
                                    @endif

                                    {!! view_render_event('bagisto.shop.products.view.buy_now.after', ['product' => $product]) !!}
                                @endif

                                {!! view_render_event('bagisto.shop.products.view.additional_actions.before', ['product' => $product]) !!}

                                <!-- Share Buttons -->
                                <div class="mt-10 flex gap-9 max-md:mt-4 max-md:flex-wrap max-sm:justify-center max-sm:gap-3">
                                    {!! view_render_event('bagisto.shop.products.view.compare.before', ['product' => $product]) !!}

                                    <div
                                        class="flex cursor-pointer items-center justify-center gap-2.5 max-sm:gap-1.5 max-sm:text-base"
                                        role="button"
                                        tabindex="0"
                                        @click="is_buy_now=0; addToCompare({{ $product->id }})"
                                    >
                                        @if (core()->getConfigData('catalog.products.settings.compare_option'))
                                            <span
                                                class="icon-compare text-2xl"
                                                role="presentation"
                                            ></span>

                                            @lang('shop::app.products.view.compare')
                                        @endif
                                    </div>

                                    {!! view_render_event('bagisto.shop.products.view.compare.after', ['product' => $product]) !!}
                                </div>

                                {!! view_render_event('bagisto.shop.products.view.additional_actions.after', ['product' => $product]) !!}
                            </div>
                        </div>
                    </div>
                </form>
            </x-shop::form>
        </script>

        <script type="module">
            app.component('v-product', {
                template: '#v-product-template',

                data() {
                    return {
                        isWishlist: Boolean("{{ (boolean) auth()->guard()->user()?->wishlist_items->where('channel_id', core()->getCurrentChannel()->id)->where('product_id', $product->id)->count() }}"),

                        isCustomer: '{{ auth()->guard('customer')->check() }}',

                        is_buy_now: 0,

                        isStoring: {
                            addToCart: false,
                            buyNow: false,
                        },
                        
                        isInStock: {{ $product->isSaleable(1) ? 'true' : 'false' }},
                        pincode: '',
                        pincodeMessage: '',
                        pincodeAvailable: false,
                        isCheckingPincode: false,
                    }
                },

                methods: {
                    addToCart(params) {
                        const operation = this.is_buy_now ? 'buyNow' : 'addToCart';

                        this.isStoring[operation] = true;

                        let formData = new FormData(this.$refs.formData);

                        this.ensureQuantity(formData);

                        this.$axios.post('{{ route("shop.api.checkout.cart.store") }}', formData, {
                                headers: {
                                    'Content-Type': 'multipart/form-data'
                                }
                            })
                            .then(response => {
                                if (response.data.message) {
                                    this.$emitter.emit('update-mini-cart', response.data.data);

                                    this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                                    if (response.data.redirect) {
                                        window.location.href= response.data.redirect;
                                    }
                                } else {
                                    this.$emitter.emit('add-flash', { type: 'warning', message: response.data.data.message });
                                }

                                this.isStoring[operation] = false;
                            })
                            .catch(error => {
                                this.isStoring[operation] = false;

                                this.$emitter.emit('add-flash', { type: 'warning', message: error.response.data.message });
                            });
                    },

                    addToWishlist() {
                        if (this.isCustomer) {
                            this.$axios.post('{{ route('shop.api.customers.account.wishlist.store') }}', {
                                    product_id: "{{ $product->id }}"
                                })
                                .then(response => {
                                    this.isWishlist = ! this.isWishlist;

                                    this.$emitter.emit('add-flash', { type: 'success', message: response.data.data.message });
                                })
                                .catch(error => {});
                        } else {
                            window.location.href = "{{ route('shop.customer.session.index')}}";
                        }
                    },

                    validatePincode() {
                        // Remove any non-digit characters
                        this.pincode = this.pincode.replace(/\D/g, '');
                        
                        // Truncate to 6 digits if longer
                        if (this.pincode.length > 6) {
                            this.pincode = this.pincode.substring(0, 6);
                        }
                        
                        // Clear message when user is typing
                        if (this.pincode.length !== 6) {
                            this.pincodeMessage = '';
                            this.pincodeAvailable = false;
                        }
                    },
                    
                    checkPincode() {
                        if (this.pincode.length !== 6) {
                            this.pincodeMessage = 'Please enter a valid 6-digit pincode';
                            this.pincodeAvailable = false;
                            return;
                        }

                    this.isCheckingPincode = true;
                    this.pincodeMessage = '';
        
                        // Simulate API call with realistic delay
                        setTimeout(() => {
                            // Extended list of available pincodes for demo
                            const availablePincodes = [
                                '560001', '560002', '560003', '560004', '560005', // Bangalore
                                '110001', '110002', '110003', '110004', '110005', // Delhi
                                '400001', '400002', '400003', '400004', '400005', // Mumbai
                                '600001', '600002', '600003', '600004', '600005', // Chennai
                                '700001', '700002', '700003', '700004', '700005', // Kolkata
                                '380001', '380002', '380003', '380004', '380005', // Ahmedabad
                            ];
                            
                            if (availablePincodes.includes(this.pincode)) {
                                this.pincodeMessage = `Delivery available to ${this.pincode}`;
                                this.pincodeAvailable = true;
                            } else {
                                this.pincodeMessage = `Sorry, delivery not available to ${this.pincode}`;
                                this.pincodeAvailable = false;
                            }
                            this.isCheckingPincode = false;
                        }, 1500);
                    },

                    // Method to reset pincode checker
                    resetPincodeChecker() {
                        this.pincode = '';
                        this.pincodeMessage = '';
                        this.pincodeAvailable = false;
                        this.isCheckingPincode = false;
                    },

                    addToCompare(productId) {
                        /**
                         * This will handle for customers.
                         */
                        if (this.isCustomer) {
                            this.$axios.post('{{ route("shop.api.compare.store") }}', {
                                    'product_id': productId
                                })
                                .then(response => {
                                    this.$emitter.emit('add-flash', { type: 'success', message: response.data.data.message });
                                })
                                .catch(error => {
                                    if ([400, 422].includes(error.response.status)) {
                                        this.$emitter.emit('add-flash', { type: 'warning', message: error.response.data.data.message });

                                        return;
                                    }

                                    this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message});
                                });

                            return;
                        }

                        /**
                         * This will handle for guests.
                         */
                        let existingItems = this.getStorageValue(this.getCompareItemsStorageKey()) ?? [];

                        if (existingItems.length) {
                            if (! existingItems.includes(productId)) {
                                existingItems.push(productId);

                                this.setStorageValue(this.getCompareItemsStorageKey(), existingItems);

                                this.$emitter.emit('add-flash', { type: 'success', message: "@lang('shop::app.products.view.add-to-compare')" });
                            } else {
                                this.$emitter.emit('add-flash', { type: 'warning', message: "@lang('shop::app.products.view.already-in-compare')" });
                            }
                        } else {
                            this.setStorageValue(this.getCompareItemsStorageKey(), [productId]);

                            this.$emitter.emit('add-flash', { type: 'success', message: "@lang('shop::app.products.view.add-to-compare')" });
                        }
                    },

                    updateQty(quantity, id) {
                        this.isLoading = true;

                        let qty = {};

                        qty[id] = quantity;

                        this.$axios.put('{{ route('shop.api.checkout.cart.update') }}', { qty })
                            .then(response => {
                                if (response.data.message) {
                                    this.cart = response.data.data;
                                } else {
                                    this.$emitter.emit('add-flash', { type: 'warning', message: response.data.data.message });
                                }

                                this.isLoading = false;
                            }).catch(error => this.isLoading = false);
                    },

                    getCompareItemsStorageKey() {
                        return 'compare_items';
                    },

                    setStorageValue(key, value) {
                        localStorage.setItem(key, JSON.stringify(value));
                    },

                    getStorageValue(key) {
                        let value = localStorage.getItem(key);

                        if (value) {
                            value = JSON.parse(value);
                        }

                        return value;
                    },

                    scrollToReview() {
                        let accordionElement = document.querySelector('#review-accordian-button');

                        if (accordionElement) {
                            accordionElement.click();

                            accordionElement.scrollIntoView({
                                behavior: 'smooth'
                            });
                        }

                        let tabElement = document.querySelector('#review-tab-button');

                        if (tabElement) {
                            tabElement.click();

                            tabElement.scrollIntoView({
                                behavior: 'smooth'
                            });
                        }
                    },

                    ensureQuantity(formData) {
                        if (! formData.has('quantity')) {
                            formData.append('quantity', 1);
                        }
                    },
                },
            });
        </script>
    @endPushOnce

    @push('styles')
        <style>
            /* Custom styling for the product page */
            .product-title {
                font-size: 2rem;
                font-weight: 600;
                color: #1a1a1a;
                margin-bottom: 0.5rem;
            }
            
            .product-price {
                font-size: 1.8rem;
                font-weight: 700;
                color: #b8860b; /* Gold color for jewelry */
                margin: 1rem 0;
            }
            
            .product-attributes {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                gap: 1.5rem;
                margin: 2rem 0;
                padding: 1.5rem;
                background: #f9f9f9;
                border-radius: 8px;
            }
            
            .attribute-label {
                font-weight: 600;
                color: #555;
                margin-bottom: 0.25rem;
            }
            
            .attribute-value {
                font-weight: 500;
                color: #222;
            }
            
            .pincode-checker {
                margin: 2rem 0;
                padding: 1.5rem;
                background: #f5f5f5;
                border-radius: 8px;
            }
            
            .stock-status {
                display: inline-flex;
                align-items: center;
                padding: 0.25rem 0.5rem;
                border-radius: 4px;
                font-weight: 500;
                margin-bottom: 1rem;
            }
            
            .in-stock {
                background-color: #e6f7ee;
                color: #10b981;
            }
            
            .out-of-stock {
                background-color: #fee2e2;
                color: #ef4444;
            }
            
            @media (max-width: 768px) {
                .product-title {
                    font-size: 1.5rem;
                }
                
                .product-price {
                    font-size: 1.4rem;
                }
                
                .product-attributes {
                    grid-template-columns: 1fr 1fr;
                }
            }

            /* Styling for Pincode section */
            .animate-bounce {
                    animation: bounce 1s infinite;
                }

                @keyframes bounce {
                    0%, 20%, 53%, 80%, 100% {
                        transform: translate3d(0,0,0);
                    }
                    40%, 43% {
                        transform: translate3d(0, -8px, 0);
                    }
                    70% {
                        transform: translate3d(0, -4px, 0);
                    }
                    90% {
                        transform: translate3d(0, -2px, 0);
                    }
                }

                .animate-pulse {
                    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
                }

                @keyframes pulse {
                    0%, 100% {
                        opacity: 1;
                    }
                    50% {
                        opacity: .5;
                    }
                }

                .pincode-checker-enhanced {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.pincode-input-focus:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    border-color: #3b82f6;
}

.delivery-option-card {
    transition: all 0.2s ease-in-out;
}

.delivery-option-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

@media (max-width: 640px) {
    .quick-pincode-buttons {
        justify-content: center;
    }
}
        </style>
    @endpush
</x-shop::layouts>