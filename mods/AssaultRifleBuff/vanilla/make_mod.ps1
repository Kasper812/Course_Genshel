param(
    [string]$GamePath = "",
    [int]$Rounds = 1488,
    [double]$FireRateMultiplier = 5.0
)

$ErrorActionPreference = "Stop"
$inv = [System.Globalization.CultureInfo]::InvariantCulture

function Get-Attr($node, $name) {
    foreach ($a in $node.Attributes) {
        if ($a.Name -ieq $name) { return $a }
    }
    return $null
}

function Set-Attr($node, $name, $value) {
    $a = Get-Attr $node $name
    if ($a -ne $null) { $a.Value = $value; return }
    $node.SetAttribute($name, $value)
}

function Get-Float($node, $name, $fallback) {
    $a = Get-Attr $node $name
    if ($a -eq $null) { return $fallback }
    $parsed = 0.0
    if ([double]::TryParse($a.Value, [System.Globalization.NumberStyles]::Any, $inv, [ref]$parsed)) { return $parsed }
    return $fallback
}

function Test-GamePath($path) {
    if ([string]::IsNullOrWhiteSpace($path)) { return $false }
    try {
        return (Test-Path -LiteralPath (Join-Path $path "Content\Items\Weapons\weapons.xml"))
    } catch {
        return $false
    }
}

function Find-GamePath {
    $candidates = New-Object System.Collections.Generic.List[string]

    foreach ($key in @("HKCU:\Software\Valve\Steam", "HKLM:\SOFTWARE\WOW6432Node\Valve\Steam", "HKLM:\SOFTWARE\Valve\Steam")) {
        try {
            $prop = Get-ItemProperty -Path $key -ErrorAction Stop
            $steam = $prop.SteamPath
            if (-not $steam) { $steam = $prop.InstallPath }
            if ($steam) {
                $steam = $steam.Replace("/", "\")
                $candidates.Add((Join-Path $steam "steamapps\common\Barotrauma"))
                $vdf = Join-Path $steam "steamapps\libraryfolders.vdf"
                if (Test-Path $vdf) {
                    foreach ($line in Get-Content $vdf) {
                        if ($line -match '"path"\s+"(.+?)"') {
                            $lib = $Matches[1].Replace("\\", "\")
                            $candidates.Add((Join-Path $lib "steamapps\common\Barotrauma"))
                        }
                    }
                }
            }
        } catch { }
    }

    foreach ($drive in @("C", "D", "E", "F", "G")) {
        $candidates.Add("${drive}:\Program Files (x86)\Steam\steamapps\common\Barotrauma")
        $candidates.Add("${drive}:\Steam\steamapps\common\Barotrauma")
        $candidates.Add("${drive}:\SteamLibrary\steamapps\common\Barotrauma")
        $candidates.Add("${drive}:\Games\Barotrauma")
    }

    foreach ($c in $candidates) {
        if (Test-GamePath $c) { return $c }
    }
    return ""
}

if (-not (Test-GamePath $GamePath)) {
    $GamePath = Find-GamePath
}

while (-not (Test-GamePath $GamePath)) {
    Write-Host ""
    Write-Host "Papka s igroj Barotrauma ne najdena avtomaticheski." -ForegroundColor Yellow
    Write-Host "Ukazhi put' k papke s igroj (tam lezhit Barotrauma.exe i papka Content)."
    $GamePath = Read-Host "Put'"
    $GamePath = $GamePath.Trim('"').Trim()
    if (-not (Test-GamePath $GamePath)) {
        Write-Host "Po etomu puti net Content\Items\Weapons\weapons.xml" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "Igra najdena: $GamePath" -ForegroundColor Green

$sourceFile = Join-Path $GamePath "Content\Items\Weapons\weapons.xml"
[xml]$source = Get-Content -LiteralPath $sourceFile -Raw -Encoding UTF8

$matched = @()
foreach ($item in $source.SelectNodes("//Item")) {
    $idAttr = Get-Attr $item "identifier"
    if ($idAttr -eq $null) { continue }
    if ($idAttr.Value.ToLowerInvariant().StartsWith("assaultrifle")) { $matched += $item }
}

if ($matched.Count -eq 0) {
    Write-Host "V weapons.xml ne najdeno ni odnogo predmeta assaultrifle*." -ForegroundColor Red
    exit 1
}

$outDoc = New-Object System.Xml.XmlDocument
$decl = $outDoc.CreateXmlDeclaration("1.0", "utf-8", $null)
$outDoc.AppendChild($decl) | Out-Null
$root = $outDoc.CreateElement("Items")
$outDoc.AppendChild($root) | Out-Null
$override = $outDoc.CreateElement("Override")
$root.AppendChild($override) | Out-Null

$assetExt = @(".png", ".ogg", ".wav", ".xml", ".webm", ".jpg")
$report = @()

foreach ($item in $matched) {
    $copy = $outDoc.ImportNode($item, $true)
    $override.AppendChild($copy) | Out-Null

    $identifier = (Get-Attr $copy "identifier").Value

    foreach ($node in @($copy) + @($copy.SelectNodes(".//*"))) {
        foreach ($a in $node.Attributes) {
            $v = $a.Value
            if ([string]::IsNullOrWhiteSpace($v)) { continue }
            if ($v.Contains("/") -or $v.Contains("\")) { continue }
            $ext = [System.IO.Path]::GetExtension($v).ToLowerInvariant()
            if ($assetExt -contains $ext) {
                $a.Value = "Content/Items/Weapons/" + $v
            }
        }
    }

    $isWeapon = $false
    foreach ($w in $copy.SelectNodes(".//*")) {
        if ($w.Name -inotmatch "^rangedweapon$") { continue }
        $isWeapon = $true
        $reload = Get-Float $w "reload" 1.0
        $reloadNoSkill = Get-Float $w "reloadnoskill" 1.0
        Set-Attr $w "reload" (($reload / $FireRateMultiplier).ToString("0.#####", $inv))
        Set-Attr $w "reloadnoskill" (($reloadNoSkill / $FireRateMultiplier).ToString("0.#####", $inv))
        $report += "  $identifier : reload $reload -> " + (($reload / $FireRateMultiplier).ToString("0.#####", $inv))
    }

    if (-not $isWeapon) {
        $cost = 0.0
        foreach ($effect in $copy.SelectNodes(".//*")) {
            if ($effect.Name -inotmatch "^statuseffect$") { continue }
            $c = Get-Attr $effect "Condition"
            if ($c -eq $null) { continue }
            $parsed = 0.0
            if ([double]::TryParse($c.Value, [System.Globalization.NumberStyles]::Any, $inv, [ref]$parsed)) {
                if ($parsed -lt 0 -and $cost -eq 0.0) { $cost = [math]::Abs($parsed) }
            }
        }

        $oldHealth = Get-Float $copy "health" 100.0
        if ($cost -gt 0.0) {
            $newHealth = $cost * $Rounds
            $oldRounds = [math]::Floor($oldHealth / $cost)
        } else {
            $newHealth = $oldHealth * ($Rounds / 30.0)
            $oldRounds = 30
        }
        Set-Attr $copy "health" ($newHealth.ToString("0.#####", $inv))
        $report += "  $identifier : patronov $oldRounds -> $Rounds"
    }
}

$modName = "AssaultRifleBuff"
$modDir = Join-Path $GamePath ("LocalMods\" + $modName)
$itemsDir = Join-Path $modDir "Items"
New-Item -ItemType Directory -Force -Path $itemsDir | Out-Null

$settings = New-Object System.Xml.XmlWriterSettings
$settings.Indent = $true
$settings.Encoding = New-Object System.Text.UTF8Encoding($false)
$writer = [System.Xml.XmlWriter]::Create((Join-Path $itemsDir "weapons_override.xml"), $settings)
$outDoc.Save($writer)
$writer.Close()

$filelist = @"
<?xml version="1.0" encoding="utf-8"?>
<contentpackage name="Assault Rifle Buff ($Rounds / x$FireRateMultiplier)" modversion="1.0.0" corepackage="false" gameversion="1.9.0.0">
  <Item file="%ModDir%/Items/weapons_override.xml" />
</contentpackage>
"@
[System.IO.File]::WriteAllText((Join-Path $modDir "filelist.xml"), $filelist, (New-Object System.Text.UTF8Encoding($false)))

Write-Host ""
Write-Host "Gotovo. Mod sobran iz tvoih fajlov igry:" -ForegroundColor Green
Write-Host "  $modDir"
Write-Host ""
Write-Host "Izmeneniya:" -ForegroundColor Green
$report | ForEach-Object { Write-Host $_ }
Write-Host ""
Write-Host "Dalshe: zapusti igru -> Settings -> Mods -> vklyuchi 'Assault Rifle Buff' -> Apply -> perezapusk." -ForegroundColor Cyan
Write-Host ""
