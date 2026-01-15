<?php

namespace App\Services;

use App\Models\User;
use App\Models\Facility;
use App\Models\EquipmentItem;

/**
 * Pricing Calculator Service
 * 
 * Handles all pricing calculations with two-tier discount system:
 * - Tier 1: City Residency Discount (30% for Caloocan residents)
 * - Tier 2: Identity-Based Discount (20% for Senior/PWD/Student - applied after city discount)
 */
class PricingCalculatorService
{
    // Discount Percentages (as per specification)
    const CITY_RESIDENT_DISCOUNT = 30.00; // 30% for Caloocan residents
    const IDENTITY_DISCOUNT = 20.00; // 20% for Senior/PWD/Student
    
    // ID Types that qualify for identity discount
    const IDENTITY_DISCOUNT_TYPES = ['senior', 'pwd', 'student'];
    
    /**
     * Calculate complete pricing for a booking
     * 
     * @param User $user The user making the booking
     * @param Facility $facility The facility being booked
     * @param array $equipmentItems Array of ['equipment_item_id' => quantity]
     * @param string $selectedIdType The ID type selected (senior, pwd, student, regular)
     * @return array Complete pricing breakdown
     */
    public function calculateBookingPrice(
        User $user, 
        Facility $facility, 
        array $equipmentItems = [], 
        string $selectedIdType = 'regular'
    ): array {
        // Step 1: Get base facility rate
        $facilityBaseRate = $facility->facility_rate ?? 5000.00;
        
        // Step 2: Calculate equipment total
        $equipmentTotal = $this->calculateEquipmentTotal($equipmentItems);
        
        // Step 3: Calculate subtotal (facility + equipment)
        $subtotal = $facilityBaseRate;
        
        // Step 4: Apply City Residency Discount (Tier 1)
        $cityDiscountPercentage = 0;
        $cityDiscountAmount = 0;
        $afterCityDiscount = $subtotal;
        
        if ($user->is_caloocan_resident) {
            $cityDiscountPercentage = self::CITY_RESIDENT_DISCOUNT;
            $cityDiscountAmount = $this->calculatePercentage($subtotal, $cityDiscountPercentage);
            $afterCityDiscount = $subtotal - $cityDiscountAmount;
        }
        
        // Step 5: Apply Identity-Based Discount (Tier 2) - only on facility rate, after city discount
        $identityDiscountType = null;
        $identityDiscountPercentage = 0;
        $identityDiscountAmount = 0;
        $afterIdentityDiscount = $afterCityDiscount;
        
        if (in_array($selectedIdType, self::IDENTITY_DISCOUNT_TYPES)) {
            $identityDiscountType = $selectedIdType;
            $identityDiscountPercentage = self::IDENTITY_DISCOUNT;
            $identityDiscountAmount = $this->calculatePercentage($afterCityDiscount, $identityDiscountPercentage);
            $afterIdentityDiscount = $afterCityDiscount - $identityDiscountAmount;
        }
        
        // Step 6: Add equipment (no discounts on equipment)
        $finalTotal = $afterIdentityDiscount + $equipmentTotal;
        
        // Step 7: Calculate total savings
        $totalSavings = $cityDiscountAmount + $identityDiscountAmount;
        
        // Return comprehensive breakdown
        return [
            'facility_base_rate' => round($facilityBaseRate, 2),
            'subtotal' => round($subtotal, 2),
            'equipment_total' => round($equipmentTotal, 2),
            
            // City Discount (Tier 1)
            'city_discount_percentage' => $cityDiscountPercentage,
            'city_discount_amount' => round($cityDiscountAmount, 2),
            'after_city_discount' => round($afterCityDiscount, 2),
            'is_city_resident' => $user->is_caloocan_resident,
            'city_name' => $user->city,
            
            // Identity Discount (Tier 2)
            'identity_discount_type' => $identityDiscountType,
            'identity_discount_percentage' => $identityDiscountPercentage,
            'identity_discount_amount' => round($identityDiscountAmount, 2),
            'after_identity_discount' => round($afterIdentityDiscount, 2),
            
            // Final Totals
            'total_savings' => round($totalSavings, 2),
            'final_total' => round($finalTotal, 2),
            
            // Equipment Breakdown
            'equipment_details' => $this->getEquipmentBreakdown($equipmentItems),
            
            // Metadata
            'selected_id_type' => $selectedIdType,
            'calculation_date' => now()->toDateTimeString()
        ];
    }
    
    /**
     * Calculate equipment rental total
     * 
     * @param array $equipmentItems Array of ['equipment_item_id' => quantity]
     * @return float Total equipment cost
     */
    private function calculateEquipmentTotal(array $equipmentItems): float
    {
        if (empty($equipmentItems)) {
            return 0.00;
        }
        
        $total = 0;
        
        foreach ($equipmentItems as $equipmentId => $quantity) {
            $equipment = EquipmentItem::find($equipmentId);
            
            if ($equipment && $equipment->is_available) {
                $total += $equipment->price_per_unit * $quantity;
            }
        }
        
        return $total;
    }
    
    /**
     * Get detailed equipment breakdown
     * 
     * @param array $equipmentItems Array of ['equipment_item_id' => quantity]
     * @return array Equipment breakdown with names and prices
     */
    private function getEquipmentBreakdown(array $equipmentItems): array
    {
        if (empty($equipmentItems)) {
            return [];
        }
        
        $breakdown = [];
        
        foreach ($equipmentItems as $equipmentId => $quantity) {
            $equipment = EquipmentItem::find($equipmentId);
            
            if ($equipment && $equipment->is_available) {
                $breakdown[] = [
                    'equipment_id' => $equipment->id,
                    'name' => $equipment->name,
                    'category' => $equipment->category,
                    'quantity' => $quantity,
                    'price_per_unit' => round($equipment->price_per_unit, 2),
                    'subtotal' => round($equipment->price_per_unit * $quantity, 2)
                ];
            }
        }
        
        return $breakdown;
    }
    
    /**
     * Calculate percentage of a value
     * 
     * @param float $value The base value
     * @param float $percentage The percentage to calculate
     * @return float The calculated percentage amount
     */
    private function calculatePercentage(float $value, float $percentage): float
    {
        return ($value * $percentage) / 100;
    }
    
    /**
     * Preview pricing for a user (before booking)
     * 
     * @param User $user The user
     * @param float $facilityRate The facility base rate
     * @param string $selectedIdType The ID type they plan to use
     * @return array Pricing preview
     */
    public function previewPricing(User $user, float $facilityRate, string $selectedIdType = 'regular'): array
    {
        $subtotal = $facilityRate;
        
        // City discount
        $cityDiscountAmount = 0;
        if ($user->is_caloocan_resident) {
            $cityDiscountAmount = $this->calculatePercentage($subtotal, self::CITY_RESIDENT_DISCOUNT);
        }
        $afterCityDiscount = $subtotal - $cityDiscountAmount;
        
        // Identity discount
        $identityDiscountAmount = 0;
        if (in_array($selectedIdType, self::IDENTITY_DISCOUNT_TYPES)) {
            $identityDiscountAmount = $this->calculatePercentage($afterCityDiscount, self::IDENTITY_DISCOUNT);
        }
        
        $finalTotal = $afterCityDiscount - $identityDiscountAmount;
        $totalSavings = $cityDiscountAmount + $identityDiscountAmount;
        
        return [
            'original_price' => round($subtotal, 2),
            'city_discount' => round($cityDiscountAmount, 2),
            'identity_discount' => round($identityDiscountAmount, 2),
            'total_savings' => round($totalSavings, 2),
            'final_price' => round($finalTotal, 2),
            'savings_percentage' => $subtotal > 0 ? round(($totalSavings / $subtotal) * 100, 2) : 0
        ];
    }
    
    /**
     * Validate if a user qualifies for a discount
     * 
     * @param User $user The user
     * @param string $idType The ID type to validate
     * @return array Validation result
     */
    public function validateDiscountEligibility(User $user, string $idType): array
    {
        $eligibility = [
            'is_eligible' => false,
            'discount_type' => null,
            'discount_percentage' => 0,
            'reasons' => []
        ];
        
        // Check city residency
        if ($user->is_caloocan_resident) {
            $eligibility['is_eligible'] = true;
            $eligibility['reasons'][] = "Caloocan City resident: {$this->CITY_RESIDENT_DISCOUNT}% discount";
        }
        
        // Check identity discount
        if (in_array($idType, self::IDENTITY_DISCOUNT_TYPES)) {
            $eligibility['is_eligible'] = true;
            $eligibility['discount_type'] = $idType;
            $eligibility['discount_percentage'] += self::IDENTITY_DISCOUNT;
            $eligibility['reasons'][] = ucfirst($idType) . " ID: {$this->IDENTITY_DISCOUNT}% discount";
        }
        
        return $eligibility;
    }
}

