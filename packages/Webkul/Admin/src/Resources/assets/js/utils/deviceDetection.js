/**
 * iOS Device Detection Utility
 * Provides methods to detect iOS devices and their capabilities
 */

/**
 * Detect if the current device is an iOS device
 * @returns {boolean} True if device is iOS
 */
export function isIOS() {
    return /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
}

/**
 * Detect if the current device is an iPhone
 * @returns {boolean} True if device is iPhone
 */
export function isIPhone() {
    return /iPhone/.test(navigator.userAgent) && !window.MSStream;
}

/**
 * Detect if the current device is an iPad
 * @returns {boolean} True if device is iPad
 */
export function isIPad() {
    return /iPad/.test(navigator.userAgent) || 
           (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
}

/**
 * Get iOS version if on iOS device
 * @returns {string|null} iOS version or null if not iOS
 */
export function getIOSVersion() {
    if (!isIOS()) return null;
    
    const match = navigator.userAgent.match(/OS (\d+)_(\d+)_?(\d+)?/);
    if (match) {
        return `${match[1]}.${match[2]}${match[3] ? '.' + match[3] : ''}`;
    }
    return null;
}

/**
 * Check if device supports getUserMedia
 * @returns {boolean} True if getUserMedia is supported
 */
export function supportsGetUserMedia() {
    return !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia);
}

/**
 * Get iOS specific camera constraints for better performance
 * @returns {Object} Camera constraints optimized for iOS
 */
export function getIOSCameraConstraints() {
    const constraints = {
        video: {
            facingMode: "environment", // Use back camera by default
            width: { ideal: 1280 },
            height: { ideal: 720 },
            frameRate: { ideal: 30, max: 30 }
        },
        audio: false
    };

    // Additional constraints for iOS
    if (isIOS()) {
        constraints.video.aspectRatio = { ideal: 16/9 };
        
        // For older iOS versions, use lower resolution
        const version = getIOSVersion();
        if (version && parseFloat(version) < 14) {
            constraints.video.width = { ideal: 640 };
            constraints.video.height = { ideal: 480 };
            constraints.video.frameRate = { ideal: 15, max: 15 };
        }
    }

    return constraints;
}

/**
 * Check if device is in landscape mode
 * @returns {boolean} True if in landscape
 */
export function isLandscape() {
    return window.innerWidth > window.innerHeight;
}

/**
 * Get device orientation
 * @returns {string} 'portrait' or 'landscape'
 */
export function getOrientation() {
    return isLandscape() ? 'landscape' : 'portrait';
}