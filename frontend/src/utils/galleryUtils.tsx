import path from "node:path";
import fs from "fs/promises";
import {ImageMetadata} from "@/interfaces/image.interface";

export async function getImagesFromFolder(folderName: string): Promise<ImageMetadata[]>{
  if(typeof window === 'undefined'){
    try{
      const directory =  path.join(process.cwd(), 'public', folderName);
      const files = await fs.readdir(directory);

      return files
        .filter(file => /\.(jpg|jpeg|png|gif|webp|svg)$/i.test(file))
        .map(file => ({
          src: `/${folderName}/${file}`,
          name: file.replace(/\.[^/.]+$/, ""),
          fullName: file
        }));
    }catch {
      return [];
    }
  }
  return [];
}

export async function generateImageMetadata(folderName: string): Promise<ImageMetadata[]> {
  const images = await getImagesFromFolder(folderName);

  // Сохраняем метаданные в JSON файл
  const metadataPath = path.join(process.cwd(), 'public', folderName, 'images.json');
  await fs.writeFile(metadataPath, JSON.stringify(images));

  return images;
}
