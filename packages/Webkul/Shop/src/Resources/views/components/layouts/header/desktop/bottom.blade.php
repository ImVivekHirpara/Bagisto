

<div id="topbar-message" >
    <topbar-message></topbar-message>
</div>
{!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.before') !!}





<div class="flex min-h-[78px] w-full justify-between border border-b border-l-0 border-r-0 border-t-0 px-[60px] max-1180:px-8 relative">
    <!--
        This section will provide categories for the first, second, and third levels. If
        additional levels are required, users can customize them according to their needs.
    -->
  <!-- Left Search Bar Section -->
    <!-- Left Search Bar Section -->
    <div class="flex items-center flex-1 max-w-[445px]">
        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.search_bar.before') !!}
 
        <!-- Search Bar Container -->
        <div class="relative w-full">
            <form
                action="{{ route('shop.search.index') }}"
                class="flex w-full items-center"
                role="search"
            >
                <label
                    for="organic-search"
                    class="sr-only"
                >
                    @lang('shop::app.components.layouts.header.desktop.bottom.search')
                </label>
 
                <div class="icon-search pointer-events-none absolute top-2.5 flex items-center text-xl ltr:left-3 rtl:right-3"></div>
 
                <input
                    type="text"
                    name="query"
                    value="{{ request('query') }}"
                    class="block w-full rounded-lg border border-transparent bg-zinc-100 px-11 py-3 text-xs font-medium text-gray-900 transition-all hover:border-gray-400 focus:border-gray-400"
                    minlength="{{ core()->getConfigData('catalog.products.search.min_query_length') }}"
                    maxlength="{{ core()->getConfigData('catalog.products.search.max_query_length') }}"
                    placeholder="@lang('shop::app.components.layouts.header.desktop.bottom.search-text')"
                    aria-label="@lang('shop::app.components.layouts.header.desktop.bottom.search-text')"
                    aria-required="true"
                    pattern="[^\\]+"
                    required
                >
 
                <button
                    type="submit"
                    class="hidden"
                    aria-label="@lang('shop::app.components.layouts.header.desktop.bottom.submit')"
                >
                </button>
 
                @if (core()->getConfigData('catalog.products.settings.image_search'))
                    @include('shop::search.images.index')
                @endif
            </form>
        </div>
 
        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.search_bar.after') !!}
    </div>
 
    <!-- Center Logo Section -->
    <div class="absolute left-1/2 top-1/2 transform -translate-x-1/2 -translate-y-1/2 z-10">
        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.logo.before') !!}
 
        <a
            href="{{ route('shop.home.index') }}"
            aria-label="@lang('shop::app.components.layouts.header.desktop.bottom.bagisto')"
        >
            <img
                src="{{ core()->getCurrentChannel()->logo_url ?? bagisto_asset('images/logo.svg') }}"
                width="131"
                height="29"
                alt="{{ config('app.name') }}"
            >
        </a>
 
        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.logo.after') !!}
        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.category.after') !!}
    </div>

    <!-- Right Navigation Section -->
    <div class="flex items-center gap-x-9 max-[1100px]:gap-x-6 max-lg:gap-x-8">
        <!-- Right Navigation Links -->
        <div class="mt-1.5 flex gap-x-8 max-[1100px]:gap-x-6 max-lg:gap-x-8">

            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.compare.before') !!}

            <!-- Compare -->
            <!-- @if(core()->getConfigData('catalog.products.settings.compare_option'))
                <a
                    href="{{ route('shop.compare.index') }}"
                    aria-label="@lang('shop::app.components.layouts.header.desktop.bottom.compare')"
                >
                    <span
                        class="icon-compare inline-block cursor-pointer text-2xl"
                        role="presentation"
                    ></span>
                </a>
            @endif -->

             @if (core()->getConfigData('customer.settings.wishlist.wishlist_option'))
                            <a class="icon-heart inline-block cursor-pointer text-2xl" role="presentation" href="{{ route('shop.customers.account.wishlist.index') }}">

                            </a>
                        @endif

            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.compare.after') !!}

            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.mini_cart.before') !!}

            <!-- Mini cart -->
            @if(core()->getConfigData('sales.checkout.shopping_cart.cart_page'))
                @include('shop::checkout.cart.mini-cart')
            @endif

            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.mini_cart.after') !!}

            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.profile.before') !!}

            <!-- user profile -->
            <x-shop::dropdown position="bottom-{{ core()->getCurrentLocale()->direction === 'ltr' ? 'right' : 'left' }}">
                <x-slot:toggle>
                    <span
                        class="icon-users inline-block cursor-pointer text-2xl"
                        role="button"
                        aria-label="@lang('shop::app.components.layouts.header.desktop.bottom.profile')"
                        tabindex="0"
                    ></span>
                </x-slot>

                <!-- Guest Dropdown -->
                @guest('customer')
                    <x-slot:content>
                        <div class="grid gap-2.5">
                            <p class="font-dmserif text-xl">
                                @lang('shop::app.components.layouts.header.desktop.bottom.welcome-guest')
                            </p>

                            <p class="text-sm">
                                @lang('shop::app.components.layouts.header.desktop.bottom.dropdown-text')
                            </p>
                        </div>

                        <p class="mt-3 w-full border border-zinc-200"></p>

                        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.customers_action.before') !!}

                        <div class="mt-6 flex gap-4">
                            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.sign_in_button.before') !!}

                            <a
                                href="{{ route('shop.customer.session.create') }}"
                                class="primary-button m-0 mx-auto block w-max rounded-2xl px-7 text-center text-base max-md:rounded-lg ltr:ml-0 rtl:mr-0"
                            >
                                @lang('shop::app.components.layouts.header.desktop.bottom.sign-in')
                            </a>

                            <a
                                href="{{ route('shop.customers.register.index') }}"
                                class="secondary-button m-0 mx-auto block w-max rounded-2xl border-2 px-7 text-center text-base max-md:rounded-lg max-md:py-3 ltr:ml-0 rtl:mr-0"
                            >
                                @lang('shop::app.components.layouts.header.desktop.bottom.sign-up')
                            </a>

                            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.sign_up_button.after') !!}
                        </div>

                        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.customers_action.after') !!}
                    </x-slot>
                @endguest

                <!-- Customers Dropdown -->
                @auth('customer')
                    <x-slot:content class="!p-0">
                        <div class="grid gap-2.5 p-5 pb-0">
                            <p class="font-dmserif text-xl">
                                @lang('shop::app.components.layouts.header.desktop.bottom.welcome')'
                                {{ auth()->guard('customer')->user()->first_name }}
                            </p>

                            <p class="text-sm">
                                @lang('shop::app.components.layouts.header.desktop.bottom.dropdown-text')
                            </p>
                        </div>

                        <p class="mt-3 w-full border border-zinc-200"></p>

                        <div class="mt-2.5 grid gap-1 pb-2.5">
                            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.profile_dropdown.links.before') !!}

                            <a
                                class="cursor-pointer px-5 py-2 text-base hover:bg-gray-100"
                                href="{{ route('shop.customers.account.profile.index') }}"
                            >
                                @lang('shop::app.components.layouts.header.desktop.bottom.profile')
                            </a>

                            <a
                                class="cursor-pointer px-5 py-2 text-base hover:bg-gray-100"
                                href="{{ route('shop.customers.account.orders.index') }}"
                            >
                                @lang('shop::app.components.layouts.header.desktop.bottom.orders')
                            </a>

                            @if (core()->getConfigData('customer.settings.wishlist.wishlist_option'))
                                <a
                                    class="cursor-pointer px-5 py-2 text-base hover:bg-gray-100"
                                    href="{{ route('shop.customers.account.wishlist.index') }}"
                                >
                                    @lang('shop::app.components.layouts.header.desktop.bottom.wishlist')
                                </a>
                            @endif

                            <!--Customers logout-->
                            @auth('customer')
                                <x-shop::form
                                    method="DELETE"
                                    action="{{ route('shop.customer.session.destroy') }}"
                                    id="customerLogout"
                                />

                                <a
                                    class="cursor-pointer px-5 py-2 text-base hover:bg-gray-100"
                                    href="{{ route('shop.customer.session.destroy') }}"
                                    onclick="event.preventDefault(); document.getElementById('customerLogout').submit();"
                                >
                                    @lang('shop::app.components.layouts.header.desktop.bottom.logout')
                                </a>
                            @endauth

                            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.profile_dropdown.links.after') !!}
                        </div>
                    </x-slot>
                @endauth
            </x-shop::dropdown>

            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.profile.after') !!}
        </div>
    </div>
</div>


 @php
    use Webkul\Category\Models\Category;

    // Get all root categories except the main "Root" one (which is parent_id = 0)
    $rootCategories = Category::where('parent_id', 1)->where('status', 1)->with('children')->get();
@endphp
@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-desktop-category-template"
    >
        <!-- Loading State -->
        <div
            class="flex items-center gap-5"
            v-if="isLoading"
        >
            <span
                class="shimmer h-6 w-20 rounded"
                role="presentation"
            ></span>

            <span
                class="shimmer h-6 w-20 rounded"
                role="presentation"
            ></span>

            <span
                class="shimmer h-6 w-20 rounded"
                role="presentation"
            ></span>
        </div>

        <!-- Default category layout -->
        <div
            class="flex items-center"
            v-else-if="'{{ core()->getConfigData('general.design.categories.category_view') }}' !== 'sidebar'"
        >
        <!-- SHOP BY TOP LEVEL ITEM -->
<div
    class="group relative flex h-[77px] items-center border-b-4 border-transparent hover:border-b-4 hover:border-navyBlue"
>
    <span>
        <a href="/shop-by" class="inline-block px-5 uppercase">
            Shop By
        </a>
    </span>

    <!-- DROPDOWN -->
    <div
        class="pointer-events-none fixed left-0 right-0 z-[1] w-full bg-white opacity-0 shadow-[0_6px_6px_1px_rgba(0,0,0,.3)] transition duration-300 ease-out group-hover:pointer-events-auto group-hover:opacity-100 group-hover:duration-200 group-hover:ease-in"
        style="top: calc(171px + 2rem);"
    >
        <div class="flex justify-center gap-x-[30px] p-8 m-4">
            <!-- COLUMN 1: SHOP ALL -->
            <div class="w-full min-w-max max-w-[200px] flex-auto pl-10">
                <p class="font-bold text-lg text-navyBlue mb-4 uppercase">
                    SHOP ALL
                </p>
                <ul class="grid grid-cols-1 gap-3">
                    <li><a href="/shop-by/all-gifts" class="text-sm font-medium text-zinc-500">All Gifts</a></li>
                     
                    @foreach ($rootCategories as $root)
                    @foreach ($root->children as $child)
                        <li>
                            <a href="{{ $child->url }}" class="text-sm font-medium text-zinc-500">
                                {{ $child->name }}
                            </a>
                        </li>
                    @endforeach
                @endforeach
                </ul>
            </div>

            <!-- COLUMN 2: FEATURED -->
            <div class="w-full min-w-max max-w-[200px] flex-auto">
                <p class="font-bold text-lg text-navyBlue mb-4 uppercase">
                    FEATURED
                </p>
                <ul class="grid grid-cols-1 gap-3">
                    <li><a href="/shop-by/back-in-stock" class="text-sm font-medium text-zinc-500">Back In Stock</a></li>
                    <li><a href="/shop-by/leaving-soon" class="text-sm font-medium text-zinc-500">Leaving Soon</a></li>
                    <li><a href="/shop-by/edits" class="text-sm font-medium text-zinc-500">Edits</a></li>
                </ul>
            </div>

            <!-- COLUMN 3: COLLECTIONS -->
            <div class="w-full min-w-max max-w-[200px] flex-auto">
                <p class="font-bold text-lg text-navyBlue mb-4 uppercase">
                    COLLECTIONS
                </p>
                <ul class="grid grid-cols-1 gap-3">
                    <li><a href="/shop-by/signature-collection" class="text-sm font-medium text-zinc-500">Signature Collection</a></li>
                    <li><a href="/shop-by/limited-edition" class="text-sm font-medium text-zinc-500">Limited Edition</a></li>
                    <li><a href="/shop-by/seasonal" class="text-sm font-medium text-zinc-500">Seasonal</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

            <div
                class="group relative flex h-[77px] items-center border-b-4 border-transparent hover:border-b-4 hover:border-navyBlue"
            >
                <span>
                    <a href="/new-in" class="inline-block px-5 uppercase">
                        New In
                    </a>
                </span>
            </div>
            <div
                class="group relative flex h-[77px] items-center border-b-4 border-transparent hover:border-b-4 hover:border-navyBlue"
            >
                <span>
                    <a href="/new-in" class="inline-block px-5 uppercase">
                        Best Sellers
                    </a>
                </span>
            </div>

           
            <div
    class="group relative flex h-[77px] items-center border-b-4 border-transparent hover:border-b-4 hover:border-navyBlue"
    v-for="category in categories"
>
 <!-- Static Category: New In -->
            
    <span>
        <a
            :href="category.url"
            class="inline-block px-5 uppercase"
        >
            @{{ category.name }}
        </a>
    </span>


    <div
    class="pointer-events-none fixed left-0 right-0 z-[1] w-full bg-white opacity-0 shadow-[0_6px_6px_1px_rgba(0,0,0,.3)] transition duration-300 ease-out group-hover:pointer-events-auto group-hover:opacity-100 group-hover:duration-200 group-hover:ease-in"
    style="top: calc(171px + 2rem);"
    v-if="category.children && category.children.length"
>
    <div class="p-8 m-4" style="display: grid; grid-template-columns: auto 150px 200px; gap: 50px; justify-content: center;">
        <!-- Shop All Section - Auto width based on content -->
        <div class="pl-10 pr-6">
            <p class="font-bold text-lg text-navyBlue mb-4 uppercase whitespace-nowrap">
                <a :href="category.url">
                    Shop All @{{ category.name }}
                </a>
            </p>
            
            <!-- Categories List -->
            <div class="grid grid-cols-[1fr] content-start gap-2">
                <template v-for="secondLevelCategory in category.children">
                    <p class="text-sm font-medium text-zinc-500 whitespace-nowrap">
                        <a :href="secondLevelCategory.url">
                            @{{ secondLevelCategory.name }}
                        </a>
                    </p>

                    <ul
                        class="grid grid-cols-[1fr] gap-3 mb-4"
                        v-if="secondLevelCategory.children && secondLevelCategory.children.length"
                    >
                        <li
                            class="text-sm font-medium text-zinc-500 whitespace-nowrap"
                            v-for="thirdLevelCategory in secondLevelCategory.children"
                        >
                            <a :href="thirdLevelCategory.url">
                                @{{ thirdLevelCategory.name }}
                            </a>
                        </li>
                    </ul>
                </template>
            </div>
        </div>

        <!-- Shop By Price - Center -->
        <div>
            <p class="font-bold text-lg text-navyBlue mb-4 uppercase">
                Shop By Price
            </p>
            
            <ul class="grid grid-cols-[1fr] gap-3">
                <li class="text-sm font-medium text-zinc-500">
                    <a :href="`${category.url}?price=0%2C1500`">
                        Under ₹1,500
                    </a>
                </li>
                <li class="text-sm font-medium text-zinc-500">
                    <a :href="`${category.url}?price=1500%2C5000`">
                        ₹1,500 - ₹5,000
                    </a>
                </li>
                <li class="text-sm font-medium text-zinc-500">
                    <a :href="`${category.url}?price=5000%2C10000`">
                        ₹5,000 - ₹10,000
                    </a>
                </li>
                @php
$maxPrice = DB::table('products')
    ->join('product_flat', 'products.id', '=', 'product_flat.product_id')
    ->where('products.type', 'simple')
    ->max('product_flat.price');
@endphp

                <li class="text-sm font-medium text-zinc-500">
                    <a :href="`${category.url}?price=10000%2C{{ $maxPrice }}`">
                        Above ₹10,000
                    </a>
                </li>
            </ul>
        </div>

        <!-- Shop By Style - Right Side -->
        <div>
            <p class="font-bold text-lg text-navyBlue mb-4 uppercase">
                Shop By Style
            </p>
            
            <ul class="grid grid-cols-[1fr] gap-3">
                <li class="text-sm font-medium text-zinc-500">
                    <a :href="`${category.url}?style=10`">
                        Everyday
                    </a>
                </li>
                <li class="text-sm font-medium text-zinc-500">
                    <a :href="`${category.url}?style=11`">
                        Office
                    </a>
                </li>
                <li class="text-sm font-medium text-zinc-500">
                    <a :href="`${category.url}?style=12`">
                        Party
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
</div>
<!-- </div> -->
</div>
     

        <!-- Sidebar category layout -->
        <div v-else>
            <!-- Categories Navigation -->
            <div class="flex items-center">
                <!-- "All" button for opening the category drawer -->
                <div
                    class="flex h-[77px] cursor-pointer items-center border-b-4 border-transparent hover:border-b-4 hover:border-navyBlue"
                    @click="toggleCategoryDrawer"
                >
                    <span class="flex items-center gap-1 px-5 uppercase">
                        <span class="icon-hamburger text-xl"></span>

                        @lang('shop::app.components.layouts.header.desktop.bottom.all')
                    </span>
                </div>

                <!-- Show only first 4 categories in main navigation -->
                <div
                    class="group relative flex h-[77px] items-center border-b-4 border-transparent hover:border-b-4 hover:border-navyBlue"
                    v-for="category in categories.slice(0, 4)"
                >
                    <span>
                        <a
                            :href="category.url"
                            class="inline-block px-5 uppercase"
                        >
                            @{{ category.name }}
                        </a>
                    </span>

                    <!-- Dropdown for each category -->
                    <div
                        class="pointer-events-none absolute top-[78px] z-[1] max-h-[580px] w-max max-w-[1260px] translate-y-1 overflow-auto overflow-x-auto border border-b-0 border-l-0 border-r-0 border-t border-[#F3F3F3] bg-white p-9 opacity-0 shadow-[0_6px_6px_1px_rgba(0,0,0,.3)] transition duration-300 ease-out group-hover:pointer-events-auto group-hover:translate-y-0 group-hover:opacity-100 group-hover:duration-200 group-hover:ease-in ltr:-left-9 rtl:-right-9"
                        v-if="category.children && category.children.length"
                    >
                        <div class="flex justify-between gap-x-[70px]">
                            <div
                                class="grid w-full min-w-max max-w-[150px] flex-auto grid-cols-[1fr] content-start gap-5"
                                v-for="pairCategoryChildren in pairCategoryChildren(category)"
                            >
                                <template v-for="secondLevelCategory in pairCategoryChildren">
                                    <p class="font-medium text-navyBlue">
                                        <a :href="secondLevelCategory.url">
                                            @{{ secondLevelCategory.name }}
                                        </a>
                                    </p>

                                    <ul
                                        class="grid grid-cols-[1fr] gap-3"
                                        v-if="secondLevelCategory.children && secondLevelCategory.children.length"
                                    >
                                        <li
                                            class="text-sm font-medium text-zinc-500"
                                            v-for="thirdLevelCategory in secondLevelCategory.children"
                                        >
                                            <a :href="thirdLevelCategory.url">
                                                @{{ thirdLevelCategory.name }}
                                            </a>
                                        </li>
                                    </ul>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bagisto Drawer Integration -->
            <x-shop::drawer
                position="left"
                width="400px"
                ::is-active="isDrawerActive"
                @toggle="onDrawerToggle"
                @close="onDrawerClose"
            >
                <x-slot:toggle></x-slot>

                <x-slot:header class="border-b border-gray-200">
                    <div class="flex w-full items-center justify-between">
                        <p class="text-xl font-medium">
                            @lang('shop::app.components.layouts.header.desktop.bottom.categories')
                        </p>
                    </div>
                </x-slot>

                <x-slot:content class="!px-0">
                    <!-- Wrapper with transition effects -->
                    <div class="relative h-full overflow-hidden">
                        <!-- Sliding container -->
                        <div
                            class="flex h-full transition-transform duration-300"
                            :class="{
                                'ltr:translate-x-0 rtl:translate-x-0': currentViewLevel !== 'third',
                                'ltr:-translate-x-full rtl:translate-x-full': currentViewLevel === 'third'
                            }"
                        >
                            <!-- First level view -->
                            <div class="h-[calc(100vh-74px)] w-full flex-shrink-0 overflow-auto">
                                <div class="py-4">
                                    <div
                                        v-for="category in categories"
                                        :key="category.id"
                                        :class="{'mb-2': category.children && category.children.length}"
                                    >
                                        <div class="flex cursor-pointer items-center justify-between px-6 py-2 transition-colors duration-200 hover:bg-gray-100">
                                            <a
                                                :href="category.url"
                                                class="text-base font-medium text-black"
                                            >
                                                @{{ category.name }}
                                            </a>
                                        </div>

                                        <!-- Second Level Categories -->
                                        <div v-if="category.children && category.children.length" >
                                            <div
                                                v-for="secondLevelCategory in category.children"
                                                :key="secondLevelCategory.id"
                                            >
                                                <div
                                                    class="flex cursor-pointer items-center justify-between px-6 py-2 transition-colors duration-200 hover:bg-gray-100"
                                                    @click="showThirdLevel(secondLevelCategory, category, $event)"
                                                >
                                                    <a
                                                        :href="secondLevelCategory.url"
                                                        class="text-sm font-normal"
                                                    >
                                                        @{{ secondLevelCategory.name }}
                                                    </a>

                                                    <span
                                                        v-if="secondLevelCategory.children && secondLevelCategory.children.length"
                                                        class="icon-arrow-right rtl:icon-arrow-left"
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
                                        class="flex items-center justify-center gap-2 focus:outline-none"
                                        aria-label="Go back"
                                    >
                                        <span class="icon-arrow-left rtl:icon-arrow-right text-lg"></span>

                                        <p class="text-base font-medium text-black">
                                            @lang('shop::app.components.layouts.header.desktop.bottom.back-button')
                                        </p>
                                    </button>
                                </div>

                                <!-- Third Level Content -->
                                <div class="py-4">
                                    <div
                                        v-for="thirdLevelCategory in currentSecondLevelCategory?.children"
                                        :key="thirdLevelCategory.id"
                                        class="mb-2"
                                    >
                                        <a
                                            :href="thirdLevelCategory.url"
                                            class="block px-6 py-2 text-sm transition-colors duration-200 hover:bg-gray-100"
                                        >
                                            @{{ thirdLevelCategory.name }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-slot>
            </x-shop::drawer>
        </div>
    </script>

    <script type="module">
        app.component('v-desktop-category', {
            template: '#v-desktop-category-template',

            data() {
                return {
                    isLoading: true,
                    categories: [],
                    isDrawerActive: false,
                    currentViewLevel: 'main',
                    currentSecondLevelCategory: null,
                    currentParentCategory: null
                }
            },

            mounted() {
                this.getCategories();
            },

            methods: {
                getCategories() {
                    this.$axios.get("{{ route('shop.api.categories.tree') }}")
                        .then(response => {
                            this.isLoading = false;
                            this.categories = response.data.data;
                        })
                        .catch(error => {
                            console.log(error);
                        });
                },

                pairCategoryChildren(category) {
                    if (! category.children) return [];

                    return category.children.reduce((result, value, index, array) => {
                        if (index % 2 === 0) {
                            result.push(array.slice(index, index + 2));
                        }
                        return result;
                    }, []);
                },

                toggleCategoryDrawer() {
                    this.isDrawerActive = !this.isDrawerActive;
                    if (this.isDrawerActive) {
                        this.currentViewLevel = 'main';
                    }
                },

                onDrawerToggle(event) {
                    this.isDrawerActive = event.isActive;
                },

                onDrawerClose(event) {
                    this.isDrawerActive = false;
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
    </script>
@endPushOnce


@pushOnce('scripts')
<script type="text/x-template" id="topbar-message-template">
    <div class="w-screen bg-black text-white py-3 px-6">
        <div class="w-[500px] mx-auto flex items-center justify-center relative py-6">
            <!-- Left Arrow -->
            <span 
                @click="prev"
                class="absolute left-0 cursor-pointer hover:text-gray-400 select-none text-lg"
            >
                &#8592;
            </span>

            <!-- Animated Message -->
            <transition
                name="fade"
                mode="out-in"
                enter-active-class="transition duration-300 ease-out"
                leave-active-class="transition duration-300 ease-in"
                enter-from-class="opacity-0 translate-y-1"
                enter-to-class="opacity-100 translate-y-0"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 -translate-y-1"
            >
                <span 
                    :key="currentMessage" 
                    class="text-center text-base font-medium text-white w-full"
                >
                    @{{ currentMessage }}
                </span>
            </transition>

            <!-- Right Arrow -->
            <span 
                @click="next"
                class="absolute right-0 cursor-pointer hover:text-gray-400 select-none text-lg"
            >
                &#8594;
            </span>
        </div>
    </div>
</script>

<script type="module">
    app.component('topbar-message', {
        template: '#topbar-message-template',
        data() {
            return {
                messages: [
                    'COD available within India',
                    'Free International Shipping above $150',
                    'Free Shipping all over India',
                ],
                index: 0,
                intervalId: null,
            };
        },
        computed: {
            currentMessage() {
                return this.messages[this.index];
            }
        },
        methods: {
            next() {
                this.index = (this.index + 1) % this.messages.length;
            },
            prev() {
                this.index = (this.index - 1 + this.messages.length) % this.messages.length;
            },
            startAutoSlide() {
                this.intervalId = setInterval(this.next, 4000); // Every 4 seconds
            },
            stopAutoSlide() {
                if (this.intervalId) clearInterval(this.intervalId);
            }
        },
        mounted() {
            this.startAutoSlide();
        },
        beforeUnmount() {
            this.stopAutoSlide();
        }
    });
</script>
@endPushOnce





{!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.after') !!}