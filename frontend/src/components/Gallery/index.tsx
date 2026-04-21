"use client"

import { useState } from "react";
import Image from "next/image";


interface GalleryProps {
  productId: string,
  images?: string[];
}

export const Gallery = ({ images, productId }: GalleryProps) => {
  const [selectedImage, setSelectedImage] = useState<string | null>(null);

  if(!images || images.length === 0){
    return null;
  }
  const getImgSrc = (fileName: string) => {
    if (fileName.startsWith('/') || fileName.startsWith('http')) return fileName;
    return `${process.env.NEXT_PUBLIC_BACKEND_URL}/images/${productId}/${fileName}`.replace(/(?<!:)\/\/+/g, '/');
  };

  return (
    <div className="mt-8">
      <h3 className="text-xl font-bold mb-4">Образцы документов</h3>
      <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
          {images.map((fileName, index) => {
            const src = getImgSrc(fileName);
            return (
              <div
                key={index}
                className="relative aspect-[3/4] cursor-pointer overflow-hidden rounded-lg border border-gray-200 bg-gray-50 shadow-sm transition-all hover:opacity-80 hover:shadow-md"
                onClick={() => setSelectedImage(src)}
              >
                <Image
                  src={src}
                  alt={`Образец ${index + 1}`}
                  fill
                  className="object-cover"
                  sizes="(max-width: 768px) 50vw, (max-width: 1200px) 33vw, 25vw"
                />
              </div>
            );
          })}
      </div>
      {selectedImage && (
        <div
          className="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4 backdrop-blur-sm transition-opacity"
          onClick={() => setSelectedImage(null)}
        >
          <div className="relative w-full max-w-4xl h-[80vh] md:h-[90vh]">
            <Image
              src={selectedImage}
              alt="Увеличенный образец"
              fill
              className="object-contain"
              sizes="100vw"
              priority
            />

            <button
              className="absolute top-2 right-2 flex h-10 w-10 items-center justify-center rounded-full bg-black/50 text-white hover:bg-black/80 transition-colors"
              onClick={(e) => {
                e.stopPropagation();
                setSelectedImage(null);
              }}
              aria-label="Закрыть"
            >
              ✕
            </button>
          </div>
        </div>
      )}
    </div>
  );
}
