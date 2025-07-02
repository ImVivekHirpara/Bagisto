<v-products-carousel
    src="{{ $src }}"
    title="{{ $title }}"
    navigation-link="{{ $navigationLink ?? '' }}"
>
    <x-shop::shimmer.products.carousel :navigation-link="$navigationLink ?? false" />
</v-products-carousel>

@pushOnce('scripts')
    <!-- Enhanced CSS for BEST SELLER badge -->
    <style>
        .best-seller-badge {
            background: linear-gradient(135deg, #FFD700, #FFA500) !important;
            color: #000 !important;
            font-weight: 700 !important;
            font-size: 12px !important;
            padding: 6px 12px !important;
            border-radius: 20px !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            box-shadow: 0 4px 12px rgba(255, 215, 0, 0.4) !important;
            border: 2px solid #FFD700 !important;
            position: relative !important;
            z-index: 10 !important;
            animation: pulse-glow 2s infinite !important;
        }

        @keyframes pulse-glow {
            0%, 100% {
                box-shadow: 0 4px 12px rgba(255, 215, 0, 0.4);
                transform: scale(1);
            }
            50% {
                box-shadow: 0 6px 20px rgba(255, 215, 0, 0.6);
                transform: scale(1.05);
            }
        }

        .view-all-btn {
            background-color: #000000 !important;
            color: #FFFFFF !important;
            font-family: 'DM Sans', sans-serif !important;
            font-size: 16px !important;
            padding: 17px 60px !important;
            text-decoration: none !important;
            font-weight: 400 !important;
            letter-spacing: 1px !important;
            cursor: pointer !important;
            transition: all 0.3s ease-in-out !important;
            border-radius: 4px !important;
        }

        .view-all-btn:hover {
            background-color: #333333 !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3) !important;
        }

        .section-title {
            font-family: 'Lora', serif !important;
            font-size: 36px !important;
            color: #1a1a1a !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            letter-spacing: 2px !important;
            position: relative !important;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, #FFD700, #FFA500);
            border-radius: 2px;
        }

        .carousel-container {
            position: relative;
        }

        .carousel-container:hover .navigation-arrows {
            opacity: 1;
        }

        .navigation-arrows {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        /* Ensure they are within image area */
        margin-top: -20px; /* optional fine-tune */
        background: rgba(255, 255, 255, 0.95);
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: all 0.3s ease;
        z-index: 15;
        cursor: pointer;
        box-shadow: 0 3px 12px rgba(0, 0, 0, 0.15);
    }

        .navigation-arrows:hover {
            background: rgba(255, 255, 255, 1);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
            transform: translateY(-50%) scale(1.05);
        }

        .navigation-arrows.left {
            left: -22px; /* move closer or overlap slightly into image */
        }

        .navigation-arrows.right {
            right: -22px;
        }

        .navigation-arrows.hidden {
            display: none;
        }

        /* Ensure the carousel container has proper positioning for centering arrows */
        .carousel-container .flex {
            position: relative;
        }

        @media (max-width: 768px) {
            .section-title {
                font-size: 28px !important;
            }
        }

        @media (max-width: 640px) {
            .section-title {
                font-size: 22px !important;
            }
        }

        @media (max-width: 768px) {
        .navigation-arrows {
            width: 36px;
            height: 36px;
        }

        .navigation-arrows.left {
            left: 4px;
        }

        .navigation-arrows.right {
            right: 4px;
        }
    }

    </style>

    <script
        type="text/x-template"
        id="v-products-carousel-template"
    >
        <div
            class="container mt-8 max-lg:px-8 max-md:mt-6 max-sm:mt-4 max-sm:!px-4"
            v-if="! isLoading && products.length"
        >
            <div class="flex justify-center relative mb-12">
                <h2 class="section-title text-center">
                    @{{ title }}
                </h2>

                <div class="flex items-center justify-between gap-8 absolute right-0">
                    <a
                        :href="navigationLink"
                        class="hidden max-lg:flex cursor-pointer hover:scale-105 transition-transform duration-300"
                        v-if="navigationLink"
                    >
                        <p class="items-center text-xl max-md:text-base max-sm:text-sm uppercase">
                            @lang('shop::app.components.products.carousel.view-all')

                            <span class="icon-arrow-right text-2xl max-md:text-lg max-sm:text-sm"></span>
                        </p>
                    </a>
                </div>
            </div>

            <div class="carousel-container max-lg:overflow-visible relative">
                <!-- Left Navigation Arrow -->
                <div
                    v-if="showNavigation && currentFrame > 0"
                    class="navigation-arrows left max-lg:hidden"
                    @click="swipeLeft"
                    role="button"
                    aria-label="@lang('shop::app.components.products.carousel.previous')"
                    tabindex="0"
                >
                    <span class="icon-arrow-left-stylish rtl:icon-arrow-right-stylish text-lg"></span>
                </div>

                <!-- Right Navigation Arrow -->
                <div
                    v-if="showNavigation && currentFrame < totalFrames - 1"
                    class="navigation-arrows right max-lg:hidden"
                    @click="swipeRight"
                    role="button"
                    aria-label="@lang('shop::app.components.products.carousel.next')"
                    tabindex="0"
                >
                    <span class="icon-arrow-right-stylish rtl:icon-arrow-left-stylish text-lg"></span>
                </div>

                <div
                    ref="swiperContainer"
                    class="flex gap-8 pb-2.5 [&>*]:flex-[0] mt-10 overflow-auto scroll-smooth scrollbar-hide max-md:gap-7 max-md:mt-5 max-sm:gap-4 max-md:pb-0 max-md:whitespace-nowrap relative"
                >
                    <x-shop::products.card
                        class="min-w-[291px] max-md:h-fit max-md:min-w-56 max-sm:min-w-[192px] relative"
                        v-for="product in products"
                    >
                        <!-- Enhanced BEST SELLER Badge -->
                        <div 
                            v-if="product.is_bestseller || product.badge === 'BEST SELLER'"
                            class="best-seller-badge absolute top-3 left-3 z-10"
                        >
                            BEST SELLER
                        </div>
                    </x-shop::products.card>
                </div>
            </div>

            <a
                :href="navigationLink"
                class="view-all-btn mx-auto mt-10 block w-max text-center max-lg:mt-8 max-lg:hidden uppercase"
                :aria-label="title"
                v-if="navigationLink"
            >
                @lang('shop::app.components.products.carousel.view-all')
            </a>
        </div>

        <!-- Product Card Listing -->
        <template v-if="isLoading">
            <x-shop::shimmer.products.carousel :navigation-link="$navigationLink ?? false" />
        </template>
    </script>

    <script type="module">
        app.component('v-products-carousel', {
            template: '#v-products-carousel-template',

            props: [
                'src',
                'title',
                'navigationLink',
            ],

            data() {
                return {
                    isLoading: true,
                    products: [],
                    currentFrame: 0,
                    totalFrames: 0,
                    itemsPerFrame: 4,
                    itemWidth: 291,
                    gap: 32,
                    showNavigation: false,
                };
            },

            mounted() {
                this.getProducts();
                this.updateResponsiveSettings();
                window.addEventListener('resize', this.handleResize);
            },

            beforeDestroy() {
                window.removeEventListener('resize', this.handleResize);
            },

            methods: {
                getProducts() {
                    this.$axios.get(this.src)
                        .then(response => {
                            this.isLoading = false;
                            // Limit to maximum 10 items
                            this.products = response.data.data.slice(0, 10);
                            this.$nextTick(() => {
                                this.calculateFrames();
                            });
                        }).catch(error => {
                            console.log(error);
                        });
                },

                updateResponsiveSettings() {
                    const screenWidth = window.innerWidth;
                    
                    if (screenWidth <= 640) { // max-sm
                        this.itemsPerFrame = 1;
                        this.itemWidth = 192;
                        this.gap = 16;
                    } else if (screenWidth <= 768) { // max-md
                        this.itemsPerFrame = 2;
                        this.itemWidth = 224;
                        this.gap = 28;
                    } else if (screenWidth <= 1024) { // max-lg
                        this.itemsPerFrame = 3;
                        this.itemWidth = 224;
                        this.gap = 28;
                    } else if (screenWidth <= 1440) { // max-2xl
                        this.itemsPerFrame = 3;
                        this.itemWidth = 291;
                        this.gap = 32;
                    } else {
                        this.itemsPerFrame = 4;
                        this.itemWidth = 291;
                        this.gap = 32;
                    }
                },

                calculateFrames() {
                    if (!this.products.length) return;
                    
                    this.updateResponsiveSettings();
                    this.totalFrames = Math.ceil(this.products.length / this.itemsPerFrame);
                    this.showNavigation = this.totalFrames > 1;
                    
                    // Reset current frame if it exceeds total frames
                    if (this.currentFrame >= this.totalFrames) {
                        this.currentFrame = 0;
                    }
                },

                handleResize() {
                    this.updateResponsiveSettings();
                    this.calculateFrames();
                    this.scrollToCurrentFrame();
                },

                swipeLeft() {
                    if (this.currentFrame > 0) {
                        this.currentFrame--;
                        this.scrollToCurrentFrame();
                    }
                },

                swipeRight() {
                    if (this.currentFrame < this.totalFrames - 1) {
                        this.currentFrame++;
                        this.scrollToCurrentFrame();
                    }
                },

                scrollToCurrentFrame() {
                    const container = this.$refs.swiperContainer;
                    if (!container) return;

                    const scrollDistance = this.currentFrame * (this.itemWidth + this.gap) * this.itemsPerFrame;
                    container.scrollLeft = scrollDistance;
                },

                getFrameScrollDistance() {
                    return (this.itemWidth + this.gap) * this.itemsPerFrame;
                }
            },

            watch: {
                products() {
                    this.calculateFrames();
                }
            }
        });
    </script>
@endPushOnce