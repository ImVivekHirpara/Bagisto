/**
 * Barcode Scanner Component with iOS Optimizations
 * Provides barcode/QR code scanning functionality optimized for iOS devices
 */

import { Html5QrcodeScanner, Html5QrcodeScanType, Html5QrcodeSupportedFormats } from 'html5-qrcode';
import { 
    isIOS, 
    isIPhone, 
    getIOSCameraConstraints, 
    supportsGetUserMedia,
    getOrientation 
} from '../utils/deviceDetection.js';

export default {
    name: 'BarcodeScanner',
    
    props: {
        visible: {
            type: Boolean,
            default: false
        },
        onScanSuccess: {
            type: Function,
            required: true
        },
        onScanFailure: {
            type: Function,
            default: () => {}
        },
        onClose: {
            type: Function,
            required: true
        }
    },

    data() {
        return {
            scanner: null,
            isScanning: false,
            errorMessage: '',
            permissionGranted: false,
            isIOSDevice: false,
            scannerConfig: null,
            lastOrientation: null
        };
    },

    mounted() {
        this.isIOSDevice = isIOS();
        this.lastOrientation = getOrientation();
        this.setupScannerConfig();
        
        if (this.visible) {
            this.$nextTick(() => {
                this.initializeScanner();
            });
        }

        // Listen for orientation changes on iOS
        if (this.isIOSDevice) {
            window.addEventListener('orientationchange', this.handleOrientationChange);
            window.addEventListener('resize', this.handleOrientationChange);
        }
    },

    beforeUnmount() {
        this.cleanup();
        
        if (this.isIOSDevice) {
            window.removeEventListener('orientationchange', this.handleOrientationChange);
            window.removeEventListener('resize', this.handleOrientationChange);
        }
    },

    watch: {
        visible(newVal) {
            if (newVal) {
                this.$nextTick(() => {
                    this.initializeScanner();
                });
            } else {
                this.stopScanner();
            }
        }
    },

    methods: {
        setupScannerConfig() {
            // Base configuration
            this.scannerConfig = {
                fps: this.isIOSDevice ? 10 : 10, // Lower FPS for iOS
                qrbox: this.isIOSDevice ? 200 : 250,
                aspectRatio: this.isIOSDevice ? 16/9 : 1.0,
                supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA],
                formatsToSupport: [
                    Html5QrcodeSupportedFormats.QR_CODE,
                    Html5QrcodeSupportedFormats.CODE_128,
                    Html5QrcodeSupportedFormats.CODE_39,
                    Html5QrcodeSupportedFormats.EAN_13,
                    Html5QrcodeSupportedFormats.EAN_8,
                    Html5QrcodeSupportedFormats.UPC_A,
                    Html5QrcodeSupportedFormats.UPC_E
                ],
                showTorchButtonIfSupported: true,
                showZoomSliderIfSupported: false, // Disabled for iOS compatibility
                defaultZoomValueIfSupported: 1
            };

            // iOS specific optimizations
            if (this.isIOSDevice) {
                this.scannerConfig.experimentalFeatures = {
                    useBarCodeDetectorIfSupported: false // Disabled for iOS compatibility
                };
                
                // iPhone specific settings
                if (isIPhone()) {
                    this.scannerConfig.qrbox = 180;
                    this.scannerConfig.fps = 8;
                }

                // Configure camera constraints for iOS
                this.scannerConfig.videoConstraints = getIOSCameraConstraints().video;
            }
        },

        async initializeScanner() {
            if (this.isScanning || !this.visible) return;

            try {
                // Check for camera support
                if (!supportsGetUserMedia()) {
                    throw new Error('Camera not supported on this device');
                }

                // Request camera permission for iOS
                if (this.isIOSDevice) {
                    await this.requestCameraPermission();
                }

                this.errorMessage = '';
                this.isScanning = true;

                // Create scanner instance
                this.scanner = new Html5QrcodeScanner(
                    "qr-reader", 
                    this.scannerConfig,
                    /* verbose= */ false
                );

                // Start scanning
                this.scanner.render(
                    this.onScanSuccessHandler,
                    this.onScanFailureHandler
                );

                this.permissionGranted = true;

            } catch (error) {
                this.handleScannerError(error);
            }
        },

        async requestCameraPermission() {
            try {
                const constraints = getIOSCameraConstraints();
                const stream = await navigator.mediaDevices.getUserMedia(constraints);
                
                // Stop the stream immediately after getting permission
                stream.getTracks().forEach(track => track.stop());
                
                return true;
            } catch (error) {
                if (error.name === 'NotAllowedError') {
                    throw new Error('Camera permission denied. Please allow camera access and try again.');
                } else if (error.name === 'NotFoundError') {
                    throw new Error('No camera found on this device.');
                } else if (error.name === 'NotSupportedError') {
                    throw new Error('Camera not supported on this device.');
                } else {
                    throw new Error(`Camera error: ${error.message}`);
                }
            }
        },

        onScanSuccessHandler(decodedText, decodedResult) {
            try {
                // Stop scanning after successful scan
                this.stopScanner();
                
                // Call the success callback
                this.onScanSuccess(decodedText, decodedResult);
                
                // Close the scanner
                this.onClose();
            } catch (error) {
                console.error('Error handling scan success:', error);
            }
        },

        onScanFailureHandler(error) {
            // Call the failure callback (optional)
            this.onScanFailure(error);
            
            // Don't show errors for common scanning issues
            const ignoredErrors = [
                'No QR code found',
                'QR code parse error',
                'Scanner is not running'
            ];
            
            if (!ignoredErrors.some(ignored => error.includes(ignored))) {
                console.warn('Scan failure:', error);
            }
        },

        handleScannerError(error) {
            console.error('Scanner error:', error);
            this.errorMessage = this.getErrorMessage(error);
            this.isScanning = false;
            this.permissionGranted = false;
        },

        getErrorMessage(error) {
            if (typeof error === 'string') {
                return error;
            }

            if (error.message) {
                return error.message;
            }

            // Default error messages for iOS
            if (this.isIOSDevice) {
                return 'Unable to access camera. Please check your camera permissions in Settings > Safari > Camera.';
            }

            return 'Unable to start camera. Please check your camera permissions.';
        },

        stopScanner() {
            if (this.scanner && this.isScanning) {
                try {
                    this.scanner.clear();
                } catch (error) {
                    console.warn('Error stopping scanner:', error);
                }
            }
            this.isScanning = false;
        },

        cleanup() {
            this.stopScanner();
            this.scanner = null;
            this.permissionGranted = false;
            this.errorMessage = '';
        },

        handleOrientationChange() {
            // Debounce orientation changes
            if (this.orientationTimeout) {
                clearTimeout(this.orientationTimeout);
            }

            this.orientationTimeout = setTimeout(() => {
                const currentOrientation = getOrientation();
                
                if (currentOrientation !== this.lastOrientation && this.isScanning) {
                    this.lastOrientation = currentOrientation;
                    
                    // Restart scanner on iOS orientation change
                    this.stopScanner();
                    
                    setTimeout(() => {
                        if (this.visible) {
                            this.initializeScanner();
                        }
                    }, 500);
                }
            }, 200);
        },

        retryScanner() {
            this.cleanup();
            this.$nextTick(() => {
                this.initializeScanner();
            });
        }
    },

    template: `
        <div v-if="visible" class="barcode-scanner-overlay">
            <div class="barcode-scanner-modal">
                <div class="barcode-scanner-header">
                    <h3>Scan Barcode</h3>
                    <button @click="onClose" class="close-button">
                        <span class="icon-cancel-1"></span>
                    </button>
                </div>
                
                <div class="barcode-scanner-content">
                    <div v-if="errorMessage" class="error-message">
                        <p>{{ errorMessage }}</p>
                        <button @click="retryScanner" class="retry-button">
                            Try Again
                        </button>
                    </div>
                    
                    <div v-else-if="isScanning" class="scanner-container">
                        <div id="qr-reader"></div>
                        <div class="scanner-instructions">
                            <p v-if="isIOSDevice">
                                Point your camera at a barcode or QR code. 
                                For best results, ensure good lighting and hold steady.
                            </p>
                            <p v-else>
                                Point your camera at a barcode or QR code.
                            </p>
                        </div>
                    </div>
                    
                    <div v-else class="scanner-loading">
                        <div class="loading-spinner"></div>
                        <p>Initializing camera...</p>
                    </div>
                </div>
            </div>
        </div>
    `
};