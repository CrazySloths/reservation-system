/**
 * AI-POWERED ID VERIFICATION SYSTEM
 * 
 * Technologies:
 * - TensorFlow.js + Face-API.js: Face detection & matching
 * - Google Teachable Machine: ID classification & authenticity
 * - Perceptual Hashing: Duplicate detection
 * 
 * @version 1.0.0
 */

class AIIDVerification {
    constructor() {
        this.faceApiModelsLoaded = false;
        this.teachableMachineModel = null;
        this.verificationResults = {
            faceMatchScore: 0,
            idAuthenticityScore: 0,
            livenessScore: 0,
            isDuplicateDetected: false,
            overallStatus: 'pending',
            notes: []
        };
        
        // Thresholds for auto-approval
        this.THRESHOLDS = {
            FACE_MATCH_MIN: 70,        // 70% similarity required
            ID_AUTHENTICITY_MIN: 75,    // 75% authenticity required
            LIVENESS_MIN: 60,           // 60% liveness required
            AUTO_APPROVE_MIN: 80        // 80% overall confidence for auto-approval
        };
        
        // Timeout for operations (30 seconds max)
        this.OPERATION_TIMEOUT = 30000;
    }
    
    /**
     * Wrap async function with timeout
     */
    async withTimeout(promise, timeoutMs = this.OPERATION_TIMEOUT, operationName = 'Operation') {
        return Promise.race([
            promise,
            new Promise((_, reject) =>
                setTimeout(() => reject(new Error(`${operationName} timed out after ${timeoutMs/1000}s`)), timeoutMs)
            )
        ]);
    }

    /**
     * Initialize all AI models
     */
    async initialize() {
        console.log('🤖 Initializing AI Verification System...');
        
        try {
            // Load Face-API.js models
            await this.loadFaceAPIModels();
            
            // Load Google Teachable Machine model
            await this.loadTeachableMachineModel();
            
            console.log('✅ AI Models loaded successfully');
            return true;
        } catch (error) {
            console.error('❌ Failed to load AI models:', error);
            return false;
        }
    }

    /**
     * Load Face-API.js models for face detection and recognition
     */
    async loadFaceAPIModels() {
        if (typeof faceapi === 'undefined') {
            throw new Error('Face-API.js not loaded. Include face-api.min.js in your HTML');
        }

        const MODEL_URL = '/models/face-api';
        
        // Load only the models we need for ID verification
        await Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
            faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
            faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)
            // Note: faceExpressionNet removed - not needed for ID verification
        ]);

        this.faceApiModelsLoaded = true;
        console.log('✓ Face-API models loaded');
    }

    /**
     * Load Google Teachable Machine model for ID classification
     * OPTIONAL: Only loads if library and model are available
     */
    async loadTeachableMachineModel() {
        if (typeof tmImage === 'undefined') {
            console.warn('⚠ Teachable Machine library not loaded - skipping ID classification (optional feature)');
            return; // Don't throw error - it's optional
        }

        // TODO: Replace with your actual Teachable Machine model URL
        const MODEL_URL = '/models/teachable-machine/model.json';
        const METADATA_URL = '/models/teachable-machine/metadata.json';

        try {
            this.teachableMachineModel = await tmImage.load(MODEL_URL, METADATA_URL);
            console.log('✓ Teachable Machine model loaded');
        } catch (error) {
            console.warn('⚠ Teachable Machine model not found. Skipping ID classification (optional feature).');
            // Continue without TM model - we'll use Face-API only for basic verification
        }
    }

    /**
     * MAIN VERIFICATION FUNCTION
     * Verifies ID front, back, and selfie images
     */
    async verifyImages(idFrontImg, idBackImg, selfieImg) {
        console.log('Starting AI verification...');
        const results = {};

        try {
            // Step 1: Detect faces in images (with 10s timeout per image)
            console.log('Step 1: Detecting faces...');
            const idFrontFace = await this.withTimeout(
                this.detectFace(idFrontImg),
                10000,
                'ID face detection'
            );
            const selfieFace = await this.withTimeout(
                this.detectFace(selfieImg),
                10000,
                'Selfie face detection'
            );

            if (!idFrontFace) {
                results.error = 'No face detected in ID photo';
                results.status = 'failed';
                return results;
            }

            if (!selfieFace) {
                results.error = 'No face detected in selfie';
                results.status = 'failed';
                return results;
            }

            // Step 2: Compare faces (Face Matching)
            console.log('Step 2: Comparing faces...');
            const faceMatchScore = await this.compareFaces(idFrontFace, selfieFace);
            results.faceMatchScore = faceMatchScore;

            // Step 3: Check ID authenticity with Teachable Machine
            console.log('Step 3: Checking ID authenticity...');
            if (this.teachableMachineModel) {
                const authenticityScore = await this.checkIDAuthenticity(idFrontImg);
                results.idAuthenticityScore = authenticityScore;
            } else {
                results.idAuthenticityScore = 50; // Neutral score if model not available
                results.notes = results.notes || [];
                results.notes.push('ID authenticity check skipped (model not loaded)');
            }

            // Step 4: Liveness detection (basic - check for blur, brightness)
            console.log('Step 4: Checking liveness...');
            const livenessScore = await this.checkLiveness(selfieImg);
            results.livenessScore = livenessScore;

            // Step 5: Calculate perceptual hashes for duplicate detection
            console.log('Step 5: Generating image hashes...');
            results.idFrontHash = await this.calculatePerceptualHash(idFrontImg);
            results.idBackHash = await this.calculatePerceptualHash(idBackImg);
            results.selfieHash = await this.calculatePerceptualHash(selfieImg);

            // Step 6: Determine overall status
            results.overallConfidence = this.calculateOverallConfidence(results);
            results.status = this.determineVerificationStatus(results);

            console.log('AI Verification complete:', results);
            return results;

        } catch (error) {
            console.error('AI Verification failed:', error);
            return {
                error: error.message,
                status: 'failed',
                notes: ['AI verification encountered an error - manual review required']
            };
        }
    }

    /**
     * Detect face in an image using Face-API.js
     */
    async detectFace(imgElement) {
        if (!this.faceApiModelsLoaded) {
            throw new Error('Face-API models not loaded');
        }

        try {
            // Create canvas from image for better compatibility
            const canvas = document.createElement('canvas');
            canvas.width = imgElement.naturalWidth || imgElement.width;
            canvas.height = imgElement.naturalHeight || imgElement.height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(imgElement, 0, 0);
            
            console.log(`Detecting face in image (${canvas.width}x${canvas.height})...`);
            
            // Try with the simplest possible options first
            let detections = null;
            
            try {
                // Method 1: Use TinyFaceDetector with basic options
                detections = await faceapi
                    .detectSingleFace(canvas, new faceapi.TinyFaceDetectorOptions())
                    .withFaceLandmarks()
                    .withFaceDescriptor();
            } catch (e) {
                console.warn('TinyFaceDetector failed, trying alternative...', e);
                
                // Method 2: Try without custom options
                try {
                    detections = await faceapi
                        .detectSingleFace(canvas)
                        .withFaceLandmarks()
                        .withFaceDescriptor();
                } catch (e2) {
                    console.error('All detection methods failed', e2);
                    throw e2;
                }
            }

            if (!detections) {
                throw new Error('No face detected in image');
            }
            
            console.log('✓ Face detected successfully');
            return detections;
            
        } catch (error) {
            console.error('Face detection error:', error);
            throw new Error(`Face detection failed: ${error.message}`);
        }
    }

    /**
     * Compare two faces and return similarity score (0-100)
     */
    async compareFaces(face1, face2) {
        if (!face1 || !face2 || !face1.descriptor || !face2.descriptor) {
            throw new Error('Invalid face data for comparison');
        }

        // Calculate Euclidean distance
        const distance = faceapi.euclideanDistance(face1.descriptor, face2.descriptor);
        
        // Convert distance to similarity score (0-100)
        // Lower distance = higher similarity
        // Distance typically ranges from 0 to 1
        const similarityScore = Math.max(0, Math.min(100, (1 - distance) * 100));

        return Math.round(similarityScore * 100) / 100; // Round to 2 decimals
    }

    /**
     * Check ID authenticity using Google Teachable Machine
     */
    async checkIDAuthenticity(imgElement) {
        if (!this.teachableMachineModel) {
            console.warn('Teachable Machine model not available');
            return 50; // Neutral score
        }

        const predictions = await this.teachableMachineModel.predict(imgElement);
        
        // Find the "authentic" or "real_id" class prediction
        // Adjust class names based on your training
        const authenticClass = predictions.find(p => 
            p.className.toLowerCase().includes('authentic') || 
            p.className.toLowerCase().includes('real') ||
            p.className.toLowerCase().includes('valid')
        );

        if (authenticClass) {
            return Math.round(authenticClass.probability * 100 * 100) / 100;
        }

        // If no specific class found, use highest confidence
        const highestConfidence = Math.max(...predictions.map(p => p.probability));
        return Math.round(highestConfidence * 100 * 100) / 100;
    }

    /**
     * Basic liveness detection (check image quality)
     * Optimized for speed
     */
    async checkLiveness(imgElement) {
        // Create smaller canvas for faster processing
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        
        // Downscale to 200x200 for speed
        const maxSize = 200;
        const scale = Math.min(maxSize / imgElement.width, maxSize / imgElement.height);
        canvas.width = imgElement.width * scale;
        canvas.height = imgElement.height * scale;
        ctx.drawImage(imgElement, 0, 0, canvas.width, canvas.height);

        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const data = imageData.data;

        // Calculate average brightness (sample every 4th pixel for speed)
        let totalBrightness = 0;
        let sampleCount = 0;
        for (let i = 0; i < data.length; i += 16) { // Sample every 4th pixel
            const brightness = (data[i] + data[i + 1] + data[i + 2]) / 3;
            totalBrightness += brightness;
            sampleCount++;
        }
        const avgBrightness = totalBrightness / sampleCount;

        // Simple liveness score based on brightness
        // Good selfies typically have brightness between 80-200
        let score = 100;
        if (avgBrightness < 50) score -= 30; // Too dark
        if (avgBrightness > 240) score -= 20; // Too bright/overexposed
        if (avgBrightness >= 80 && avgBrightness <= 200) score = 100; // Ideal

        // TODO: Add blur detection, edge detection for better liveness
        
        return Math.max(0, Math.min(100, score));
    }

    /**
     * Calculate perceptual hash for duplicate detection
     * Simple implementation - you may want to use a library for production
     */
    async calculatePerceptualHash(imgElement) {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        
        // Resize to 8x8 for hashing
        canvas.width = 8;
        canvas.height = 8;
        ctx.drawImage(imgElement, 0, 0, 8, 8);

        const imageData = ctx.getImageData(0, 0, 8, 8);
        const data = imageData.data;

        // Calculate average grayscale value
        let total = 0;
        const pixels = [];
        for (let i = 0; i < data.length; i += 4) {
            const gray = (data[i] + data[i + 1] + data[i + 2]) / 3;
            pixels.push(gray);
            total += gray;
        }
        const average = total / pixels.length;

        // Create hash: 1 if pixel > average, 0 otherwise
        let hash = '';
        for (const pixel of pixels) {
            hash += pixel > average ? '1' : '0';
        }

        // Convert binary to hex
        let hexHash = '';
        for (let i = 0; i < hash.length; i += 4) {
            const chunk = hash.substr(i, 4);
            hexHash += parseInt(chunk, 2).toString(16);
        }

        return hexHash;
    }

    /**
     * Calculate overall confidence score
     */
    calculateOverallConfidence(results) {
        const { faceMatchScore = 0, idAuthenticityScore = 0, livenessScore = 0 } = results;
        
        // Weighted average
        const weights = {
            faceMatch: 0.5,        // 50% weight on face matching
            authenticity: 0.3,     // 30% weight on ID authenticity
            liveness: 0.2          // 20% weight on liveness
        };

        const confidence = (
            (faceMatchScore * weights.faceMatch) +
            (idAuthenticityScore * weights.authenticity) +
            (livenessScore * weights.liveness)
        );

        return Math.round(confidence * 100) / 100;
    }

    /**
     * Determine verification status based on scores
     */
    determineVerificationStatus(results) {
        const { faceMatchScore, idAuthenticityScore, livenessScore, overallConfidence } = results;

        // Auto-reject if any critical threshold not met
        if (faceMatchScore < this.THRESHOLDS.FACE_MATCH_MIN) {
            return 'failed';
        }

        // Auto-approve if all scores are high
        if (
            overallConfidence >= this.THRESHOLDS.AUTO_APPROVE_MIN &&
            faceMatchScore >= this.THRESHOLDS.FACE_MATCH_MIN &&
            idAuthenticityScore >= this.THRESHOLDS.ID_AUTHENTICITY_MIN &&
            livenessScore >= this.THRESHOLDS.LIVENESS_MIN
        ) {
            return 'passed';
        }

        // Manual review for borderline cases
        return 'manual_review';
    }

    /**
     * Display results to user (UI feedback)
     */
    displayResults(results) {
        console.log('Verification Results:', results);
        
        // You can create UI elements to show this to the user
        // For now, just return formatted message
        if (results.status === 'passed') {
            return {
                success: true,
                message: 'ID verification passed! Your registration is being processed.',
                scores: results
            };
        } else if (results.status === 'failed') {
            return {
                success: false,
                message: `ID verification failed: ${results.error || 'Face does not match ID photo'}`,
                scores: results
            };
        } else {
            return {
                success: true,
                message: 'Your ID is under review. You\'ll be notified once verified.',
                scores: results
            };
        }
    }
}

// Export for use
window.AIIDVerification = AIIDVerification;

