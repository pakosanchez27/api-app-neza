import fs from 'node:fs/promises';
import path from 'node:path';
import sharp from 'sharp';

const [, , inputPath, outputPath] = process.argv;

if (!inputPath || !outputPath) {
    console.error('Usage: node scripts/convert-image-to-webp.mjs <input> <output>');
    process.exit(1);
}

try {
    await fs.mkdir(path.dirname(outputPath), { recursive: true });

    await sharp(inputPath)
        .rotate()
        .webp({
            quality: 82,
            effort: 4,
        })
        .toFile(outputPath);
} catch (error) {
    console.error(error instanceof Error ? error.message : String(error));
    process.exit(1);
}
