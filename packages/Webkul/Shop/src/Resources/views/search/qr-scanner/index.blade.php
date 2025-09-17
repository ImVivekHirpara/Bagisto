<v-qr-scanner>
    <button
        type="button"
        class="icon-camera absolute top-3 flex items-center text-xl max-sm:top-2.5 ltr:right-3 ltr:pr-3 max-md:ltr:right-1.5 rtl:left-3 rtl:pl-3 max-md:rtl:left-1.5"
        aria-label="@lang('shop::app.search.qr-scanner.scan')"
    >
    </button>
</v-qr-scanner>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-qr-scanner-template"
    >
        <div>
            <!-- Scanner Toggle Button -->
            <label
                class="icon-camera absolute top-3 flex items-center text-xl max-sm:top-2.5 ltr:right-3 ltr:pr-3 max-md:ltr:right-1.5 rtl:left-3 rtl:pl-3 max-md:rtl:left-1.5 cursor-pointer"
                aria-label="@lang('shop::app.search.qr-scanner.scan')"
                @click="toggleScanner"
                v-if="!isLoading && !isScanning"
            >
            </label>

            <!-- Loading Spinner -->
            <label
                class="absolute top-2.5 flex cursor-pointer items-center text-xl ltr:right-3 ltr:pr-3 max-md:ltr:pr-1 rtl:left-3 rtl:pl-3 max-md:rtl:pl-1"
                v-if="isLoading"
            >
                <svg
                    class="h-5 w-5 animate-spin text-black"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                >
                    <circle
                        class="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        stroke-width="4"
                    />
                    <path
                        class="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                    />
                </svg>
            </label>

            <!-- Stop Scanner Button -->
            <label
                class="icon-close absolute top-3 flex cursor-pointer items-center text-xl text-red-600 ltr:right-3 ltr:pr-3 max-md:ltr:pr-1 rtl:left-3 rtl:pl-3 max-md:rtl:pl-1"
                @click="stopScanner"
                v-if="isScanning && !isLoading"
                aria-label="@lang('shop::app.search.qr-scanner.stop')"
            >
            </label>

            <!-- Scanner Modal -->
            <div
                v-if="isScanning"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75"
                @click.self="stopScanner"
            >
                <div class="relative mx-4 max-w-lg w-full bg-white rounded-lg p-6">
                    <!-- Modal Header -->
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-lg font-semibold">@lang('shop::app.search.qr-scanner.title')</h3>
                        <button
                            @click="stopScanner"
                            class="text-gray-400 hover:text-gray-600"
                            aria-label="@lang('shop::app.search.qr-scanner.close')"
                        >
                            <span class="icon-close text-xl"></span>
                        </button>
                    </div>

                    <!-- Scanner Container -->
                    <div class="relative">
                        <div
                            id="qr-scanner"
                            class="w-full rounded-lg overflow-hidden"
                            style="min-height: 300px;"
                        ></div>
                        
                        <!-- Scanner overlay with instructions -->
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <div class="w-48 h-48 border-2 border-white rounded-lg shadow-lg"></div>
                        </div>
                    </div>

                    <!-- Instructions -->
                    <p class="mt-4 text-sm text-gray-600 text-center">
                        @lang('shop::app.search.qr-scanner.instructions')
                    </p>

                    <!-- Error Message -->
                    <div v-if="errorMessage" class="mt-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                        @{{ errorMessage }}
                    </div>
                </div>
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-qr-scanner', {
            template: '#v-qr-scanner-template',

            data() {
                return {
                    isLoading: false,
                    isScanning: false,
                    scanner: null,
                    errorMessage: '',
                    Html5QrcodeScanner: null,
                };
            },

            methods: {
                async toggleScanner() {
                    if (this.isScanning) {
                        this.stopScanner();
                        return;
                    }

                    await this.startScanner();
                },

                async loadQrCodeLibrary() {
                    if (!this.Html5QrcodeScanner) {
                        try {
                            // Use dynamic script loading for better compatibility
                            await this.loadScript('https://unpkg.com/html5-qrcode@2.3.8/minified/html5-qrcode.min.js');
                            this.Html5QrcodeScanner = window.Html5QrcodeScanner;
                        } catch (error) {
                            console.error('Failed to load QR code library:', error);
                            throw new Error('Failed to load QR scanner library');
                        }
                    }
                    return this.Html5QrcodeScanner;
                },

                loadScript(src) {
                    return new Promise((resolve, reject) => {
                        if (document.querySelector(`script[src="${src}"]`)) {
                            resolve();
                            return;
                        }

                        const script = document.createElement('script');
                        script.src = src;
                        script.onload = resolve;
                        script.onerror = reject;
                        document.head.appendChild(script);
                    });
                },

                async startScanner() {
                    this.isLoading = true;
                    this.errorMessage = '';

                    try {
                        // Check if device has camera capabilities
                        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                            throw new Error('Camera not supported on this device');
                        }

                        // Load QR code library
                        await this.loadQrCodeLibrary();

                        this.isScanning = true;
                        this.isLoading = false;

                        // Wait for DOM to be ready
                        await this.$nextTick();

                        // Initialize scanner with proper configuration
                        this.scanner = new this.Html5QrcodeScanner(
                            "qr-scanner",
                            {
                                fps: 10,
                                qrbox: { width: 250, height: 250 },
                                aspectRatio: 1.0,
                                rememberLastUsedCamera: true,
                                showTorchButtonIfSupported: true,
                                showZoomSliderIfSupported: true,
                                defaultZoomValueIfSupported: 2,
                                useBarCodeDetectorIfSupported: true,
                            },
                            false // verbose logging disabled
                        );

                        // Handle successful scan
                        this.scanner.render(
                            (decodedText, decodedResult) => {
                                this.onScanSuccess(decodedText, decodedResult);
                            },
                            (errorMessage) => {
                                // Only log actual errors, not scan failures
                                if (errorMessage.includes('NotFoundException') || 
                                    errorMessage.includes('No MultiFormat Readers') ||
                                    errorMessage.includes('Camera facing mode change')) {
                                    // Ignore these common non-error messages
                                    return;
                                }
                                console.warn('QR scan error:', errorMessage);
                            }
                        );

                    } catch (error) {
                        this.isLoading = false;
                        this.isScanning = false;
                        this.handleError(error);
                    }
                },

                stopScanner() {
                    if (this.scanner) {
                        try {
                            this.scanner.clear();
                        } catch (error) {
                            console.warn('Error clearing scanner:', error);
                        }
                        this.scanner = null;
                    }
                    
                    this.isScanning = false;
                    this.isLoading = false;
                    this.errorMessage = '';
                },

                onScanSuccess(decodedText, decodedResult) {
                    console.log('QR Code detected:', decodedText);
                    
                    // Stop the scanner
                    this.stopScanner();

                    // Show success message
                    this.$emitter.emit('add-flash', { type: 'success', message: 'QR code scanned successfully!' });

                    // Check if the scanned content is a URL
                    try {
                        const url = new URL(decodedText);
                        // If it's a valid URL, navigate to it
                        if (confirm('Navigate to: ' + decodedText + '?')) {
                            window.location.href = decodedText;
                        }
                    } catch (urlError) {
                        // If not a URL, use it as a search query
                        const searchUrl = `{{ route('shop.search.index') }}?query=${encodeURIComponent(decodedText)}&qr-search=1`;
                        window.location.href = searchUrl;
                    }
                },

                handleError(error) {
                    console.error('QR Scanner error:', error);
                    
                    let message = 'An error occurred while starting the scanner';
                    
                    if (error.name === 'NotAllowedError' || error.message.includes('Permission denied')) {
                        message = 'Camera permission denied. Please allow camera access to use the QR scanner.';
                    } else if (error.name === 'NotFoundError' || error.message.includes('No camera found')) {
                        message = 'No camera found on this device.';
                    } else if (error.name === 'NotSupportedError') {
                        message = 'QR scanning is not supported on this device or browser.';
                    } else if (error.message.includes('Camera not supported')) {
                        message = 'Camera not supported on this device.';
                    } else if (error.message) {
                        message = error.message;
                    }

                    this.errorMessage = message;
                    this.$emitter.emit('add-flash', { type: 'error', message: message });
                },

                // Handle device orientation changes
                handleOrientationChange() {
                    if (this.isScanning && this.scanner) {
                        // Restart scanner on orientation change for better mobile experience
                        setTimeout(() => {
                            this.stopScanner();
                            this.startScanner();
                        }, 500);
                    }
                }
            },

            mounted() {
                // Listen for orientation changes on mobile devices
                window.addEventListener('orientationchange', this.handleOrientationChange);
                window.addEventListener('resize', this.handleOrientationChange);
            },

            beforeUnmount() {
                this.stopScanner();
                window.removeEventListener('orientationchange', this.handleOrientationChange);
                window.removeEventListener('resize', this.handleOrientationChange);
            }
        });
    </script>
@endPushOnce