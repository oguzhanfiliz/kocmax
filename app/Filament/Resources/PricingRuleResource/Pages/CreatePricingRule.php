<?php

namespace App\Filament\Resources\PricingRuleResource\Pages;

use App\Filament\Resources\PricingRuleResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePricingRule extends CreateRecord
{
    protected static string $resource = PricingRuleResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Kullanıcı dostu form verilerini JSON formatına dönüştür
        $conditions = [];
        $actions = [];

        // Customer types
        if (!empty($data['customer_types'])) {
            $conditions['customer_types'] = $data['customer_types'];
        }

        // Quantity conditions
        if (!empty($data['min_quantity'])) {
            $conditions['min_quantity'] = (int) $data['min_quantity'];
        }
        if (!empty($data['max_quantity'])) {
            $conditions['max_quantity'] = (int) $data['max_quantity'];
        }

        // Amount conditions  
        if (!empty($data['min_amount'])) {
            $conditions['min_amount'] = (float) $data['min_amount'];
        }
        if (!empty($data['max_amount'])) {
            $conditions['max_amount'] = (float) $data['max_amount'];
        }

        // Days of week
        if (!empty($data['days_of_week'])) {
            $conditions['days_of_week'] = array_map('intval', $data['days_of_week']);
        }

        // Advanced conditions
        if (!empty($data['advanced_conditions'])) {
            $advancedConditions = json_decode($data['advanced_conditions'], true);
            if ($advancedConditions) {
                $conditions = array_merge($conditions, $advancedConditions);
            }
        }

        // Discount actions
        if (!empty($data['discount_type']) && !empty($data['discount_value'])) {
            if ($data['discount_type'] === 'percentage') {
                $actions['discount_percentage'] = (float) $data['discount_value'];
            } elseif ($data['discount_type'] === 'fixed_amount') {
                $actions['discount_amount'] = (float) $data['discount_value'];
            }
        }

        // Advanced actions
        if (!empty($data['advanced_actions'])) {
            $advancedActions = json_decode($data['advanced_actions'], true);
            if ($advancedActions) {
                $actions = array_merge($actions, $advancedActions);
            }
        }

        // Set the JSON data
        $data['conditions'] = $conditions;
        $data['actions'] = $actions;

        // Clean up temporary fields
        unset(
            $data['customer_types'],
            $data['min_quantity'],
            $data['max_quantity'], 
            $data['min_amount'],
            $data['max_amount'],
            $data['days_of_week'],
            $data['discount_type'],
            $data['discount_value'],
            $data['advanced_conditions'],
            $data['advanced_actions']
        );

        $data['created_by'] = auth()->id();
        return $data;
    }
}