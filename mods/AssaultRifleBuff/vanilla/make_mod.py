import argparse
import os
import sys
import xml.etree.ElementTree as ET

RELATIVE_XML = os.path.join("Content", "Items", "Weapons", "weapons.xml")
ASSET_EXT = (".png", ".ogg", ".wav", ".xml", ".webm", ".jpg")


def attr(elem, name):
    for key in elem.attrib:
        if key.lower() == name.lower():
            return key
    return None


def get_float(elem, name, fallback):
    key = attr(elem, name)
    if key is None:
        return fallback
    try:
        return float(elem.attrib[key])
    except ValueError:
        return fallback


def set_attr(elem, name, value):
    key = attr(elem, name) or name
    elem.attrib[key] = value


def fmt(value):
    text = ("%.5f" % value).rstrip("0").rstrip(".")
    return text if text else "0"


def is_game_path(path):
    return bool(path) and os.path.isfile(os.path.join(path, RELATIVE_XML))


def candidates():
    home = os.path.expanduser("~")
    result = []
    for drive in "CDEFG":
        result.append(r"%s:\Program Files (x86)\Steam\steamapps\common\Barotrauma" % drive)
        result.append(r"%s:\Steam\steamapps\common\Barotrauma" % drive)
        result.append(r"%s:\SteamLibrary\steamapps\common\Barotrauma" % drive)
    result.append(os.path.join(home, ".steam", "steam", "steamapps", "common", "Barotrauma"))
    result.append(os.path.join(home, ".local", "share", "Steam", "steamapps", "common", "Barotrauma"))
    result.append(os.path.join(home, "Library", "Application Support", "Steam", "steamapps", "common", "Barotrauma"))
    return result


def find_game_path():
    for path in candidates():
        if is_game_path(path):
            return path
    return ""


def fix_assets(elem):
    for node in elem.iter():
        for key, value in list(node.attrib.items()):
            if not value or "/" in value or "\\" in value:
                continue
            if os.path.splitext(value)[1].lower() in ASSET_EXT:
                node.attrib[key] = "Content/Items/Weapons/" + value


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("--game-path", default="")
    parser.add_argument("--rounds", type=int, default=1488)
    parser.add_argument("--fire-rate", type=float, default=5.0)
    args = parser.parse_args()

    game_path = args.game_path or find_game_path()
    while not is_game_path(game_path):
        print("Papka s igroj Barotrauma ne najdena.")
        game_path = input("Ukazhi put' k papke s igroj: ").strip().strip('"')

    print("Igra najdena: %s" % game_path)

    source = ET.parse(os.path.join(game_path, RELATIVE_XML))
    root = ET.Element("Items")
    override = ET.SubElement(root, "Override")

    report = []
    for item in source.getroot().iter():
        if item.tag.lower() != "item":
            continue
        key = attr(item, "identifier")
        if key is None or not item.attrib[key].lower().startswith("assaultrifle"):
            continue

        copy = ET.fromstring(ET.tostring(item))
        override.append(copy)
        fix_assets(copy)
        identifier = copy.attrib[attr(copy, "identifier")]

        weapons = [n for n in copy.iter() if n.tag.lower() == "rangedweapon"]
        if weapons:
            for weapon in weapons:
                reload_time = get_float(weapon, "reload", 1.0)
                reload_noskill = get_float(weapon, "reloadnoskill", 1.0)
                set_attr(weapon, "reload", fmt(reload_time / args.fire_rate))
                set_attr(weapon, "reloadnoskill", fmt(reload_noskill / args.fire_rate))
                report.append("  %s : reload %s -> %s" % (identifier, reload_time, fmt(reload_time / args.fire_rate)))
            continue

        cost = 0.0
        for node in copy.iter():
            if node.tag.lower() != "statuseffect":
                continue
            value = get_float(node, "Condition", 0.0)
            if value < 0 and cost == 0.0:
                cost = abs(value)

        old_health = get_float(copy, "health", 100.0)
        if cost > 0.0:
            new_health = cost * args.rounds
            old_rounds = int(old_health // cost)
        else:
            new_health = old_health * (args.rounds / 30.0)
            old_rounds = 30
        set_attr(copy, "health", fmt(new_health))
        report.append("  %s : patronov %d -> %d" % (identifier, old_rounds, args.rounds))

    if not report:
        print("V weapons.xml ne najdeno ni odnogo predmeta assaultrifle*.")
        return 1

    mod_dir = os.path.join(game_path, "LocalMods", "AssaultRifleBuff")
    items_dir = os.path.join(mod_dir, "Items")
    os.makedirs(items_dir, exist_ok=True)

    ET.ElementTree(root).write(
        os.path.join(items_dir, "weapons_override.xml"),
        encoding="utf-8",
        xml_declaration=True,
    )

    filelist = (
        '<?xml version="1.0" encoding="utf-8"?>\n'
        '<contentpackage name="Assault Rifle Buff (%d / x%s)" modversion="1.0.0" '
        'corepackage="false" gameversion="1.9.0.0">\n'
        '  <Item file="%%ModDir%%/Items/weapons_override.xml" />\n'
        '</contentpackage>\n' % (args.rounds, fmt(args.fire_rate))
    )
    with open(os.path.join(mod_dir, "filelist.xml"), "w", encoding="utf-8") as handle:
        handle.write(filelist)

    print("")
    print("Gotovo. Mod sobran iz tvoih fajlov igry:")
    print("  %s" % mod_dir)
    print("")
    print("Izmeneniya:")
    for line in report:
        print(line)
    print("")
    print("Dalshe: zapusti igru -> Settings -> Mods -> vklyuchi 'Assault Rifle Buff' -> Apply -> perezapusk.")
    return 0


if __name__ == "__main__":
    sys.exit(main())
