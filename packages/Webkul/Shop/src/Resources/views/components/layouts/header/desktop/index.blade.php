<div class="flex flex-wrap max-lg:hidden">
    <x-shop::layouts.header.desktop.bottom />


    <!-- Manually added Category Sections -->
     
    {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.category.before') !!}
    <div class="w-full flex justify-center">
        <div class="flex items-center gap-x-8 max-[1180px]:gap-x-3 flex-wrap justify-center">
        <v-desktop-category>
            <div class="flex items-center gap-3 flex-wrap justify-center>
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
        </v-desktop-category>
        </div>
    </div>
</div>

