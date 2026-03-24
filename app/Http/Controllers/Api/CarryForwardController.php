<?php

namespace App\Http\Controllers\Api;

use App\Models\MonthlyHarvest;
use App\Models\PollenProduction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * API controller for carry-forward logic.
 * Returns prior month data for MonthlyHarvest and PollenProduction.
 */
class CarryForwardController extends Controller
{
    /**
     * GET /api/carry-forward/harvest?field_site_id=X&year=YYYY&month=M
     *
     * Returns the most recent harvest records for the given field site
     * from the previous month, so data can be auto-populated.
     */
    public function harvest(Request $request): JsonResponse
    {
        $request->validate([
            'field_site_id' => 'required|integer|exists:field_sites,id',
            'year' => 'required|integer|min:2020',
            'month' => 'required|integer|min:1|max:12',
        ]);

        $targetDate = \Carbon\Carbon::create($request->year, $request->month, 1);
        $prevMonth = $targetDate->copy()->subMonth();

        $records = MonthlyHarvest::withoutGlobalScopes()
            ->where('field_site_id', $request->field_site_id)
            ->whereYear('report_month', $prevMonth->year)
            ->whereMonth('report_month', $prevMonth->month)
            ->with('varieties')
            ->get()
            ->map(fn ($record) => [
                'location' => $record->location,
                'farm_name' => $record->farm_name,
                'area_ha' => $record->area_ha,
                'age_of_palms' => $record->age_of_palms,
                'num_hybridized_palms' => $record->num_hybridized_palms,
                'variety' => $record->variety,
                'seednuts_produced' => $record->seednuts_produced,
                'varieties' => $record->varieties->map(fn ($v) => [
                    'variety' => $v->variety,
                    'seednuts_type' => $v->seednuts_type,
                    'seednuts_count' => $v->seednuts_count,
                ]),
            ]);

        return response()->json([
            'success' => true,
            'previous_month' => $prevMonth->format('F Y'),
            'records' => $records,
        ]);
    }

    /**
     * GET /api/carry-forward/pollen?field_site_id=X&year=YYYY&month=M
     *
     * Returns the ending balance from the previous month's pollen records
     * to auto-populate the "ending_balance_prev" field.
     */
    public function pollen(Request $request): JsonResponse
    {
        $request->validate([
            'field_site_id' => 'required|integer|exists:field_sites,id',
            'year' => 'required|integer|min:2020',
            'month' => 'required|integer|min:1|max:12',
        ]);

        $targetDate = \Carbon\Carbon::create($request->year, $request->month, 1);
        $prevMonth = $targetDate->copy()->subMonth();

        $records = PollenProduction::withoutGlobalScopes()
            ->where('field_site_id', $request->field_site_id)
            ->whereYear('report_month', $prevMonth->year)
            ->whereMonth('report_month', $prevMonth->month)
            ->get()
            ->map(fn ($record) => [
                'pollen_variety' => $record->pollen_variety,
                'ending_balance' => $record->ending_balance,
            ]);

        return response()->json([
            'success' => true,
            'previous_month' => $prevMonth->format('F Y'),
            'records' => $records,
        ]);
    }
}
