const fs = require('fs');
const path = require('path');

const filePath = "c:/Users/mauro/Desktop/App anti/resources/views/layouts/plantilla.blade.php";
let content = fs.readFileSync(filePath, 'utf8');

// Define the start and end of the block to delete
// We look for "/* Restore the A tag" and the closing "</style>"
const startMarker = "/* Restore the A tag (Component Root) Box */";
const endMarker = "</style>";

const startIndex = content.indexOf(startMarker);
const endIndex = content.indexOf(endMarker, startIndex);

if (startIndex !== -1 && endIndex !== -1) {
    console.log(`Found orphaned block from index ${startIndex} to ${endIndex}`);

    // We want to remove from startMarker up to "</style>" (plus the length of </style>)
    // And ideally clean up empty lines before it.

    const before = content.substring(0, startIndex);
    const after = content.substring(endIndex + endMarker.length);

    const newContent = before + "\n          {{-- ORPHAN CSS REMOVED HERE --}}\n" + after;

    fs.writeFileSync(filePath, newContent, 'utf8');
    console.log("Successfully removed orphaned CSS block.");
} else {
    console.log("Could not find orphaned block markers.");
    console.log("Start marker found?", startIndex !== -1);
    console.log("End marker found?", endIndex !== -1);
}
