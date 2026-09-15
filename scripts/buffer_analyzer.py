import json
import math
import os
import sys

import shapefile
from shapely.geometry import LineString, Polygon, shape
from shapely.ops import transform


# ============================================================
# LandSure Waterway Buffer Analyzer
# ============================================================
#
# IMPORTANT:
#   These thresholds are LandSure prototype screening values.
#   They are NOT official Ghanaian legal or planning setbacks.
#
# This analyzer:
#   1. Reads a parcel GeoJSON Feature from STDIN.
#   2. Validates the parcel geometry.
#   3. Rejects unrealistically large parcels.
#   4. Measures distance from the actual parcel polygon to
#      mapped HydroRIVERS river lines.
#   5. Returns a structured JSON result.
#
# Interface:
#   python scripts\buffer_analyzer.py
#   GeoJSON is provided on STDIN.
#
# The Ghana HydroRIVERS shapefile path is resolved internally
# relative to the project directory. No CLI argument is used.
#


# ============================================================
# Configuration
# ============================================================

BUFFER_DISTANCE_METERS = 100.0
EXTENDED_BUFFER_DISTANCE_METERS = 250.0

# Maximum parcel area that LandSure will screen using this
# parcel-to-river analyzer.
#
# 10 km² = 1,000 hectares = approximately 2,471 acres.
#
# This is a technical screening limit, NOT a legal land-size
# limit and NOT a Ghanaian planning standard.
MAX_PARCEL_AREA_KM2 = 10.0


BASE_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))


RIVER_SHP = os.path.join(
    BASE_DIR,
    "storage",
    "app",
    "buffer-data",
    "ghana-rivers",
    "HydroRIVERS_Ghana.shp",
)


# ============================================================
# Helpers
# ============================================================

def meters_per_degree_latitude(latitude):
    """
    Approximate meters represented by one degree of latitude.
    """
    lat = math.radians(latitude)

    return (
        111132.92
        - 559.82 * math.cos(2 * lat)
        + 1.175 * math.cos(4 * lat)
        - 0.0023 * math.cos(6 * lat)
    )


def meters_per_degree_longitude(latitude):
    """
    Approximate meters represented by one degree of longitude.
    """
    lat = math.radians(latitude)

    return (
        111412.84 * math.cos(lat)
        - 93.5 * math.cos(3 * lat)
        + 0.118 * math.cos(5 * lat)
    )


def local_meter_projection(latitude):
    """
    Create a simple local meter-based projection around the
    parcel latitude.

    HydroRIVERS is stored in EPSG:4326.
    For this prototype, a local degree-to-meter conversion
    is sufficient for screening distances in Ghana.
    """
    meters_lat = meters_per_degree_latitude(latitude)
    meters_lon = meters_per_degree_longitude(latitude)

    def project(x, y, z=None):
        if z is None:
            return (
                x * meters_lon,
                y * meters_lat,
            )

        return (
            x * meters_lon,
            y * meters_lat,
            z,
        )

    return project


def load_parcel():
    """
    Read the parcel GeoJSON from STDIN.
    """
    raw = sys.stdin.read().strip()

    if not raw:
        raise ValueError(
            "No parcel GeoJSON was supplied."
        )

    parcel_data = json.loads(raw)

    if parcel_data.get("type") == "Feature":
        geometry_data = parcel_data.get("geometry")
    else:
        geometry_data = parcel_data

    if not geometry_data:
        raise ValueError(
            "Parcel geometry is missing."
        )

    geometry = shape(geometry_data)

    if geometry.is_empty:
        raise ValueError(
            "Parcel geometry is empty."
        )

    if not isinstance(geometry, Polygon):
        raise ValueError(
            f"Parcel geometry must be Polygon, got {geometry.geom_type}."
        )

    if not geometry.is_valid:
        geometry = geometry.buffer(0)

    if geometry.is_empty:
        raise ValueError(
            "Parcel geometry could not be repaired."
        )

    if not isinstance(geometry, Polygon):
        raise ValueError(
            "Parcel geometry is not a valid Polygon."
        )

    return geometry


def compute_parcel_area_km2(parcel):
    """
    Compute the parcel area in square kilometers using the
    same local meter projection used for distance measurement.

    Returns a dict with area in m², km², hectares, and acres.
    """
    centroid = parcel.centroid
    latitude = centroid.y

    project = local_meter_projection(latitude)

    parcel_meters = transform(project, parcel)

    area_m2 = parcel_meters.area
    area_km2 = area_m2 / 1_000_000.0
    area_hectares = area_m2 / 10_000.0
    area_acres = area_m2 / 4046.8564224

    return {
        "area_m2": area_m2,
        "area_km2": area_km2,
        "area_hectares": area_hectares,
        "area_acres": area_acres,
    }


def shape_to_linestrings(river_shape):
    """
    Convert a shapefile river geometry into individual LineStrings.

    HydroRIVERS records can contain multiple parts.
    """
    points = river_shape.points
    parts = list(river_shape.parts)

    if not points:
        return []

    if not parts:
        return []

    lines = []

    for index, start in enumerate(parts):

        if index + 1 < len(parts):
            end = parts[index + 1]
        else:
            end = len(points)

        segment_points = points[start:end]

        if len(segment_points) < 2:
            continue

        line = LineString(segment_points)

        if not line.is_empty:
            lines.append(line)

    return lines


def get_river_id(record):
    """
    HydroRIVERS HYRIV_ID is the first attribute.
    """
    try:
        return record[0]
    except Exception:
        return None


def get_river_length_km(record):
    """
    HydroRIVERS LENGTH_KM is the fourth attribute.
    """
    try:
        return record[3]
    except Exception:
        return None


def calculate_nearest_river(parcel, reader):
    """
    Calculate the shortest distance from the actual parcel
    polygon to the mapped HydroRIVERS network.

    The distance is measured from the actual polygon rather
    than from only the parcel center.

    Therefore:

        parcel touches river  -> 0 m
        parcel 50 m away      -> 50 m
        parcel 300 m away     -> 300 m
    """

    centroid = parcel.centroid

    latitude = centroid.y

    project = local_meter_projection(latitude)

    parcel_meters = transform(
        project,
        parcel,
    )

    closest_distance = None
    closest_river_id = None
    closest_river_length_km = None

    checked_rivers = 0
    checked_lines = 0

    for river_shape, record in (
        (shape_record.shape, shape_record.record)
        for shape_record in reader.iterShapeRecords()
    ):

        checked_rivers += 1

        lines = shape_to_linestrings(
            river_shape
        )

        for line in lines:

            checked_lines += 1

            line_meters = transform(
                project,
                line,
            )

            distance = parcel_meters.distance(
                line_meters
            )

            if (
                closest_distance is None
                or distance < closest_distance
            ):
                closest_distance = distance

                closest_river_id = get_river_id(
                    record
                )

                closest_river_length_km = (
                    get_river_length_km(
                        record
                    )
                )

                # Once the actual parcel intersects a mapped
                # waterway, the minimum possible distance is zero.
                # We can stop because no smaller distance exists.
                if closest_distance == 0:
                    return {
                        "distance_meters": 0.0,
                        "river_id": closest_river_id,
                        "river_length_km": closest_river_length_km,
                        "rivers_checked": checked_rivers,
                        "lines_checked": checked_lines,
                    }

    return {
        "distance_meters": (
            round(closest_distance, 2)
            if closest_distance is not None
            else None
        ),
        "river_id": closest_river_id,
        "river_length_km": closest_river_length_km,
        "rivers_checked": checked_rivers,
        "lines_checked": checked_lines,
    }


def classify_distance(distance_meters):
    """
    LandSure prototype screening classification.

    HIGH:
        0–100 m

    MODERATE:
        >100–250 m

    LOW:
        >250 m

    UNKNOWN:
        No result.
    """

    if distance_meters is None:
        return "unknown"

    if distance_meters <= BUFFER_DISTANCE_METERS:
        return "high"

    if distance_meters <= EXTENDED_BUFFER_DISTANCE_METERS:
        return "moderate"

    return "low"


def build_result(parcel, nearest):
    """
    Build the JSON result returned to Laravel.
    """

    distance = nearest["distance_meters"]

    status = classify_distance(
        distance
    )

    if status == "high":

        message = (
            "The parcel is within 100 meters of a mapped "
            "waterway in the HydroRIVERS dataset."
        )

    elif status == "moderate":

        message = (
            "The parcel is within 250 meters of a mapped "
            "waterway and may warrant additional site review."
        )

    elif status == "low":

        message = (
            "No mapped HydroRIVERS waterway was detected "
            "within the 250-meter screening range."
        )

    else:

        message = (
            "Waterway buffer screening could not be completed."
        )

    score = {
        "high": 0,
        "moderate": 50,
        "low": 100,
        "unknown": None,
    }[status]

    return {
        "success": True,

        "status": status,

        "score": score,

        "message": message,

        "data": {
            "nearest_waterway_distance_m": distance,

            "screening_distance_m":
                BUFFER_DISTANCE_METERS,

            "extended_screening_distance_m":
                EXTENDED_BUFFER_DISTANCE_METERS,

            "nearest_river_id":
                nearest["river_id"],

            "nearest_river_length_km":
                nearest["river_length_km"],

            "rivers_checked":
                nearest["rivers_checked"],

            "river_lines_checked":
                nearest["lines_checked"],
        },

        "classification": {
            "type": "landsure_prototype",

            "note": (
                "These distances are LandSure screening "
                "thresholds only and are not official "
                "Ghanaian legal or planning setbacks."
            ),
        },

        "source": {
            "dataset": "HydroRIVERS",

            "coverage": "Ghana",

            "measurement":
                "distance from parcel polygon to mapped waterway",

            "screening_distance_m":
                BUFFER_DISTANCE_METERS,

            "extended_screening_distance_m":
                EXTENDED_BUFFER_DISTANCE_METERS,

            "max_parcel_area_km2":
                MAX_PARCEL_AREA_KM2,

            "crs": "EPSG:4326",

            "limitations": [
                "HydroRIVERS is a modeled/global river network.",

                "Mapped waterways may not represent every local "
                "drainage channel or stream.",

                "The result is a screening result, not legal "
                "buffer verification.",

                "Actual Ghanaian setbacks may depend on the "
                "specific waterway, planning authority, and "
                "applicable regulations.",

                "Parcel-scale accuracy is limited by the source "
                "river network.",

                "The screening distance is measured from the "
                "actual parcel polygon rather than only its center.",
            ],
        },
    }


def build_too_large_result(area):
    """
    Build the JSON result when the parcel is larger than the
    configured maximum screening area.
    """

    return {
        "success": True,

        "status": "unknown",

        "score": None,

        "message": (
            "This parcel is larger than LandSure's maximum "
            f"screening area of {MAX_PARCEL_AREA_KM2:g} km². "
            "The parcel-to-waterway distance would not be "
            "meaningful at this scale. Please draw or submit "
            "a smaller parcel representing the actual property "
            "being assessed."
        ),

        "data": {
            "parcel_area_m2": round(area["area_m2"], 2),
            "parcel_area_km2": round(area["area_km2"], 4),
            "parcel_area_hectares": round(area["area_hectares"], 4),
            "parcel_area_acres": round(area["area_acres"], 4),
            "maximum_screening_area_km2": MAX_PARCEL_AREA_KM2,
        },

        "classification": {
            "type": "landsure_prototype",

            "note": (
                "The maximum screening area is a LandSure "
                "technical limit, not a legal land-size limit "
                "and not a Ghanaian planning standard."
            ),
        },

        "source": {
            "dataset": "HydroRIVERS",

            "coverage": "Ghana",

            "measurement":
                "distance from parcel polygon to mapped waterway",

            "screening_distance_m":
                BUFFER_DISTANCE_METERS,

            "extended_screening_distance_m":
                EXTENDED_BUFFER_DISTANCE_METERS,

            "max_parcel_area_km2":
                MAX_PARCEL_AREA_KM2,

            "crs": "EPSG:4326",

            "limitations": [
                "LandSure does not screen parcels larger than "
                "the configured maximum screening area because "
                "the distance result would not be meaningful as "
                "a single-parcel waterway screening result.",

                "HydroRIVERS is a modeled/global river network.",

                "The result is a screening result, not legal "
                "buffer verification.",
            ],
        },
    }


def main():

    try:

        print(
            "LandSure Ghana Waterway Buffer Analyzer",
            file=sys.stderr,
        )

        print(
            f"Primary screening distance: "
            f"{BUFFER_DISTANCE_METERS:.0f} meters",
            file=sys.stderr,
        )

        print(
            f"Extended screening distance: "
            f"{EXTENDED_BUFFER_DISTANCE_METERS:.0f} meters",
            file=sys.stderr,
        )

        print(
            f"Maximum screening area: "
            f"{MAX_PARCEL_AREA_KM2:g} km²",
            file=sys.stderr,
        )

        parcel = load_parcel()

        # ----------------------------------------------------
        # Size guard.
        #
        # If the parcel is larger than the configured maximum,
        # return UNKNOWN instead of a misleading distance.
        # ----------------------------------------------------

        area = compute_parcel_area_km2(parcel)

        if area["area_km2"] > MAX_PARCEL_AREA_KM2:

            print(
                json.dumps(
                    build_too_large_result(area),
                    indent=2,
                )
            )

            return

        # ----------------------------------------------------
        # Shapefile must exist.
        # ----------------------------------------------------

        if not os.path.exists(RIVER_SHP):

            raise FileNotFoundError(
                f"Ghana river dataset not found: {RIVER_SHP}"
            )

        reader = shapefile.Reader(
            RIVER_SHP
        )

        try:

            nearest = calculate_nearest_river(
                parcel,
                reader,
            )

        finally:

            reader.close()

        result = build_result(
            parcel,
            nearest,
        )

        print(
            json.dumps(
                result,
                indent=2,
            )
        )

    except Exception as error:

        result = {
            "success": False,

            "status": "unknown",

            "score": None,

            "message": str(error),
        }

        print(
            json.dumps(
                result,
                indent=2,
            )
        )

        sys.exit(1)


if __name__ == "__main__":
    main()