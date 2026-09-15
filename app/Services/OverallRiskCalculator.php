<?php

namespace App\Services;

/**
 * Overall Risk Calculator
 *
 * This class implements the LandSure Overall Risk Methodology.
 *
 * The authoritative design document is stored at:
 *     docs/risk-methodology.md
 *
 * Any change to the combining rule must be reflected first in that
 * document, and then implemented here.
 *
 * Summary of the rule (see the document for the full definition):
 *
 *   - Boundary is a data quality check and never contributes to
 *     overall risk severity.
 *
 *   - Any connected risk check returning high or very_high makes
 *     the overall high.
 *
 *   - Otherwise, any connected risk check returning moderate makes
 *     the overall moderate.
 *
 *   - Otherwise, any connected risk check returning unknown makes
 *     the overall unknown.
 *
 *   - Otherwise, if every connected risk check returns low, the
 *     overall is low.
 *
 *   - Otherwise, the overall is unknown as a safety net.
 *
 * The priority order is therefore:
 *
 *   high > moderate > unknown > low
 *
 * Note the deliberate placement of unknown above low. LandSure must
 * never claim low risk while any connected check is still unknown.
 */
class OverallRiskCalculator
{
    /**
     * Risk checks that participate in the overall status.
     *
     * Boundary is intentionally not in this list. It is a data
     * quality check, not a risk severity check.
     */
    public const RISK_CHECKS = [
        'flood',
        'buffer',
        'planning',
        'land',
    ];

    /**
     * Statuses considered as high risk.
     */
    public const HIGH_STATUSES = [
        'high',
        'very_high',
    ];

    /**
     * Statuses considered as moderate risk.
     */
    public const MODERATE_STATUSES = [
        'moderate',
        'medium',
    ];

    /**
     * Statuses considered as low risk.
     */
    public const LOW_STATUSES = [
        'low',
        'good',
    ];

    /**
     * Compute the overall status from a set of check statuses.
     *
     * @param array<string, string|null> $checkStatuses
     *     Associative array of check name to status, for example
     *     ['flood' => 'unknown', 'buffer' => 'low'].
     *
     *     Only the checks listed in RISK_CHECKS are considered.
     *     Any other keys are ignored so that data quality checks
     *     such as boundary never affect the result.
     *
     * @return string
     *     One of: high, moderate, unknown, low.
     */
    public function compute(array $checkStatuses): string
    {
        $anyHigh = false;
        $anyModerate = false;
        $anyUnknown = false;

        foreach (self::RISK_CHECKS as $checkName) {

            $status = $checkStatuses[$checkName] ?? null;

            $status = $this->normalize($status);

            if ($status === null) {

                /*
                 * If a risk check is missing entirely, we cannot
                 * assume low. Treat it as unknown.
                 */
                $anyUnknown = true;
                continue;
            }

            if (in_array($status, self::HIGH_STATUSES, true)) {
                $anyHigh = true;
                continue;
            }

            if (in_array($status, self::MODERATE_STATUSES, true)) {
                $anyModerate = true;
                continue;
            }

            if ($status === 'unknown') {
                $anyUnknown = true;
                continue;
            }

            /*
             * Low statuses fall through without setting any flag.
             * We do not track "allLow" directly. The absence of any
             * higher flag implies all connected checks were low.
             */
        }

        if ($anyHigh) {
            return 'high';
        }

        if ($anyModerate) {
            return 'moderate';
        }

        if ($anyUnknown) {
            return 'unknown';
        }

        return 'low';
    }

    /**
     * Normalize a raw status value.
     *
     * Returns a lowercase trimmed string, or null if the value is
     * empty or not usable.
     */
    protected function normalize($status): ?string
    {
        if ($status === null) {
            return null;
        }

        if (!is_string($status)) {
            return null;
        }

        $status = strtolower(trim($status));

        if ($status === '') {
            return null;
        }

        return $status;
    }
}