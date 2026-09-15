import json
import os
import shapefile


BASE_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))

RIVER_SHP = os.path.join(
    BASE_DIR,
    "storage",
    "app",
    "buffer-data",
    "HydroRIVERS",
    "HydroRIVERS_v10_af_shp",
    "HydroRIVERS_v10_af.shp",
)

GHANA_BOUNDARY = os.path.join(
    BASE_DIR,
    "storage",
    "app",
    "buffer-data",
    "ghana-boundary.geojson",
)

OUTPUT_DIR = os.path.join(
    BASE_DIR,
    "storage",
    "app",
    "buffer-data",
    "ghana-rivers",
)

OUTPUT_SHP = os.path.join(
    OUTPUT_DIR,
    "HydroRIVERS_Ghana.shp",
)


def point_in_polygon(point, polygon):
    """
    Ray-casting point-in-polygon test.

    point:
        (longitude, latitude)

    polygon:
        List of (longitude, latitude)
    """

    x, y = point
    inside = False

    j = len(polygon) - 1

    for i in range(len(polygon)):
        xi, yi = polygon[i]
        xj, yj = polygon[j]

        intersects = (
            ((yi > y) != (yj > y))
            and
            (
                x
                < (xj - xi) * (y - yi) / ((yj - yi) or 1e-30) + xi
            )
        )

        if intersects:
            inside = not inside

        j = i

    return inside


def load_ghana_polygon():
    with open(GHANA_BOUNDARY, "r", encoding="utf-8") as file:
        data = json.load(file)

    feature = data["features"][0]
    geometry = feature["geometry"]

    if geometry["type"] != "Polygon":
        raise RuntimeError(
            f"Expected Polygon geometry, got {geometry['type']}"
        )

    # The first ring is Ghana's exterior boundary.
    return geometry["coordinates"][0]


def geometry_intersects_ghana(shape, ghana_polygon):
    """
    Determine whether a river line intersects Ghana.

    We test the vertices of each river segment against
    Ghana's boundary polygon and also use the river's
    bounding box as a quick rejection test.
    """

    points = shape.points

    if not points:
        return False

    ghana_lons = [point[0] for point in ghana_polygon]
    ghana_lats = [point[1] for point in ghana_polygon]

    ghana_min_lon = min(ghana_lons)
    ghana_max_lon = max(ghana_lons)
    ghana_min_lat = min(ghana_lats)
    ghana_max_lat = max(ghana_lats)

    river_min_lon = min(point[0] for point in points)
    river_max_lon = max(point[0] for point in points)
    river_min_lat = min(point[1] for point in points)
    river_max_lat = max(point[1] for point in points)

    # Quick bounding-box rejection.
    if river_max_lon < ghana_min_lon:
        return False

    if river_min_lon > ghana_max_lon:
        return False

    if river_max_lat < ghana_min_lat:
        return False

    if river_min_lat > ghana_max_lat:
        return False

    # Check river vertices.
    for point in points:
        if point_in_polygon(point, ghana_polygon):
            return True

    return False


def main():
    print("LandSure Ghana River Extraction")
    print("--------------------------------")
    print()

    print("Loading Ghana boundary...")

    ghana_polygon = load_ghana_polygon()

    print("Ghana boundary loaded.")
    print()

    print("Opening HydroRIVERS Africa dataset...")
    print(RIVER_SHP)
    print()

    reader = shapefile.Reader(RIVER_SHP)

    print(f"Total river segments: {len(reader)}")
    print()

    os.makedirs(OUTPUT_DIR, exist_ok=True)

    print("Creating Ghana river dataset...")

    writer = shapefile.Writer(
        OUTPUT_SHP,
        shapeType=shapefile.POLYLINE,
    )

    # Copy the original HydroRIVERS attributes.
    for field in reader.fields[1:]:
        name, field_type, size, decimal = field

        writer.field(
            name,
            field_type,
            size=size,
            decimal=decimal,
        )

    matched = 0
    checked = 0

    try:
        for shape_record in reader.iterShapeRecords():

            checked += 1

            if geometry_intersects_ghana(
                shape_record.shape,
                ghana_polygon,
            ):
                writer.shape(shape_record.shape)
                writer.record(*shape_record.record)

                matched += 1

            if checked % 100000 == 0:
                print(
                    f"Checked: {checked:,} | "
                    f"Ghana matches: {matched:,}"
                )

    finally:
        writer.close()
        reader.close()

    print()
    print("Extraction complete.")
    print()
    print(f"River segments checked: {checked:,}")
    print(f"Ghana matches: {matched:,}")
    print()
    print("Output:")
    print(OUTPUT_SHP)
    print()

    print("Done.")


if __name__ == "__main__":
    main()