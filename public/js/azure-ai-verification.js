/**
 * Azure AI Verification - Backend-powered ID verification
 * 
 * This module sends images to the Laravel backend which uses Azure Face API
 * for professional-grade face verification.
 * 
 * @version 2.0.0
 * @author LGU System
 */

class AzureAIVerification {
    constructor() {
        this.apiEndpoint = '/api/verify-ai';
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    }

    /**
     * Verify ID images and selfie using Azure Face API (via backend)
     * 
     * @param {HTMLImageElement} idFrontImage - ID front image element
     * @param {HTMLImageElement} idBackImage - ID back image element  
     * @param {HTMLImageElement} selfieImage - Selfie image element
     * @returns {Promise<Object>} Verification results
     */
    async verifyImages(idFrontImage, idBackImage, selfieImage) {
        try {
            console.log('Starting Azure AI verification...');

            // Convert images to base64
            const idFrontBase64 = await this.imageToBase64(idFrontImage);
            const idBackBase64 = await this.imageToBase64(idBackImage);
            const selfieBase64 = await this.imageToBase64(selfieImage);

            console.log('📸 Images converted to base64, sending to backend...');

            // Send to backend API
            const response = await fetch(this.apiEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    id_front: idFrontBase64,
                    id_back: idBackBase64,
                    selfie: selfieBase64
                })
            });

            const result = await response.json();

            console.log('Backend response received:', result);

            // Handle response
            if (!result.success) {
                return {
                    status: result.status || 'failed',
                    error: result.error || 'Verification failed',
                    notes: result.notes || [],
                    face_match_score: result.face_match_score || 0,
                    id_authenticity_score: result.id_authenticity_score || 0,
                    liveness_score: result.liveness_score || 0,
                    overall_confidence: result.overall_confidence || 0,
                    hashes: result.hashes || {}
                };
            }

            // Success - return full results
            return {
                status: result.status,
                face_match_score: result.face_match_score,
                id_authenticity_score: result.id_authenticity_score,
                liveness_score: result.liveness_score,
                overall_confidence: result.overall_confidence,
                confidence: result.confidence,
                notes: result.notes,
                hashes: result.hashes,
                error: result.error
            };

        } catch (error) {
            console.error('Azure AI verification error:', error);
            
            return {
                status: 'manual_review',
                error: error.message || 'Verification service error',
                notes: ['System error - manual review required'],
                face_match_score: 0,
                id_authenticity_score: 0,
                liveness_score: 0,
                overall_confidence: 0
            };
        }
    }

    /**
     * Convert image element to base64 string
     * 
     * @param {HTMLImageElement} imageElement - Image element to convert
     * @returns {Promise<string>} Base64 encoded image
     */
    async imageToBase64(imageElement) {
        return new Promise((resolve, reject) => {
            try {
                const canvas = document.createElement('canvas');
                canvas.width = imageElement.naturalWidth || imageElement.width;
                canvas.height = imageElement.naturalHeight || imageElement.height;
                
                const ctx = canvas.getContext('2d');
                ctx.drawImage(imageElement, 0, 0);
                
                // Get base64 (JPEG format for smaller size)
                const base64 = canvas.toDataURL('image/jpeg', 0.9);
                resolve(base64);
            } catch (error) {
                reject(error);
            }
        });
    }

    /**
     * Check if Azure AI is available (optional - for status checks)
     * 
     * @returns {Promise<boolean>}
     */
    async isAvailable() {
        try {
            const response = await fetch('/api/azure-status', {
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken
                }
            });
            const result = await response.json();
            return result.available === true;
        } catch (error) {
            return false;
        }
    }
}

// Export for use in registration form
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AzureAIVerification;
}

