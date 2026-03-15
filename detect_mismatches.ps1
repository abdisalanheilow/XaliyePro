$viewDir = "d:\ERPInventory\resources\views"
$pairs = @(
    @("if", "endif"),
    @("foreach", "endforeach"),
    @("forelse", "endforelse"),
    @("while", "endwhile"),
    @("switch", "endswitch"),
    @("unless", "endunless"),
    @("php", "endphp"),
    @("section", "endsection"),
    @("push", "endpush")
)

$files = Get-ChildItem -Path $viewDir -Include "*.blade.php" -Recurse

foreach ($file in $files) {
    try {
        $content = [System.IO.File]::ReadAllText($file.FullName)
        foreach ($pair in $pairs) {
            $start = $pair[0]
            $end = $pair[1]
            
            $startCount = ([regex]::Matches($content, "(?<!@)@$start\b")).Count
            $endCount = ([regex]::Matches($content, "(?<!@)@$end\b")).Count
            
            if ($startCount -ne $endCount) {
                Write-Host "MISMATCH in $($file.FullName): @$start ($startCount) vs @$end ($endCount)"
            }
        }
    } catch {}
}
