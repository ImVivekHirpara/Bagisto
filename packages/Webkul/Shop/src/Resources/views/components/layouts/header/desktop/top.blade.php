{!! view_render_event('bagisto.shop.components.layouts.header.desktop.top.before') !!}

<v-topbar>
    <!-- Shimmer Effect -->
    <div class="flex items-center justify-between border border-b border-l-0 border-r-0 border-t-0 px-16">
        <!-- Offers -->
        <div
            class="shimmer h-6 w-72 rounded py-3"
            role="presentation"
        >
        </div>
    </div>
</v-topbar>

{!! view_render_event('bagisto.shop.components.layouts.header.desktop.top.after') !!}

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-topbar-template"
    >
        <div class="flex w-full items-center justify-between border border-b border-l-0 border-r-0 border-t-0 px-16 bg-black text-white">
            <!-- Rotating Header Messages with Navigation Arrows -->
            <div class="flex items-center justify-center flex-1 py-3">
                <div class="flex items-center justify-center gap-8 max-w-md w-full">
                    <button 
                        @click="previousMessage" 
                        class="text-white hover:text-gray-300 transition-all duration-300 transform hover:scale-110 flex-shrink-0"
                        aria-label="Previous message"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    
                    <div class="flex-1 text-center relative overflow-hidden h-6">
                        <p class="text-sm font-medium text-white absolute inset-0 flex items-center justify-center transition-all duration-500 ease-in-out transform" 
                           :class="{
                               'opacity-100 translate-y-0': !isTransitioning,
                               'opacity-0 -translate-y-2': isTransitioning
                           }">
                            @{{ currentMessage }}
                        </p>
                    </div>
                    
                    <button 
                        @click="nextMessage" 
                        class="text-white hover:text-gray-300 transition-all duration-300 transform hover:scale-110 flex-shrink-0"
                        aria-label="Next message"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </script>

    <script
        type="text/x-template"
        id="v-currency-switcher-template"
    >
        <div class="my-2.5 grid gap-1 overflow-auto max-md:my-0 sm:max-h-[500px] bg-black text-white">
            <span
                class="cursor-pointer px-5 py-2 text-base hover:bg-gray-700 text-white"
                v-for="currency in currencies"
                :class="{'bg-gray-700': currency.code == '{{ core()->getCurrentCurrencyCode() }}'}"
                @click="change(currency)"
            >
                @{{ currency.symbol + ' ' + currency.code }}
            </span>
        </div>
    </script>

    <script
        type="text/x-template"
        id="v-locale-switcher-template"
    >
        <div class="my-2.5 grid gap-1 overflow-auto max-md:my-0 sm:max-h-[500px] bg-black text-white">
            <span
                class="flex cursor-pointer items-center gap-2.5 px-5 py-2 text-base hover:bg-gray-700 text-white"
                :class="{'bg-gray-700': locale.code == '{{ app()->getLocale() }}'}"
                v-for="locale in locales"
                @click="change(locale)"                  
            >
                @{{ locale.name }}
            </span>
        </div>
    </script>

    <style>
        .bg-black {
            background-color: #282828;
        }
        .text-white {
            color: #fff;
        }
        .shop-now-btn {
            background-color: #ff0000;
            color: #fff;
            padding: 5px 15px;
            border-radius: 20px;
            text-decoration: none;
            font-weight: bold;
            margin-left: 10px;
        }
        .hover\:bg-gray-700:hover {
            background-color: #4B5563;
        }
        .hover\:text-gray-300:hover {
            color: #D1D5DB;
        }
        .transition-colors {
            transition-property: color, background-color, border-color, text-decoration-color, fill, stroke;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        }
        .transition-opacity {
            transition-property: opacity;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        }
        .transition-all {
            transition-property: all;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        }
        .duration-200 {
            transition-duration: 200ms;
        }
        .duration-300 {
            transition-duration: 300ms;
        }
        .duration-500 {
            transition-duration: 500ms;
        }
        .opacity-0 {
            opacity: 0;
        }
        .opacity-100 {
            opacity: 1;
        }
        .transform {
            transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
        }
        .hover\:scale-110:hover {
            --tw-scale-x: 1.1;
            --tw-scale-y: 1.1;
            transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
        }
        .translate-y-0 {
            --tw-translate-y: 0px;
            transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
        }
        .-translate-y-2 {
            --tw-translate-y: -0.5rem;
            transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
        }
        .ease-in-out {
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        }
        .flex-1 {
            flex: 1 1 0%;
        }
        .flex-shrink-0 {
            flex-shrink: 0;
        }
        .max-w-md {
            max-width: 28rem;
        }
        .relative {
            position: relative;
        }
        .absolute {
            position: absolute;
        }
        .inset-0 {
            top: 0px;
            right: 0px;
            bottom: 0px;
            left: 0px;
        }
        .overflow-hidden {
            overflow: hidden;
        }
        .h-6 {
            height: 1.5rem;
        }
    </style>

    <script type="module">
        app.component('v-topbar', {
            template: '#v-topbar-template',

            data() {
                return {
                    localeToggler: '',
                    currencyToggler: '',
                    
                    // Rotating messages array
                    messages: [
                        'COD available within India',
                        'Free Shipping',
                    ],
                    currentMessageIndex: 0,
                    isTransitioning: false,
                    autoRotateInterval: null,
                    rotationTimeout: 5000, // Consistent 5-second timeout for both auto-rotation and restart
                };
            },

            computed: {
                currentMessage() {
                    return this.messages[this.currentMessageIndex];
                }
            },

            mounted() {
                // Auto-rotate messages every 5 seconds
                this.startAutoRotate();
            },

            beforeUnmount() {
                this.stopAutoRotate();
            },

            methods: {
                nextMessage() {
                    this.changeMessage(1);
                },

                previousMessage() {
                    this.changeMessage(-1);
                },

                changeMessage(direction) {
                    this.stopAutoRotate(); // Stop auto-rotation when user manually navigates
                    
                    this.isTransitioning = true;
                    
                    setTimeout(() => {
                        if (direction === 1) {
                            this.currentMessageIndex = (this.currentMessageIndex + 1) % this.messages.length;
                        } else {
                            this.currentMessageIndex = this.currentMessageIndex === 0 
                                ? this.messages.length - 1 
                                : this.currentMessageIndex - 1;
                        }
                        
                        setTimeout(() => {
                            this.isTransitioning = false;
                        }, 50);
                        
                        // Restart auto-rotation after the same timeout period (5 seconds)
                        setTimeout(() => {
                            this.startAutoRotate();
                        }, this.rotationTimeout);
                    }, 250);
                },

                startAutoRotate() {
                    this.autoRotateInterval = setInterval(() => {
                        this.isTransitioning = true;
                        setTimeout(() => {
                            this.currentMessageIndex = (this.currentMessageIndex + 1) % this.messages.length;
                            setTimeout(() => {
                                this.isTransitioning = false;
                            }, 50);
                        }, 250);
                    }, this.rotationTimeout); // Use the consistent timeout value
                },

                stopAutoRotate() {
                    if (this.autoRotateInterval) {
                        clearInterval(this.autoRotateInterval);
                        this.autoRotateInterval = null;
                    }
                }
            }
        });

        app.component('v-currency-switcher', {
            template: '#v-currency-switcher-template',

            data() {
                return {
                    currencies: @json(core()->getCurrentChannel()->currencies),
                };
            },

            methods: {
                change(currency) {
                    let url = new URL(window.location.href);

                    url.searchParams.set('currency', currency.code);

                    window.location.href = url.href;
                }
            }
        });

        app.component('v-locale-switcher', {
            template: '#v-locale-switcher-template',

            data() {
                return {
                    locales: @json(core()->getCurrentChannel()->locales()->orderBy('name')->get()),
                };
            },

            methods: {
                change(locale) {
                    let url = new URL(window.location.href);

                    url.searchParams.set('locale', locale.code);

                    window.location.href = url.href;
                }
            }
        });
    </script>
@endPushOnce