# Assault Rifle Mk.2 (Barotrauma)

A pure vanilla content package — no LuaCs, no scripts, nothing generated locally. It adds a new
craftable item instead of touching the existing assault rifle, so installing it never conflicts with
the base game or other mods.

## What it adds

* **Assault Rifle Mk.2** (`assaultriflemk2`) — a variant of the vanilla assault rifle. Fires 5x
  faster (`reload`/`reloadnoskill` × 0.2). Fabricating it costs slightly more than the standard
  rifle (+1 to the first required material, +10s fabrication time).
* **Assault Rifle Magazine Mk.2** (`assaultriflemagazinemk2`) — a variant of the vanilla magazine,
  holding 1488 rounds instead of 30 (`health` × 49.6, i.e. 1488/30 — scales with whatever the
  vanilla per-shot cost happens to be). Also slightly pricier to fabricate.
* Both are unlocked and crafted the same way as their vanilla counterparts (same fabricator,
  same skill requirements) — just costing a bit more.

The Mk.2 rifle can still load a normal magazine, and a normal rifle can still load the Mk.2 magazine.

## Installation

1. Copy the `AssaultRifleBuff` folder to `Barotrauma/LocalMods/`.
2. Settings → Mods → enable **Assault Rifle Mk.2** → Apply → restart.
3. Craft it at a fabricator like any other weapon.

Multiplayer: enable on the server too.
