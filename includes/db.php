<?php
require_once __DIR__ . '/config.php';

function dbSelect(string $sql, string $types='', ...$params): array {
    $db=$db=getDB(); $stmt=$db->prepare($sql); if(!$stmt) return [];
    if($types&&$params) $stmt->bind_param($types,...$params);
    $stmt->execute(); $rows=[];
    $res=$stmt->get_result();
    while($row=$res->fetch_assoc()) $rows[]=$row;
    $stmt->close(); return $rows;
}
function dbSelectOne(string $sql, string $types='', ...$params): ?array {
    $r=dbSelect($sql,$types,...$params); return $r[0]??null;
}
function dbExecute(string $sql, string $types='', ...$params): int {
    $db=getDB(); $stmt=$db->prepare($sql); if(!$stmt) return 0;
    if($types&&$params) $stmt->bind_param($types,...$params);
    $stmt->execute(); $a=$stmt->affected_rows; $stmt->close(); return $a;
}
function nowDate(): string { return date('F j, Y'); }
function nowTime(): string { return date('h:i A'); }