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
$report = @()

foreach ($file in $files) {
    $content = [System.IO.File]::ReadAllText($file.FullName)
    foreach ($pair in $pairs) {
        $startCount = ([regex]::Matches($content, "(?<!@)@$($pair[0])\b")).Count
        $endCount = ([regex]::Matches($content, "(?<!@)@$($pair[1])\b")).Count
        if ($startCount -ne $endCount) {
            $report += "MISMATCH: $($file.FullName) | @$($pair[0]) ($startCount) vs @$($pair[1]) ($endCount)"
        }
    }
}

$report | Out-File -FilePath "d:\ERPInventory\mismatch_report_final.txt" -Encoding utf8
"Done. Report saved to mismatch_report_final.txt"
