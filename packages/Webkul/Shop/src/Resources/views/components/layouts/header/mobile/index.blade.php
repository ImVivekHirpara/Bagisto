<!-- Mobile-only header with enhanced desktop isolation -->
{!! view_render_event('bagisto.shop.components.layouts.header.mobile.top.before') !!}

<!-- Mobile-only rotating banner - Strictly mobile only -->
<div class="block lg:hidden bg-black text-white py-2 px-4 text-center text-sm font-medium">
    Free Shipping
</div>

{!! view_render_event('bagisto.shop.components.layouts.header.mobile.top.after') !!}

<!-- Mobile header container - Hidden on desktop -->
<div class="flex flex-wrap gap-4 px-4 pb-4 pt-2 shadow-sm lg:hidden xl:hidden">
    <div class="flex w-full items-center justify-between">
        <!-- Left Navigation -->
        <div class="flex items-center gap-x-4">
            {!! view_render_event('bagisto.shop.components.layouts.header.mobile.drawer.before') !!}

            <!-- Mobile Drawer -->
            <v-mobile-drawer></v-mobile-drawer>

            {!! view_render_event('bagisto.shop.components.layouts.header.mobile.drawer.after') !!}

            <!-- Mobile Search Toggle -->
            <span 
                class="icon-search cursor-pointer text-2xl hover:text-gray-600 transition-colors"
                @click="toggleSearchDrawer"
            ></span>
        </div>

        <!-- Center Logo -->
        <div class="flex items-center">
            <a
                href="{{ route('shop.home.index') }}"
                class="max-h-[30px] block lg:hidden"
                aria-label="@lang('shop::app.components.layouts.header.mobile.bagisto')"
            >
                <img
                    src="{{ core()->getCurrentChannel()->logo_url ?? bagisto_asset('images/logo.svg') }}"
                    alt="{{ config('app.name') }}"
                    width="131"
                    height="29"
                    class="max-h-[30px] w-auto"
                >
            </a>
        </div>

        <!-- Right Navigation - Mobile only -->
        <div class="block lg:hidden">
            <div class="flex items-center gap-x-5 max-md:gap-x-4">
                {!! view_render_event('bagisto.shop.components.layouts.header.mobile.wishlist.before') !!}

                <!-- Mobile Wishlist Link -->
                @if (core()->getConfigData('customer.settings.wishlist.wishlist_option'))
                    <a 
                        href="{{ route('shop.customers.account.wishlist.index') }}"
                        class="icon-heart cursor-pointer text-2xl hover:text-gray-600 transition-colors"
                        aria-label="@lang('shop::app.components.layouts.header.mobile.wishlist')"
                    ></a>
                @endif

                {!! view_render_event('bagisto.shop.components.layouts.header.mobile.wishlist.after') !!}

                {!! view_render_event('bagisto.shop.components.layouts.header.mobile.mini_cart.before') !!}

                <!-- Mobile Mini Cart -->
                <v-mobile-mini-cart></v-mobile-mini-cart>

                {!! view_render_event('bagisto.shop.components.layouts.header.mobile.mini_cart.after') !!}

                 <!-- Mobile Profile Link -->
                <a
                    href="{{ auth()->guard('customer')->check() ? route('shop.customers.account.profile.index') : route('shop.customer.session.create') }}"
                    class="icon-users cursor-pointer text-2xl hover:text-gray-600 transition-colors"
                    aria-label="@lang('shop::app.components.layouts.header.mobile.account')"
                ></a>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Search Drawer -->
<x-shop::drawer
    position="left"
    width="100%"
    ref="searchDrawer"
    class="z-[1000]"
>
    <x-slot:toggle>
        <!-- Toggle is handled by the search icon click -->
    </x-slot>

    <x-slot:header>
        <div class="flex items-center justify-between p-4">
            <p class="text-lg font-semibold">
                @lang('shop::app.components.layouts.header.mobile.search')
            </p>
            <span 
                class="icon-cancel text-2xl cursor-pointer"
                @click="closeSearchDrawer"
            ></span>
        </div>
    </x-slot>

    <x-slot:content class="!p-4">
        <!-- Mobile Search Form -->
        <form 
            action="{{ route('shop.search.index') }}" 
            class="flex w-full items-center"
            @submit.prevent="performSearch"
        >
            <label
                for="mobile-organic-search"
                class="sr-only"
            >
                @lang('shop::app.components.layouts.header.mobile.search')
            </label>

            <div class="relative w-full">
                <div class="icon-search pointer-events-none absolute top-3 flex items-center text-2xl max-md:text-xl max-sm:top-2.5 ltr:left-3 rtl:right-3 text-gray-500"></div>

                <input
                    id="mobile-organic-search"
                    type="text"
                    class="block w-full rounded-xl border border-gray-300 px-11 py-3.5 text-sm font-medium text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all max-md:rounded-lg max-md:px-10 max-md:py-3 max-md:font-normal max-sm:text-xs"
                    name="query"
                    v-model="searchQuery"
                    placeholder="@lang('shop::app.components.layouts.header.mobile.search-text')"
                    required
                    @input="updateSearchQuery"
                >

                <div v-if="showImageSearch" class="lg:hidden">
                    @include('shop::search.images.index')
                </div>
            </div>
        </form>
    </x-slot>
</x-shop::drawer>

@pushOnce('scripts')
    <!-- Mobile Mini Cart Component -->
    <script type="text/x-template" id="v-mobile-mini-cart-template">
        <div v-if="showCart" class="lg:hidden">
            @include('shop::checkout.cart.mini-cart')
        </div>
    </script>

    <script type="module">
        // Mobile Mini Cart Component
        app.component('v-mobile-mini-cart', {
            template: '#v-mobile-mini-cart-template',

            data() {
                return {
                    showCart: @json((bool) core()->getConfigData('sales.checkout.shopping_cart.cart_page'))
                };  
            }
        });

        // Mobile Search Functionality
        document.addEventListener('alpine:init', () => {
            Alpine.data('mobileHeader', () => ({
                searchQuery: @json(request('query') ?? ''),
                showImageSearch: @json((bool) core()->getConfigData('catalog.products.settings.image_search')),

                init() {
                    // Initialize any required functionality
                },

                toggleSearchDrawer() {
                    this.$refs.searchDrawer.toggle();
                },

                closeSearchDrawer() {
                    this.$refs.searchDrawer.close();
                },

                updateSearchQuery(event) {
                    this.searchQuery = event.target.value;
                },

                performSearch() {
                    if (this.searchQuery.trim()) {
                        window.location.href = `{{ route('shop.search.index') }}?query=${encodeURIComponent(this.searchQuery)}`;
                    }
                }
            }));
        });
    </script>

    <!-- Mobile Drawer Component -->
    <script type="text/x-template" id="v-mobile-drawer-template">
        <div class="lg:hidden">
            <x-shop::drawer
                position="left"
                width="100%"
                @close="onDrawerClose"
            >
                <x-slot:toggle>
                    <span class="icon-hamburger cursor-pointer text-2xl hover:text-gray-600 transition-colors"></span>
                </x-slot>

                <x-slot:header>
                    <div class="flex items-center justify-between">
                        <a href="{{ route('shop.home.index') }}">
                            <img
                                src="{{ core()->getCurrentChannel()->logo_url ?? bagisto_asset('images/logo.svg') }}"
                                alt="{{ config('app.name') }}"
                                width="131"
                                height="29"
                                class="max-h-[29px] w-auto"
                            >
                        </a>
                    </div>
                </x-slot>

                <x-slot:content class="!p-0">
                    <!-- Account Profile Hero Section -->
                    <div class="border-b border-zinc-200 p-4">
                        <div class="grid grid-cols-[auto_1fr] items-center gap-4 rounded-xl border border-zinc-200 p-2.5">
                            <div>
                                <img
                                    src="{{ auth()->user()?->image_url ??  bagisto_asset('images/user-placeholder.png') }}"
                                    class="h-[60px] w-[60px] rounded-full object-cover"
                                    alt="User Avatar"
                                >
                            </div>

                            @guest('customer')
                                <a
                                    href="{{ route('shop.customer.session.create') }}"
                                    class="flex text-base font-medium hover:text-blue-600 transition-colors"
                                >
                                    @lang('shop::app.components.layouts.header.mobile.login')

                                    <i class="icon-double-arrow text-2xl ltr:ml-2.5 rtl:mr-2.5"></i>
                                </a>
                            @endguest

                            @auth('customer')
                                <div class="flex flex-col justify-between gap-2.5 max-md:gap-0">
                                    <p class="font-medium break-all text-2xl max-md:text-xl">Hello! {{ auth()->user()?->first_name }}</p>

                                    <p class="text-zinc-500 no-underline max-md:text-sm break-all">{{ auth()->user()?->email }}</p>
                                </div>
                            @endauth
                        </div>
                    </div>

                    {!! view_render_event('bagisto.shop.components.layouts.header.mobile.drawer.categories.before') !!}

                    <!-- Mobile category view -->
                    <v-mobile-category ref="mobileCategory"></v-mobile-category>

                    {!! view_render_event('bagisto.shop.components.layouts.header.mobile.drawer.categories.after') !!}
                </x-slot>

                <x-slot:footer>
                    <!-- Localization & Currency Section -->
                    @if(core()->getCurrentChannel()->locales()->count() > 1 || core()->getCurrentChannel()->currencies()->count() > 1 )
                        <div class="fixed bottom-0 z-10 grid w-full max-w-full grid-cols-[1fr_auto_1fr] items-center justify-items-center border-t border-zinc-200 bg-white px-5 ltr:left-0 rtl:right-0">
                            <!-- Currency Drawer -->
                            <x-shop::drawer
                                position="bottom"
                                width="100%"
                            >
                                <!-- Drawer Toggler -->
                                <x-slot:toggle>
                                    <div
                                        class="flex cursor-pointer items-center gap-x-2.5 px-2.5 py-3.5 text-lg font-medium uppercase max-md:py-3 max-sm:text-base hover:text-blue-600 transition-colors"
                                        role="button"
                                    >
                                        {{ core()->getCurrentCurrency()->symbol . ' ' . core()->getCurrentCurrencyCode() }}
                                    </div>
                                </x-slot>

                                <!-- Drawer Header -->
                                <x-slot:header>
                                    <div class="flex items-center justify-between">
                                        <p class="text-lg font-semibold">
                                            @lang('shop::app.components.layouts.header.mobile.currencies')
                                        </p>
                                    </div>
                                </x-slot>

                                <!-- Drawer Content -->
                                <x-slot:content class="!px-0">
                                    <div
                                        class="overflow-auto"
                                        :style="{ height: getCurrentScreenHeight }"
                                    >
                                        <v-currency-switcher></v-currency-switcher>
                                    </div>
                                </x-slot>
                            </x-shop::drawer>

                            <!-- Seperator -->
                            <span class="h-5 w-0.5 bg-zinc-200"></span>

                            <!-- Locale Drawer -->
                            <x-shop::drawer
                                position="bottom"
                                width="100%"
                            >
                                <!-- Drawer Toggler -->
                                <x-slot:toggle>
                                    <div
                                        class="flex cursor-pointer items-center gap-x-2.5 px-2.5 py-3.5 text-lg font-medium uppercase max-md:py-3 max-sm:text-base hover:text-blue-600 transition-colors"
                                        role="button"
                                    >
                                        <img
                                            src="{{ ! empty(core()->getCurrentLocale()->logo_url)
                                                    ? core()->getCurrentLocale()->logo_url
                                                    : bagisto_asset('images/default-language.svg')
                                                }}"
                                            class="h-4 w-6 object-cover"
                                            alt="Default locale"
                                            width="24"
                                            height="16"
                                        />

                                        {{ core()->getCurrentChannel()->locales()->orderBy('name')->where('code', app()->getLocale())->value('name') }}
                                    </div>
                                </x-slot>

                                <!-- Drawer Header -->
                                <x-slot:header>
                                    <div class="flex items-center justify-between">
                                        <p class="text-lg font-semibold">
                                            @lang('shop::app.components.layouts.header.mobile.locales')
                                        </p>
                                    </div>
                                </x-slot>

                                <!-- Drawer Content -->
                                <x-slot:content class="!px-0">
                                    <div
                                        class="overflow-auto"
                                        :style="{ height: getCurrentScreenHeight }"
                                    >
                                        <v-locale-switcher></v-locale-switcher>
                                    </div>
                                </x-slot>
                            </x-shop::drawer>
                        </div>
                    @endif
                </x-slot>
            </x-shop::drawer>
        </div>
    </script>

    <!-- Mobile Category Component -->
    <script
        type="text/x-template"
        id="v-mobile-category-template"
    >
        <!-- Wrapper with transition effects -->
        <div class="relative h-full overflow-hidden">
            <!-- Sliding container -->
            <div
                class="flex h-full transition-transform duration-300 ease-in-out"
                :class="{
                    'ltr:translate-x-0 rtl:translate-x-0': currentViewLevel !== 'third',
                    'ltr:-translate-x-full rtl:translate-x-full': currentViewLevel === 'third'
                }"
            >
                <!-- First level view -->
                <div class="h-full w-full flex-shrink-0 overflow-auto px-6">
                    <div class="py-4">
                        <div
                            v-for="category in categories"
                            :key="category.id"
                            :class="{'mb-2': category.children && category.children.length}"
                        >
                            <div class="flex cursor-pointer items-center justify-between py-2 transition-colors duration-200 hover:bg-gray-50 rounded-lg px-2">
                                <a :href="category.url" class="text-base font-medium text-black hover:text-blue-600 transition-colors">
                                    @{{ category.name }}
                                </a>
                            </div>

                            <!-- Second Level Categories -->
                            <div v-if="category.children && category.children.length" class="ml-4">
                                <div
                                    v-for="secondLevelCategory in category.children"
                                    :key="secondLevelCategory.id"
                                >
                                    <div
                                        class="flex cursor-pointer items-center justify-between py-2 px-2 transition-colors duration-200 hover:bg-gray-50 rounded-lg"
                                        @click="showThirdLevel(secondLevelCategory, category, $event)"
                                    >
                                        <a :href="secondLevelCategory.url" class="text-sm font-normal hover:text-blue-600 transition-colors">
                                            @{{ secondLevelCategory.name }}
                                        </a>

                                        <span
                                            v-if="secondLevelCategory.children && secondLevelCategory.children.length"
                                            class="icon-arrow-right rtl:icon-arrow-left text-gray-400"
                                        ></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Third level view -->
                <div
                    class="h-full w-full flex-shrink-0"
                    v-if="currentViewLevel === 'third'"
                >
                    <div class="border-b border-gray-200 px-6 py-4">
                        <button
                            @click="goBackToMainView"
                            class="flex items-center justify-center gap-2 focus:outline-none hover:text-blue-600 transition-colors"
                            aria-label="Go back"
                        >
                            <span class="icon-arrow-left rtl:icon-arrow-right text-lg"></span>
                            <div class="text-base font-medium text-black">
                                @lang('shop::app.components.layouts.header.mobile.back-button')
                            </div>
                        </button>
                    </div>

                    <!-- Third Level Content -->
                    <div class="px-6 py-4">
                        <div
                            v-for="thirdLevelCategory in currentSecondLevelCategory?.children"
                            :key="thirdLevelCategory.id"
                            class="mb-2"
                        >
                            <a
                                :href="thirdLevelCategory.url"
                                class="block py-2 px-2 text-sm transition-colors duration-200 hover:bg-gray-50 rounded-lg hover:text-blue-600"
                            >
                                @{{ thirdLevelCategory.name }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-mobile-category', {
            template: '#v-mobile-category-template',

            data() {
                return  {
                    categories: [],
                    currentViewLevel: 'main',
                    currentSecondLevelCategory: null,
                    currentParentCategory: null
                }
            },

            mounted() {
                this.getCategories();
            },

            computed: {
                getCurrentScreenHeight() {
                    return window.innerHeight - (window.innerWidth < 920 ? 61 : 0) + 'px';
                },
            },

            methods: {
                getCategories() {
                    this.$axios.get("{{ route('shop.api.categories.tree') }}")
                        .then(response => {
                            this.categories = response.data.data;
                        })
                        .catch(error => {
                            console.log(error);
                        });
                },

                showThirdLevel(secondLevelCategory, parentCategory, event) {
                    if (secondLevelCategory.children && secondLevelCategory.children.length) {
                        this.currentSecondLevelCategory = secondLevelCategory;
                        this.currentParentCategory = parentCategory;
                        this.currentViewLevel = 'third';

                        if (event) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                    }
                },

                goBackToMainView() {
                    this.currentViewLevel = 'main';
                }
            },
        });

        app.component('v-mobile-drawer', {
            template: '#v-mobile-drawer-template',

            methods: {
                onDrawerClose() {
                    if (this.$refs.mobileCategory) {
                        this.$refs.mobileCategory.currentViewLevel = 'main';
                    }
                }
            },
        });
    </script>
@endPushOnce