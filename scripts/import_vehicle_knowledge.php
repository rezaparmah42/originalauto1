<?php
/** Register PDF sources and optionally extract text. Run locally: C:\xampp\php\php.exe scripts/import_vehicle_knowledge.php [--extract] */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Core/Database.php';
$root = realpath(__DIR__ . '/../ai_sources/vehicle_knowledge');
if (!$root || !is_dir($root)) { fwrite(STDERR, "Source directory not found\n"); exit(1); }
$db = \App\Core\Database::connect();
$extract = in_array('--extract', $argv ?? [], true);
$files = glob($root . DIRECTORY_SEPARATOR . '*.pdf') ?: [];
$insert = $db->prepare('INSERT INTO knowledge_source_documents (source_path,source_name,source_hash,source_type,extracted_text,status) VALUES (?,?,?,?,?,?) ON DUPLICATE KEY UPDATE source_path=VALUES(source_path), source_name=VALUES(source_name), extracted_text=COALESCE(VALUES(extracted_text), extracted_text), updated_at=CURRENT_TIMESTAMP');
$count=0; $extracted=0;
foreach ($files as $file) {
 $hash=hash_file('sha256',$file); $text=null; $status='imported';
 if ($extract) {
  $bin=getenv('PDFTOTEXT_BIN') ?: 'pdftotext';
  $out=tempnam(sys_get_temp_dir(),'oa_pdf_');
  $cmd=escapeshellarg($bin).' -layout '.escapeshellarg($file).' '.escapeshellarg($out).' 2>NUL';
  @exec($cmd,$lines,$code);
  if ($code===0 && is_file($out)) { $text=file_get_contents($out) ?: null; if ($text!==null && trim($text)!=='') {$status='mapped';$extracted++;} }
  @unlink($out);
 }
 $insert->execute([$file,basename($file),$hash,'pdf',$text,$status]); $count++;
}
echo "Registered {$count} PDF source documents; extracted {$extracted}.\n";