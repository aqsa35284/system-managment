<?php

namespace App\Support;

class PlanFeatures
{
    public static function aliases(): array
    {
        return config('features.aliases', []);
    }

    public static function normalize(string $slug): string
    {
        return self::aliases()[$slug] ?? $slug;
    }

    public static function forPlan(string $planSlug): array
    {
        return config("features.tiers.{$planSlug}", []);
    }

    public static function matches(string $requested, array $enabledFeatures): bool
    {
        $normalized = self::normalize($requested);

        if (in_array($requested, $enabledFeatures, true) || in_array($normalized, $enabledFeatures, true)) {
            return true;
        }

        if ($requested === 'team_chat') {
            return in_array('team_chat_limited', $enabledFeatures, true);
        }

        foreach (self::aliases() as $legacy => $canonical) {
            if ($canonical === $normalized && in_array($legacy, $enabledFeatures, true)) {
                return true;
            }
        }

        return false;
    }

    public static function syncOfficialPlans(): void
    {
        $pricing = config('features.pricing', []);
        $tiers   = config('features.tiers', []);

        foreach ($pricing as $slug => $meta) {
            \App\Models\SubscriptionPlan::updateOrCreate(
                ['slug' => $slug],
                [
                    'name'            => $meta['name'],
                    'price'           => $meta['price'],
                    'extra_user_rate' => $meta['extra_user_rate'],
                    'retention_days'  => $meta['retention_days'],
                    'features'        => $tiers[$slug] ?? [],
                ]
            );
        }
    }
}
