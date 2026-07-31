<?php
$lines = file('gestion_arboles.sql');
$out = fopen('data_only.sql', 'w');
fwrite($out, "SET FOREIGN_KEY_CHECKS=0;\n");
foreach ($lines as $line) {
    if (strpos($line, 'INSERT INTO') === 0) {
        fwrite($out, $line);
    }
}
fwrite($out, "SET FOREIGN_KEY_CHECKS=1;\n");
fclose($out);
echo "Archivo data_only.sql generado con exito.\n";
