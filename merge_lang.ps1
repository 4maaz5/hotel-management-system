$hrPath = "F:\Reservation+HRM\hr-multi-branch-hotel-management\resources\lang"
$resPath = "F:\Reservation+HRM\Reservation-management\resources\lang"

# 1. Merge messages.php (en + ar)
function Merge-Messages {
    param($hrFile, $resFile, $outputFile, $label)

    $hr = php -r "return " + (Get-Content $hrFile -Raw) + ";"
    $res = php -r "return " + (Get-Content $resFile -Raw) + ";"

    $merged = @{}
    $hr.GetEnumerator() | ForEach-Object { $merged[$_.Key] = $_.Value }
    $res.GetEnumerator() | ForEach-Object {
        if (-not $merged.ContainsKey($_.Key)) {
            $merged[$_.Key] = $_.Value
        }
    }

    # Generate PHP code
    $phpCode = "<?php`n`nreturn [`n"
    $keys = $merged.Keys | Sort-Object
    foreach ($key in $keys) {
        $val = $merged[$key] -replace "'", "\'"
        $phpCode += "    '$key' => '$val',`n"
    }
    $phpCode += "];`n"

    Set-Content -Path $outputFile -Value $phpCode -Encoding UTF8
    Write-Output "$label: $($keys.Count) keys merged"
}

Merge-Messages -hrFile "$hrPath\en\messages.php" -resFile "$resPath\en\messages.php" -outputFile "$hrPath\en\messages.php" -label "EN messages"
Merge-Messages -hrFile "$hrPath\ar\messages.php" -resFile "$resPath\ar\messages.php" -outputFile "$hrPath\ar\messages.php" -label "AR messages"

# 2. Create en/auth.php (remove empty Auth.php)
$resAuth = php -r "return " + (Get-Content "$resPath\en\auth.php" -Raw) + ";"
$phpCode = "<?php`n`nreturn [`n"
foreach ($key in ($resAuth.Keys | Sort-Object)) {
    $val = $resAuth[$key] -replace "'", "\'"
    $phpCode += "    '$key' => '$val',`n"
}
$phpCode += "];`n"
Set-Content -Path "$hrPath\en\auth.php" -Value $phpCode -Encoding UTF8
Write-Output "EN auth.php created: $($resAuth.Keys.Count) keys"

# Remove empty Auth.php (capital A)
Remove-Item "$hrPath\en\Auth.php" -ErrorAction SilentlyContinue
Write-Output "Removed empty Auth.php"

# Create ar/auth.php
$resArAuth = php -r "return " + (Get-Content "$resPath\ar\auth.php" -Raw) + ";"
$phpCode = "<?php`n`nreturn [`n"
foreach ($key in ($resArAuth.Keys | Sort-Object)) {
    $val = $resArAuth[$key] -replace "'", "\'"
    $phpCode += "    '$key' => '$val',`n"
}
$phpCode += "];`n"
Set-Content -Path "$hrPath\ar\auth.php" -Value $phpCode -Encoding UTF8
Write-Output "AR auth.php created: $($resArAuth.Keys.Count) keys"

# 3. Create en/validation.php from reservation project
$resVal = php -r "return " + (Get-Content "$resPath\en\validation.php" -Raw) + ";"
# Add HR-specific attributes
$existingAttrs = $resVal['attributes']
$hrAttrs = @{
    'name' = 'name'
    'email' = 'email address'
    'password' = 'password'
    'message' = 'message'
    'session_id' = 'session'
    'user_id' = 'user'
}
foreach ($kv in $hrAttrs.GetEnumerator()) {
    if (-not $existingAttrs.ContainsKey($kv.Key)) {
        $resVal['attributes'][$kv.Key] = $kv.Value
    }
}
$phpCode = php -r "
\$data = " + (ConvertTo-Json $resVal -Depth 5) + ";
echo var_export(\$data, true);
"
$fullCode = "<?php`n`nreturn " + $phpCode + ";`n"
# Fix stdClass syntax
$fullCode = $fullCode -replace "stdClass::__set_state\(array\(", "["
$fullCode = $fullCode -replace "\)\)", "])"
Set-Content -Path "$hrPath\en\validation.php" -Value $fullCode -Encoding UTF8
Write-Output "EN validation.php created"

# 4. Merge ar/validation.php
$hrArVal = php -r "return " + (Get-Content "$hrPath\ar\validation.php" -Raw) + ";"
$resArVal = php -r "return " + (Get-Content "$resPath\ar\validation.php" -Raw) + ";"
$mergedVal = @{}
$hrArVal.GetEnumerator() | ForEach-Object { $mergedVal[$_.Key] = $_.Value }
$resArVal.GetEnumerator() | ForEach-Object {
    if (-not $mergedVal.ContainsKey($_.Key)) {
        $mergedVal[$_.Key] = $_.Value
    }
}
# For 'attributes', merge both
if ($mergedVal.ContainsKey('attributes') -and $hrArVal.ContainsKey('attributes') -and $resArVal.ContainsKey('attributes')) {
    $attrs = @{}
    $hrArVal['attributes'].GetEnumerator() | ForEach-Object { $attrs[$_.Key] = $_.Value }
    $resArVal['attributes'].GetEnumerator() | ForEach-Object {
        if (-not $attrs.ContainsKey($_.Key)) {
            $attrs[$_.Key] = $_.Value
        }
    }
    $mergedVal['attributes'] = $attrs
}
$phpCode = php -r "
\$data = " + (ConvertTo-Json $mergedVal -Depth 5) + ";
echo var_export(\$data, true);
"
$fullCode = "<?php`n`nreturn " + $phpCode + ";`n"
$fullCode = $fullCode -replace "stdClass::__set_state\(array\(", "["
$fullCode = $fullCode -replace "\)\)", "])"
Set-Content -Path "$hrPath\ar\validation.php" -Value $fullCode -Encoding UTF8
Write-Output "AR validation.php merged"

Write-Output "`nAll done!"
