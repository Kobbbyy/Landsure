import json
import os
import sys

import numpy as np
import rasterio
from rasterio.windows import from_bounds


def main():

    # ---------------------------------------------------------
    # LandSure Flood Analyzer
    #
    # Reads a GeoJSON parcel and checks it against the
    # GAR Atlas 100-year flood-hazard raster.
    #
    # Raster values:
    #   Flood-water depth in centimeters
    #
    # NODATA:
    #   0.0
    #
    # Important:
    #   This is a modeled flood-hazard scenario.
    #   It is NOT a live flood warning and does not guarantee
    #   that a property is safe.
    # ---------------------------------------------------------

    if len(sys.argv) < 2:

        print(json.dumps({
            "success": False,
            "error": "No flood raster was supplied."
        }))

        sys.exit(1)


    raster_path = sys.argv[1]


    # ---------------------------------------------------------
    # Read GeoJSON from standard input.
    # ---------------------------------------------------------

    try:

        parcel = json.loads(
            sys.stdin.read()
        )

    except Exception as error:

        print(json.dumps({
            "success": False,
            "error": f"Invalid parcel JSON: {error}"
        }))

        sys.exit(1)


    # ---------------------------------------------------------
    # Check raster exists.
    # ---------------------------------------------------------

    if not os.path.exists(raster_path):

        print(json.dumps({
            "success": False,
            "error": "Flood raster file was not found."
        }))

        sys.exit(1)


    try:

        geometry = parcel.get("geometry")


        if not isinstance(geometry, dict):

            raise ValueError(
                "Parcel geometry is missing."
            )


        if geometry.get("type") != "Polygon":

            raise ValueError(
                "Parcel geometry must be a Polygon."
            )


        coordinates = geometry.get("coordinates")


        if (
            not isinstance(coordinates, list)
            or not coordinates
        ):

            raise ValueError(
                "Parcel polygon has no coordinates."
            )


        outer_ring = coordinates[0]


        if (
            not isinstance(outer_ring, list)
            or len(outer_ring) < 4
        ):

            raise ValueError(
                "Parcel polygon does not contain enough coordinates."
            )


        # -----------------------------------------------------
        # Extract longitude/latitude values.
        #
        # GeoJSON order is:
        #
        #   [longitude, latitude]
        # -----------------------------------------------------

        longitudes = []

        latitudes = []


        for point in outer_ring:

            if (
                not isinstance(point, list)
                or len(point) < 2
            ):

                raise ValueError(
                    "Parcel contains an invalid coordinate point."
                )


            longitudes.append(
                float(point[0])
            )

            latitudes.append(
                float(point[1])
            )


        min_lon = min(longitudes)
        max_lon = max(longitudes)

        min_lat = min(latitudes)
        max_lat = max(latitudes)


        # -----------------------------------------------------
        # Open raster.
        # -----------------------------------------------------

        with rasterio.open(raster_path) as src:

            if src.crs is None:

                raise ValueError(
                    "Flood raster has no CRS."
                )


            raster_crs = str(src.crs)


            # -------------------------------------------------
            # This LandSure flood dataset is EPSG:4326.
            #
            # The parcel coordinates are also expected to be
            # longitude/latitude.
            # -------------------------------------------------

            if raster_crs != "EPSG:4326":

                raise ValueError(
                    f"Expected EPSG:4326 raster, found {raster_crs}."
                )


            # -------------------------------------------------
            # Check whether parcel bounding box intersects
            # the raster at all.
            # -------------------------------------------------

            if (
                max_lon < src.bounds.left
                or min_lon > src.bounds.right
                or max_lat < src.bounds.bottom
                or min_lat > src.bounds.top
            ):

                result = {

                    "success": True,

                    "status": "unknown",

                    "message": (
                        "The parcel is outside the coverage area "
                        "of the flood dataset."
                    ),

                    "data": {
                        "valid_pixels": 0,
                        "total_pixels": 0,
                        "coverage_percent": 0,
                        "min_depth_cm": None,
                        "median_depth_cm": None,
                        "max_depth_cm": None,
                        "min_depth_m": None,
                        "median_depth_m": None,
                        "max_depth_m": None
                    },

                    "source": {
                        "dataset": "GAR Atlas Flood Hazard",
                        "scenario": "100-year return period",
                        "measurement": "modeled flood-water depth",
                        "unit": "centimeters",
                        "resolution": "approximately 1 km",
                        "crs": raster_crs
                    }
                }


                print(
                    json.dumps(result)
                )

                return


            # -------------------------------------------------
            # Clip the requested bounds to the raster bounds.
            # -------------------------------------------------

            left = max(
                min_lon,
                src.bounds.left
            )

            right = min(
                max_lon,
                src.bounds.right
            )

            bottom = max(
                min_lat,
                src.bounds.bottom
            )

            top = min(
                max_lat,
                src.bounds.top
            )


            # -------------------------------------------------
            # Convert geographic bounds into a raster window.
            # -------------------------------------------------

            window = from_bounds(
                left,
                bottom,
                right,
                top,
                src.transform
            )


            # -------------------------------------------------
            # Make sure the window stays inside the raster.
            # -------------------------------------------------

            window = window.intersection(
                rasterio.windows.Window(
                    0,
                    0,
                    src.width,
                    src.height
                )
            )


            # -------------------------------------------------
            # Read only the relevant raster section.
            # -------------------------------------------------

            data = src.read(
                1,
                window=window
            )


            nodata = src.nodata


            # -------------------------------------------------
            # Remove NoData.
            #
            # IMPORTANT:
            # In this dataset:
            #
            #     0.0 = NODATA
            #
            # Therefore 0 must NEVER be interpreted as
            # "zero flood depth" or "safe".
            # -------------------------------------------------

            if nodata is None:

                valid = data[
                    np.isfinite(data)
                ]

            else:

                valid = data[
                    np.isfinite(data)
                    &
                    (data != nodata)
                ]


            total_pixels = int(
                data.size
            )

            valid_pixels = int(
                valid.size
            )


            # -------------------------------------------------
            # No valid modeled flood cells.
            # -------------------------------------------------

            if valid_pixels == 0:

                result = {

                    "success": True,

                    "status": "unknown",

                    "message": (
                        "No modeled flood-hazard cells with "
                        "valid data intersected the parcel area."
                    ),

                    "data": {

                        "valid_pixels": 0,

                        "total_pixels": total_pixels,

                        "coverage_percent": 0,

                        "min_depth_cm": None,

                        "median_depth_cm": None,

                        "max_depth_cm": None,

                        "min_depth_m": None,

                        "median_depth_m": None,

                        "max_depth_m": None
                    },

                    "source": {

                        "dataset": "GAR Atlas Flood Hazard",

                        "scenario": "100-year return period",

                        "measurement": "modeled flood-water depth",

                        "unit": "centimeters",

                        "resolution": "approximately 1 km",

                        "crs": raster_crs
                    }
                }


                print(
                    json.dumps(result)
                )

                return


            # -------------------------------------------------
            # Calculate statistics.
            # -------------------------------------------------

            minimum = float(
                np.min(valid)
            )

            median = float(
                np.median(valid)
            )

            maximum = float(
                np.max(valid)
            )


            coverage_percent = (
                valid_pixels / total_pixels
            ) * 100 if total_pixels > 0 else 0


            # -------------------------------------------------
            # LandSure prototype classification.
            #
            # These thresholds are NOT official Ghanaian
            # legal/planning standards.
            #
            # < 30 cm       LOW
            # 30 - <100 cm MODERATE
            # 100 - <200cm HIGH
            # >= 200 cm    VERY HIGH
            #
            # We classify using MAXIMUM modeled depth because
            # a parcel can intersect a deeper flood cell even
            # when its median value is lower.
            # -------------------------------------------------

            if maximum >= 200:

                status = "very_high"

                message = (
                    "The parcel intersects modeled flood-hazard "
                    "cells with a maximum modeled depth of at "
                    "least 2 meters."
                )


            elif maximum >= 100:

                status = "high"

                message = (
                    "The parcel intersects modeled flood-hazard "
                    "cells with a maximum modeled depth of at "
                    "least 1 meter."
                )


            elif maximum >= 30:

                status = "moderate"

                message = (
                    "The parcel intersects modeled flood-hazard "
                    "cells with modeled depths of at least "
                    "30 centimeters."
                )


            else:

                status = "low"

                message = (
                    "The parcel intersects modeled flood-hazard "
                    "cells, but the maximum modeled depth is "
                    "below 30 centimeters."
                )


            # -------------------------------------------------
            # Final result.
            # -------------------------------------------------

            result = {

                "success": True,

                "status": status,

                "message": message,

                "data": {

                    "valid_pixels": valid_pixels,

                    "total_pixels": total_pixels,

                    "coverage_percent": round(
                        coverage_percent,
                        2
                    ),

                    "min_depth_cm": round(
                        minimum,
                        2
                    ),

                    "median_depth_cm": round(
                        median,
                        2
                    ),

                    "max_depth_cm": round(
                        maximum,
                        2
                    ),

                    "min_depth_m": round(
                        minimum / 100,
                        3
                    ),

                    "median_depth_m": round(
                        median / 100,
                        3
                    ),

                    "max_depth_m": round(
                        maximum / 100,
                        3
                    )
                },

                "classification": {

                    "type": "landsure_prototype",

                    "note": (
                        "Prototype flood-depth classification "
                        "only. These thresholds are not official "
                        "Ghanaian legal or planning standards."
                    )
                },

                "source": {

                    "dataset": "GAR Atlas Flood Hazard",

                    "scenario": "100-year return period",

                    "measurement": "modeled flood-water depth",

                    "unit": "centimeters",

                    "resolution": "approximately 1 km",

                    "crs": raster_crs,

                    "limitations": [

                        "This is a modeled flood-hazard scenario.",

                        "It is not a live flood warning.",

                        "The approximately 1 km resolution limits "
                        "parcel-scale precision.",

                        "Local flood defenses may not be represented.",

                        "The result should not be treated as a "
                        "guarantee of safety."
                    ]
                }
            }


            print(
                json.dumps(result)
            )


    except Exception as error:

        print(
            json.dumps({
                "success": False,
                "error": str(error)
            })
        )

        sys.exit(1)


if __name__ == "__main__":

    main()