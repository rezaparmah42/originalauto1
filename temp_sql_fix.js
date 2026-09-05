const fs = require('fs');
const path = 'database/vehicle_knowledge_base_migration.sql';
const text = fs.readFileSync(path, 'utf8');
let updated = text;
updated = updated.replace(/1\. بازبینی شمع و کویل[\s\S]*?3\. دیاگ سنسور اکسیژن/, `1. بازبینی شمع و کویل\\n2. بررسی فشار سوخت\\n3. دیاگ سنسور اکسیژن`);
updated = updated.replace(/1\. بررسی سنسور lambda[\s\S]*?3\. بررسی مصرف سوخت/, `1. بررسی سنسور lambda\\n2. بررسی سیستم اگزوز\\n3. بررسی مصرف سوخت`);
if (updated === text) {
  console.log('No changes made. Pattern not found.');
  process.exit(1);
}
fs.writeFileSync(path, updated, 'utf8');
console.log('Fixed vehicle_diagnostics multiline SQL strings.');
