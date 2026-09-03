# Assault Rifle Buff (Barotrauma)

Every vanilla assault rifle fires 5x faster and every assault rifle magazine holds 1488 rounds.
No key combinations, no custom items — the vanilla items themselves are changed.

## Two ways to install

### 1. Vanilla, no LuaCs (`vanilla/`)

Barotrauma cannot patch a single value of a vanilla item: an override has to contain the full item
definition. The scripts here build that override automatically from the game's own
`Content/Items/Weapons/weapons.xml`, so the result always matches the installed game version.

* Windows: run `vanilla/install.bat` (or `make_mod.ps1`).
* Any OS: `python3 vanilla/make_mod.py`.

The script finds the game folder, copies every `assaultrifle*` item, divides `reload` and
`reloadnoskill` by 5, scales the magazine's max condition so it lasts 1488 shots, and writes the
package to `<game>/LocalMods/AssaultRifleBuff`. Then enable it in Settings → Mods.

Options: `-Rounds` / `-FireRateMultiplier` for PowerShell, `--rounds` / `--fire-rate` for Python.

### 2. LuaCs (`lua/AssaultRifleBuff_Lua/`)

A ready-to-copy package for [LuaCsForBarotrauma](https://github.com/evilfactory/LuaCsForBarotrauma).
It patches items at runtime: `RangedWeapon.Reload` is divided by 5 and each magazine's
`HealthMultiplier` is raised to 1488/30. Copy the folder to `LocalMods` and enable it.

## How the ammo count works

A magazine has no round counter: it spends condition per shot (vanilla assault rifle magazine =
100 condition, 3.3333 per shot = 30 rounds). 1488 rounds means max condition = 3.3333 × 1488.
