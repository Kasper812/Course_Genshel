local RIFLE_ID = "goy_assaultrifle"
local MAG_ID = "goy_assaultriflemagazine"
local NET_ID = "goy_assaultrifle_give"
local SEQUENCE_TIMEOUT = 1.5
local FIRE_RATE_MULTIPLIER = 2.0

local baseReload = nil

local function getPrefab(id)
    local ok, prefab = pcall(function() return ItemPrefab.GetItemPrefab(id) end)
    if ok and prefab ~= nil then return prefab end
    ok, prefab = pcall(function() return ItemPrefab.Prefabs[id] end)
    if ok then return prefab end
    return nil
end

local function tuneRifle(item)
    if item == nil then return end
    if item.Prefab == nil or tostring(item.Prefab.Identifier) ~= RIFLE_ID then return end
    local weapon = item.GetComponentString("RangedWeapon")
    if weapon == nil then return end
    if baseReload == nil then baseReload = weapon.Reload end
    weapon.Reload = baseReload / FIRE_RATE_MULTIPLIER
end

local function tuneInventory(inventory, depth)
    if inventory == nil or depth > 2 then return end
    for i = 0, inventory.Capacity - 1 do
        local item = inventory.GetItemAt(i)
        if item ~= nil then
            tuneRifle(item)
            tuneInventory(item.OwnInventory, depth + 1)
        end
    end
end

local function notify(text)
    pcall(function()
        if CLIENT then
            GUI.AddMessage(text, Color.LightGreen)
        else
            print(text)
        end
    end)
end

local function giveRifle(character)
    if character == nil or character.Inventory == nil or character.IsDead then return end
    if Entity.Spawner == nil then return end

    local riflePrefab = getPrefab(RIFLE_ID)
    local magPrefab = getPrefab(MAG_ID)
    if riflePrefab == nil or magPrefab == nil then
        notify("GOY: mod items not found, is the content package enabled?")
        return
    end

    Entity.Spawner.AddItemToSpawnQueue(riflePrefab, character.Inventory, nil, nil, function(rifle)
        if rifle == nil then return end
        tuneRifle(rifle)
        if rifle.OwnInventory ~= nil then
            Entity.Spawner.AddItemToSpawnQueue(magPrefab, rifle.OwnInventory)
        end
    end)
end

if SERVER then
    Networking.Receive(NET_ID, function(_, client)
        if client == nil then return end
        giveRifle(client.Character)
    end)
end

if CLIENT then
    local keys = { Keys.G, Keys.O, Keys.Y }
    local step = 1
    local lastKeyTime = 0
    local tuneTimer = 0

    local function request()
        if Game.IsMultiplayer then
            local msg = Networking.Start(NET_ID)
            Networking.Send(msg)
        else
            giveRifle(Character.Controlled)
        end
        notify("GOY: assault rifle delivered.")
    end

    local function typingInChat()
        local ok, busy = pcall(function()
            return GUI.KeyboardDispatcher ~= nil and GUI.KeyboardDispatcher.Subscriber ~= nil
        end)
        return ok and busy
    end

    Hook.Add("think", "goy.assaultrifle.input", function()
        local now = Timer.GetTime()

        if tuneTimer < now then
            tuneTimer = now + 0.5
            if Character.Controlled ~= nil then
                tuneInventory(Character.Controlled.Inventory, 0)
            end
        end

        if Character.Controlled == nil or Character.Controlled.IsDead or typingInChat() then
            step = 1
            return
        end

        if step > 1 and now - lastKeyTime > SEQUENCE_TIMEOUT then step = 1 end

        for index = 1, #keys do
            if PlayerInput.KeyHit(keys[index]) then
                lastKeyTime = now
                if keys[index] == keys[step] then
                    step = step + 1
                    if step > #keys then
                        step = 1
                        request()
                    end
                elseif keys[index] == keys[1] then
                    step = 2
                else
                    step = 1
                end
                break
            end
        end
    end)
end
