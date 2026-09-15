<?php

namespace App\Services;

use App\Models\Parcel;
use App\Models\RiskAnalysis;
use Illuminate\Support\Carbon;

class RiskAnalysisService
{
    /**
     * Analyze a parcel and create/update its risk analysis.
     */
    public function analyze(Parcel $parcel): RiskAnalysis
    {
        $boundary = $this->analyzeBoundary($parcel);

        /*
         * Run the local flood-data analysis.
         *
         * If the flood analyzer cannot run, we keep the
         * flood result as UNKNOWN rather than pretending
         * the parcel is safe.
         */
        $flood = $this->analyzeFlood($parcel);

        /*
         * Run the local waterway/buffer analysis.
         *
         * This uses the locally stored HydroRIVERS Ghana
         * dataset and the LandSure prototype screening
         * distances.
         */
        $buffer = $this->analyzeBuffer($parcel);

        $riskAnalysis = RiskAnalysis::updateOrCreate(
            [
                'parcel_id' => $parcel->id,
            ],
            [
                /*
                 * Overall risk remains UNKNOWN for now because
                 * planning and official land-status checks are
                 * not yet connected.
                 */
                'overall_status' => 'unknown',
                'overall_score' => null,

                'flood_status' => $flood['status'],
                'flood_score' => $flood['score'],

                'buffer_status' => $buffer['status'],
                'buffer_score' => $buffer['score'],

                'planning_status' => 'unknown',
                'planning_score' => null,

                'land_status' => 'unknown',
                'land_score' => null,

                'boundary_status' => $boundary['status'],
                'boundary_score' => $boundary['score'],

                'findings' => [
                    'boundary' => $boundary['finding'],

                    'flood' => $flood['finding'],

                    'buffer' => $buffer['finding'],

                    'planning' => [
                        'status' => 'unknown',
                        'message' => 'Planning and zoning data is not connected yet.',
                    ],

                    'land' => [
                        'status' => 'unknown',
                        'message' => 'Official land-status data is not connected yet.',
                    ],
                ],

                'sources' => [
                    'boundary' => [
                        'type' => 'user_submitted',
                        'description' => 'Boundary supplied through the LandSure parcel checker.',
                    ],

                    'flood' => $flood['source'],

                    'buffer' => $buffer['source'],
                ],

                'analyzed_at' => Carbon::now(),
            ]
        );

        return $riskAnalysis;
    }


    /**
     * Analyze flood hazard using the local Python/Rasterio analyzer.
     */
    protected function analyzeFlood(Parcel $parcel): array
    {
        $rasterPath = storage_path(
            'app/flood-data/fl_hazard_100_yrp.tif'
        );

        $scriptPath = base_path(
            'scripts/flood_analyzer.py'
        );

        /*
         * If either required file is missing, return UNKNOWN.
         */
        if (!file_exists($rasterPath)) {
            return $this->unknownFloodResult(
                'The local flood-hazard raster could not be found.'
            );
        }

        if (!file_exists($scriptPath)) {
            return $this->unknownFloodResult(
                'The local flood analyzer script could not be found.'
            );
        }

        /*
         * The parcel boundary is already stored as GeoJSON
         * in the database and cast to an array by the model.
         */
        $boundary = $parcel->boundary;

        if (
            !is_array($boundary) ||
            empty($boundary)
        ) {
            return $this->unknownFloodResult(
                'No valid parcel boundary was supplied for the flood check.'
            );
        }

        /*
         * Convert the parcel GeoJSON to JSON for Python.
         */
        $parcelJson = json_encode(
            $boundary,
            JSON_UNESCAPED_SLASHES
        );

        if ($parcelJson === false) {
            return $this->unknownFloodResult(
                'The parcel boundary could not be converted to JSON.'
            );
        }

        /*
         * Windows Python executable.
         *
         * "python" works in the CMD environment we already tested.
         */
        $python = 'python';

        /*
         * Build the command.
         *
         * The parcel GeoJSON is sent through STDIN.
         */
        $command =
            $python
            . ' '
            . escapeshellarg($scriptPath)
            . ' '
            . escapeshellarg($rasterPath);

        /*
         * Open the Python process.
         */
        $descriptorspec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open(
            $command,
            $descriptorspec,
            $pipes,
            base_path()
        );

        if (!is_resource($process)) {
            return $this->unknownFloodResult(
                'LandSure could not start the local flood analyzer.'
            );
        }

        /*
         * Send GeoJSON to Python.
         */
        fwrite(
            $pipes[0],
            $parcelJson
        );

        fclose(
            $pipes[0]
        );

        /*
         * Read Python output.
         */
        $output = stream_get_contents(
            $pipes[1]
        );

        fclose(
            $pipes[1]
        );

        /*
         * Read any Python errors.
         */
        $errorOutput = stream_get_contents(
            $pipes[2]
        );

        fclose(
            $pipes[2]
        );

        $exitCode = proc_close(
            $process
        );

        /*
         * Python failed.
         */
        if ($exitCode !== 0) {
            return $this->unknownFloodResult(
                'The local flood analyzer returned an error.'
                . (
                    trim($errorOutput) !== ''
                        ? ' ' . trim($errorOutput)
                        : ''
                )
            );
        }

        /*
         * Decode Python JSON.
         */
        $result = json_decode(
            $output,
            true
        );

        if (!is_array($result)) {
            return $this->unknownFloodResult(
                'The flood analyzer returned an invalid response.'
            );
        }

        /*
         * Python itself reported failure.
         */
        if (($result['success'] ?? false) !== true) {
            return $this->unknownFloodResult(
                $result['message']
                    ?? $result['error']
                    ?? 'The flood analyzer could not analyze this parcel.'
            );
        }

        $status = $result['status'] ?? 'unknown';

        $data = $result['data'] ?? [];

        $source = $result['source'] ?? [];

        /*
         * Convert the flood classification to a score.
         *
         * These are LandSure prototype scores only.
         *
         * low       = 100
         * moderate  = 65
         * high      = 30
         * very_high = 0
         *
         * Unknown remains NULL.
         */
        $score = match ($status) {
            'low' => 100,
            'moderate' => 65,
            'high' => 30,
            'very_high' => 0,
            default => null,
        };

        /*
         * Build the finding stored in the database.
         */
        $finding = [
            'status' => $status,

            'message' => $result['message']
                ?? 'Flood analysis completed.',

            'valid_pixels' => $data['valid_pixels'] ?? 0,

            'total_pixels' => $data['total_pixels'] ?? 0,

            'coverage_percent' => $data['coverage_percent'] ?? 0,

            'min_depth_cm' => $data['min_depth_cm'] ?? null,

            'median_depth_cm' => $data['median_depth_cm'] ?? null,

            'max_depth_cm' => $data['max_depth_cm'] ?? null,

            'min_depth_m' => $data['min_depth_m'] ?? null,

            'median_depth_m' => $data['median_depth_m'] ?? null,

            'max_depth_m' => $data['max_depth_m'] ?? null,

            'classification' => $result['classification']
                ?? [
                    'type' => 'landsure_prototype',
                ],

            'verification' => 'modeled_dataset',
        ];

        /*
         * Add the limitations supplied by the analyzer.
         */
        if (
            isset($source['limitations']) &&
            is_array($source['limitations'])
        ) {
            $finding['limitations'] =
                $source['limitations'];
        }

        return [
            'status' => $status,

            'score' => $score,

            'finding' => $finding,

            'source' => [
                'type' => 'modeled_dataset',

                'dataset' => $source['dataset']
                    ?? 'GAR Atlas Flood Hazard',

                'scenario' => $source['scenario']
                    ?? '100-year return period',

                'measurement' => $source['measurement']
                    ?? 'modeled flood-water depth',

                'unit' => $source['unit']
                    ?? 'centimeters',

                'resolution' => $source['resolution']
                    ?? 'approximately 1 km',

                'crs' => $source['crs']
                    ?? 'EPSG:4326',

                'limitations' => $source['limitations']
                    ?? [],
            ],
        ];
    }


    /**
     * Return a safe UNKNOWN result when flood analysis
     * cannot be completed.
     */
    protected function unknownFloodResult(
        string $message
    ): array {
        return [
            'status' => 'unknown',

            'score' => null,

            'finding' => [
                'status' => 'unknown',

                'message' => $message,

                'verification' => 'not_available',
            ],

            'source' => [
                'type' => 'modeled_dataset',

                'dataset' => 'GAR Atlas Flood Hazard',

                'scenario' => '100-year return period',

                'measurement' => 'modeled flood-water depth',

                'unit' => 'centimeters',

                'resolution' => 'approximately 1 km',

                'crs' => 'EPSG:4326',

                'limitations' => [
                    'Flood analysis could not be completed for this parcel.',
                    'No safety conclusion should be drawn from an unknown result.',
                ],
            ],
        ];
    }


    /**
     * Analyze the distance from the parcel to the nearest
     * mapped waterway using the local HydroRIVERS dataset.
     *
     * The Python analyzer performs the actual geospatial
     * calculation.
     */
    protected function analyzeBuffer(Parcel $parcel): array
    {
        $scriptPath = base_path(
            'scripts/buffer_analyzer.py'
        );

        /*
         * Local Ghana river dataset.
         */
        $riverPath = storage_path(
            'app/buffer-data/ghana-rivers/HydroRIVERS_Ghana.shp'
        );

        /*
         * If the analyzer script is missing, return UNKNOWN.
         */
        if (!file_exists($scriptPath)) {
            return $this->unknownBufferResult(
                'The local waterway buffer analyzer script could not be found.'
            );
        }

        /*
         * If the Ghana river dataset is missing, return UNKNOWN.
         */
        if (!file_exists($riverPath)) {
            return $this->unknownBufferResult(
                'The local Ghana waterway dataset could not be found.'
            );
        }

        /*
         * The parcel boundary is stored as GeoJSON
         * and cast to an array by the Parcel model.
         */
        $boundary = $parcel->boundary;

        if (
            !is_array($boundary) ||
            empty($boundary)
        ) {
            return $this->unknownBufferResult(
                'No valid parcel boundary was supplied for the waterway buffer check.'
            );
        }

        /*
         * Convert the parcel GeoJSON to JSON for Python.
         */
        $parcelJson = json_encode(
            $boundary,
            JSON_UNESCAPED_SLASHES
        );

        if ($parcelJson === false) {
            return $this->unknownBufferResult(
                'The parcel boundary could not be converted to JSON for the waterway buffer check.'
            );
        }

        /*
         * Windows Python executable.
         */
        $python = 'python';

        /*
         * Build the command.
         *
         * The buffer analyzer receives the parcel GeoJSON
         * through STDIN.
         */
        $command =
            $python
            . ' '
            . escapeshellarg($scriptPath);

        /*
         * Open the Python process.
         */
        $descriptorspec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open(
            $command,
            $descriptorspec,
            $pipes,
            base_path()
        );

        if (!is_resource($process)) {
            return $this->unknownBufferResult(
                'LandSure could not start the local waterway buffer analyzer.'
            );
        }

        /*
         * Send the parcel GeoJSON to Python.
         */
        fwrite(
            $pipes[0],
            $parcelJson
        );

        fclose(
            $pipes[0]
        );

        /*
         * Read Python output.
         */
        $output = stream_get_contents(
            $pipes[1]
        );

        fclose(
            $pipes[1]
        );

        /*
         * Read any Python errors.
         */
        $errorOutput = stream_get_contents(
            $pipes[2]
        );

        fclose(
            $pipes[2]
        );

        $exitCode = proc_close(
            $process
        );

        /*
         * Python failed.
         */
        if ($exitCode !== 0) {
            return $this->unknownBufferResult(
                'The local waterway buffer analyzer returned an error.'
                . (
                    trim($errorOutput) !== ''
                        ? ' ' . trim($errorOutput)
                        : ''
                )
            );
        }

        /*
         * Decode Python JSON.
         */
        $result = json_decode(
            $output,
            true
        );

        if (!is_array($result)) {
            return $this->unknownBufferResult(
                'The waterway buffer analyzer returned an invalid response.'
            );
        }

        /*
         * Python itself reported failure.
         */
        if (($result['success'] ?? false) !== true) {
            return $this->unknownBufferResult(
                $result['message']
                    ?? $result['error']
                    ?? 'The waterway buffer analyzer could not analyze this parcel.'
            );
        }

        $status = $result['status'] ?? 'unknown';

        $score = $result['score'] ?? null;

        $data = $result['data'] ?? [];

        $source = $result['source'] ?? [];

        /*
         * Build the finding stored in the database.
         */
        $finding = [
            'status' => $status,

            'message' => $result['message']
                ?? 'Waterway buffer analysis completed.',

            'nearest_waterway_distance_m' =>
                $data['nearest_waterway_distance_m']
                ?? null,

            'screening_distance_m' =>
                $data['screening_distance_m']
                ?? 100,

            'extended_screening_distance_m' =>
                $data['extended_screening_distance_m']
                ?? 250,

            'nearest_river_id' =>
                $data['nearest_river_id']
                ?? null,

            'nearest_river_length_km' =>
                $data['nearest_river_length_km']
                ?? null,

            'rivers_checked' =>
                $data['rivers_checked']
                ?? 0,

            'classification' =>
                $result['classification']
                ?? [
                    'type' => 'landsure_prototype',
                ],

            /*
             * This is screening based on a modeled/global
             * river dataset, not legal verification.
             */
            'verification' => 'modeled_dataset',
        ];

        /*
         * When the analyzer returns a "parcel too large" result,
         * it supplies area details that explain why the screening
         * could not be completed. Preserve those so the report
         * page can show the user what was measured.
         */
        foreach (
            [
                'parcel_area_m2',
                'parcel_area_km2',
                'parcel_area_hectares',
                'parcel_area_acres',
                'maximum_screening_area_km2',
            ] as $areaKey
        ) {
            if (array_key_exists($areaKey, $data)) {
                $finding[$areaKey] = $data[$areaKey];
            }
        }

        /*
         * Add source limitations.
         */
        if (
            isset($source['limitations']) &&
            is_array($source['limitations'])
        ) {
            $finding['limitations'] =
                $source['limitations'];
        }

        return [
            'status' => $status,

            'score' => $score,

            'finding' => $finding,

            'source' => [
                'type' => 'modeled_dataset',

                'dataset' =>
                    $source['dataset']
                    ?? 'HydroRIVERS',

                'coverage' =>
                    $source['coverage']
                    ?? 'Ghana',

                'measurement' =>
                    $source['measurement']
                    ?? 'distance to mapped waterway',

                'screening_distance_m' =>
                    $source['screening_distance_m']
                    ?? 100,

                'extended_screening_distance_m' =>
                    $source['extended_screening_distance_m']
                    ?? 250,

                'max_parcel_area_km2' =>
                    $source['max_parcel_area_km2']
                    ?? null,

                'crs' =>
                    $source['crs']
                    ?? 'EPSG:4326',

                'limitations' =>
                    $source['limitations']
                    ?? [],
            ],
        ];
    }


    /**
     * Return a safe UNKNOWN result when waterway buffer
     * analysis cannot be completed.
     */
    protected function unknownBufferResult(
        string $message
    ): array {
        return [
            'status' => 'unknown',

            'score' => null,

            'finding' => [
                'status' => 'unknown',

                'message' => $message,

                'verification' => 'not_available',
            ],

            'source' => [
                'type' => 'modeled_dataset',

                'dataset' => 'HydroRIVERS',

                'coverage' => 'Ghana',

                'measurement' =>
                    'distance to mapped waterway',

                'screening_distance_m' => 100,

                'extended_screening_distance_m' => 250,

                'crs' => 'EPSG:4326',

                'limitations' => [
                    'Waterway buffer analysis could not be completed for this parcel.',
                    'No safety or legal conclusion should be drawn from an unknown result.',
                ],
            ],
        ];
    }


    /**
     * Check the quality and basic validity of a parcel boundary.
     *
     * This is a data-quality check, not a claim that the land
     * itself is legally verified.
     */
    protected function analyzeBoundary(Parcel $parcel): array
    {
        $boundary = $parcel->boundary;

        if (empty($boundary)) {
            return [
                'status' => 'unknown',
                'score' => null,
                'finding' => [
                    'status' => 'unknown',
                    'message' => 'No parcel boundary was supplied.',
                ],
            ];
        }

        if (!is_array($boundary)) {
            return [
                'status' => 'unknown',
                'score' => null,
                'finding' => [
                    'status' => 'unknown',
                    'message' => 'The stored parcel boundary is not valid GeoJSON data.',
                ],
            ];
        }

        if (($boundary['type'] ?? null) !== 'Feature') {
            return [
                'status' => 'unknown',
                'score' => null,
                'finding' => [
                    'status' => 'unknown',
                    'message' => 'The parcel boundary is missing a valid GeoJSON Feature.',
                ],
            ];
        }

        $geometry = $boundary['geometry'] ?? null;

        if (!is_array($geometry)) {
            return [
                'status' => 'unknown',
                'score' => null,
                'finding' => [
                    'status' => 'unknown',
                    'message' => 'The parcel does not contain valid geometry.',
                ],
            ];
        }

        if (($geometry['type'] ?? null) !== 'Polygon') {
            return [
                'status' => 'unknown',
                'score' => null,
                'finding' => [
                    'status' => 'unknown',
                    'message' => 'The parcel boundary must be a polygon.',
                ],
            ];
        }

        $coordinates = $geometry['coordinates'] ?? null;

        if (!is_array($coordinates) || empty($coordinates)) {
            return [
                'status' => 'unknown',
                'score' => null,
                'finding' => [
                    'status' => 'unknown',
                    'message' => 'The parcel polygon does not contain coordinates.',
                ],
            ];
        }

        $outerRing = $coordinates[0] ?? null;

        if (!is_array($outerRing)) {
            return [
                'status' => 'unknown',
                'score' => null,
                'finding' => [
                    'status' => 'unknown',
                    'message' => 'The parcel polygon has no valid outer boundary.',
                ],
            ];
        }

        if (count($outerRing) < 4) {
            return [
                'status' => 'unknown',
                'score' => null,
                'finding' => [
                    'status' => 'unknown',
                    'message' => 'The parcel boundary does not contain enough points to form a polygon.',
                ],
            ];
        }

        $firstPoint = $outerRing[0] ?? null;
        $lastPoint = $outerRing[count($outerRing) - 1] ?? null;

        if (
            !is_array($firstPoint) ||
            !is_array($lastPoint) ||
            count($firstPoint) < 2 ||
            count($lastPoint) < 2
        ) {
            return [
                'status' => 'unknown',
                'score' => null,
                'finding' => [
                    'status' => 'unknown',
                    'message' => 'The parcel boundary contains invalid coordinate points.',
                ],
            ];
        }

        $isClosed =
            (float) $firstPoint[0] === (float) $lastPoint[0] &&
            (float) $firstPoint[1] === (float) $lastPoint[1];

        if (!$isClosed) {
            return [
                'status' => 'unknown',
                'score' => null,
                'finding' => [
                    'status' => 'unknown',
                    'message' => 'The parcel polygon is not closed.',
                ],
            ];
        }

        if (
            $parcel->latitude === null ||
            $parcel->longitude === null
        ) {
            return [
                'status' => 'unknown',
                'score' => null,
                'finding' => [
                    'status' => 'unknown',
                    'message' => 'The parcel does not have a center location.',
                ],
            ];
        }

        if (
            $parcel->latitude < -90 ||
            $parcel->latitude > 90 ||
            $parcel->longitude < -180 ||
            $parcel->longitude > 180
        ) {
            return [
                'status' => 'unknown',
                'score' => null,
                'finding' => [
                    'status' => 'unknown',
                    'message' => 'The parcel contains invalid latitude or longitude values.',
                ],
            ];
        }

        return [
            'status' => 'good',

            'score' => 100,

            'finding' => [
                'status' => 'good',

                'message' => 'The submitted parcel boundary is a valid closed polygon with a valid center location.',

                'points' => count($outerRing),

                'latitude' => (float) $parcel->latitude,

                'longitude' => (float) $parcel->longitude,

                'verification' => 'user_submitted',
            ],
        ];
    }
}