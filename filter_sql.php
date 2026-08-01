<?php
$lines = file('gestion_arboles.sql');
$out = fopen('data_only.sql', 'w');
fwrite($out, "SET FOREIGN_KEY_CHECKS=0;\n");

$inInsert = false;
foreach ($lines as $line) {
    if (strpos($line, 'INSERT INTO') === 0) {
        $inInsert = true;
    }
    
    if ($inInsert) {
        fwrite($out, $line);
        $trimmed = trim($line);
        // Si la línea termina en punto y coma, significa que terminó el bloque INSERT
        if (substr($trimmed, -1) === ';') {
            $inInsert = false;
        }
    }
}

fwrite($out, "SET FOREIGN_KEY_CHECKS=1;\n");
fclose($out);
echo "Archivo data_only.sql generado con exito.\n";
