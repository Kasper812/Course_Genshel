local ROUNDS = 1488
local VANILLA_ROUNDS = 30
local FIRE_RATE_MULTIPLIER = 5.0
local SCAN_INTERVAL = 2.0

local patched = {}
local nextScan = 0

local function patchItem(item)
    if item == nil or item.Prefab == nil or item.Removed then return end
    if patched[item.ID] then return end

    local identifier = tostring(item.Prefab.Identifier):lower()
    if string.find(identifier, "assaultrifle", 1, true) == nil then return end

    if string.find(identifier, "magazine", 1, true) ~= nil then
        item.HealthMultiplier = ROUNDS / VANILLA_ROUNDS
        patched[item.ID] = true
        return
    end

    local weapon = item.GetComponentString("RangedWeapon")
    if weapon ~= nil then
        weapon.Reload = weapon.Reload / FIRE_RATE_MULTIPLIER
        weapon.ReloadNoSkill = weapon.ReloadNoSkill / FIRE_RATE_MULTIPLIER
        patched[item.ID] = true
    end
end

local function scan()
    local list = Item.ItemList
    if list == nil then return end
    for i = 0, list.Count - 1 do
        pcall(patchItem, list[i])
    end
end

Hook.Add("roundStart", "assaultriflebuff.reset", function()
    patched = {}
    nextScan = 0
end)

Hook.Add("think", "assaultriflebuff.scan", function()
    local now = Timer.GetTime()
    if now < nextScan then return end
    nextScan = now + SCAN_INTERVAL
    pcall(scan)
end)
