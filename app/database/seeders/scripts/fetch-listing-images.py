#!/usr/bin/env python3
"""Download topic-matched listing/category images and save as WebP."""
import subprocess
import sys
from pathlib import Path

try:
    from PIL import Image
except ImportError:
    subprocess.check_call([sys.executable, "-m", "pip", "install", "pillow", "-q"])
    from PIL import Image

ROOT = Path(__file__).resolve().parents[3]
PUBLIC = ROOT / "public" / "images"

MAP = {
    "categories": {
        # eat: custom local photo (from traditional-maldivian-dinner)
        # wash: custom local laundry photo
        # buy: custom local photo from lacquered-wooden-bowl asset
        # experience: custom local photo (from snorkel-with-sea-turtles)
    },
    "listings": {
        # maldivian-tuna-curry-set: custom local photo
        # fresh-lobster-per-100g: custom local photo
        "sunset-smoothie-bowl": "photo-1590301157890-4810ed352733",
        # traditional-maldivian-dinner: custom local photo
        # bajiya-hedhikaa-box: custom local photo
        # standard/express/delicate laundry: custom local laundry photo
        # handwoven-coconut-leaf-hat: custom local photo
        # lacquered-wooden-bowl: custom local photo (do not overwrite)
        # coconut-shell-earrings-pair: custom local photo
        # snorkel-with-sea-turtles: custom local photo
        # sunset-dolphin-cruise: custom local photo
        "sandbank-picnic": "photo-1507525428034-b723cf961d3e",
        # cooking-class-traditional-maldivian-curry: custom local photo
        "dhivehi-language-taster": "photo-1523240795612-9a054b0db644",
        # island-walking-tour: custom local photo
    },
}

USER_AGENT = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36"


def download(photo_id: str, out_path: Path, width: int, height: int) -> None:
    import urllib.request

    url = f"https://images.unsplash.com/{photo_id}?w={width}&h={height}&fit=crop&q=85"
    req = urllib.request.Request(url, headers={"User-Agent": USER_AGENT})
    tmp = out_path.with_suffix(".jpg")
    with urllib.request.urlopen(req) as resp, open(tmp, "wb") as f:
        f.write(resp.read())
    img = Image.open(tmp).convert("RGB")
    img.save(out_path, "WEBP", quality=82)
    tmp.unlink(missing_ok=True)
    print(f"  ok {out_path.name} ({out_path.stat().st_size // 1024}kb)")


def main() -> None:
    (PUBLIC / "listings").mkdir(parents=True, exist_ok=True)
    (PUBLIC / "categories").mkdir(parents=True, exist_ok=True)

    print("Categories:")
    for key, photo in MAP["categories"].items():
        if key in ("buy", "wash", "eat", "experience"):
            print(f"  skip {key}.webp (custom asset)")
            continue
        download(photo, PUBLIC / "categories" / f"{key}.webp", 900, 560)

    print("Listings:")
    for slug, photo in MAP["listings"].items():
        if slug in (
            "lacquered-wooden-bowl",
            "handwoven-coconut-leaf-hat",
            "coconut-shell-earrings-pair",
            "sunset-dolphin-cruise",
            "traditional-maldivian-dinner",
            "island-walking-tour",
            "bajiya-hedhikaa-box",
            "fresh-lobster-per-100g",
            "snorkel-with-sea-turtles",
            "maldivian-tuna-curry-set",
            "cooking-class-traditional-maldivian-curry",
            "standard-laundry-per-kg",
            "express-laundry-per-kg",
            "delicate-hand-wash-per-item",
        ):
            print(f"  skip {slug}.webp (custom asset)")
            continue
        download(photo, PUBLIC / "listings" / f"{slug}.webp", 800, 500)

    print("Done.")


if __name__ == "__main__":
    main()
