<?php

namespace App\Http\Controllers;

use App\Models\Parcel;
use App\Services\RiskAnalysisService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ParcelController extends Controller
{
    public function index()
    {
        /*
         * Only show parcels that belong to the currently
         * authenticated user.
         *
         * The auth middleware on the route guarantees a user
         * is present, so $request->user() is safe here.
         */
        $parcels = Parcel::where('user_id', request()->user()->id)
            ->latest()
            ->get();

        return view('landsure.parcels', [
            'parcels' => $parcels,
        ]);
    }

    public function show(Parcel $parcel)
    {
        /*
         * Refuse to display a parcel that does not belong to
         * the currently authenticated user.
         *
         * 404 rather than 403 so we do not confirm to an
         * unauthorized visitor that this parcel exists.
         */
        if ($parcel->user_id !== request()->user()->id) {
            abort(404);
        }

        /*
         * Automatically run the risk analysis when a saved
         * parcel is opened.
         *
         * The service will update the existing analysis if one
         * already exists.
         */
        $riskAnalysis = app(RiskAnalysisService::class)
            ->analyze($parcel);

        return view('landsure.parcel', [
            'parcel' => $parcel,
            'riskAnalysis' => $riskAnalysis,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'area_square_meters' => ['required', 'numeric', 'min:0'],
            'area_acres' => ['required', 'numeric', 'min:0'],
            'boundary' => ['required', 'array'],
        ]);

        /*
         * Attach the parcel to the authenticated user.
         *
         * The parcels table has a NOT NULL user_id column, so
         * this is required. Because the route is behind the
         * "auth" middleware, $request->user() will always be
         * a valid user here.
         */
        $validated['user_id'] = $request->user()->id;

        $parcel = Parcel::create($validated);

        /*
         * Run the first LandSure risk analysis immediately
         * after the parcel is saved.
         */
        $riskAnalysis = app(RiskAnalysisService::class)
            ->analyze($parcel);

        return response()->json([
            'success' => true,
            'message' => 'Land parcel saved and analyzed successfully.',
            'parcel' => $parcel,
            'risk_analysis' => $riskAnalysis,
        ], 201);
    }
}