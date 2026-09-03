# GOY Assault Rifle (Barotrauma)

Press **G → O → Y** in quick succession while in a round and the mod puts a modified assault rifle,
loaded with a 100-round magazine, into your inventory.

## Contents

| File | Purpose |
| --- | --- |
| `filelist.xml` | Content package definition |
| `Items/goy_weapons.xml` | `goy_assaultrifle` and `goy_assaultriflemagazine` item variants |
| `Lua/Autorun/goy_assaultrifle.lua` | Key sequence detection, item spawning, fire rate tuning |

## What it changes

* `goy_assaultriflemagazine` is a variant of the vanilla assault rifle magazine with `health="100"`,
  which is 100 rounds (one round consumes one point of magazine condition).
* `goy_assaultrifle` is a variant of the vanilla assault rifle that accepts the extended magazine.
* Fire rate is applied at runtime: the `RangedWeapon.Reload` value inherited from the vanilla rifle is
  divided by 2, so the weapon always fires exactly twice as fast as the current vanilla assault rifle,
  independent of the game version.

## Requirements

* [LuaCsForBarotrauma](https://github.com/evilfactory/LuaCsForBarotrauma) — needed for the key
  combination; the Lua part also has to be enabled in the LuaCs settings (`cl_lua` / server `Lua` enabled).

## Installation

1. Copy the `GOY_AssaultRifle` folder to `Barotrauma/LocalMods/`.
2. Launch the game, open **Settings → Mods**, enable **GOY Assault Rifle**, apply and restart.
3. In multiplayer the mod has to be enabled on the server as well (the client sends a network message,
   the server spawns the items).

## Usage

Start a round, press `G`, `O`, `Y` one after another (max 1.5 seconds between the keys, chat must be
closed). The rifle with the loaded 100-round magazine appears in your inventory.
