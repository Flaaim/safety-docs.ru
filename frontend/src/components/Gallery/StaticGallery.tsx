'use client';

import {StaticGalleryProps} from "@/components/Gallery/StaticGallery.props";
import {useState} from "react";
import styles from './StaticGallery.module.css';

export const StaticGallery = ({images, title}:StaticGalleryProps) => {
  const [selectedImage, setSelectedImage] = useState<string | null>(null);

  if (!images || images.length === 0) {
    return (
      <div className={styles.emptyState}>
        Нет изображений для отображения
      </div>
    );
  }
  console.log(images)
  return (
    <>
      <div className={styles.container}>
        <h3 className={styles.title}>{title}</h3>
        <div className={styles.grid}>
          {images.map((image, index) => (
            <button
              key={index}
              onClick={() => setSelectedImage(image.src)}
              className={styles.imageButton}
              aria-label={`Просмотреть ${image.name}`}
            >
              <img
                src={image.src}
                alt={image.name}
                className={styles.image}
                loading="lazy"
              />
            </button>
          ))}
        </div>
      </div>
      {selectedImage && (
        <div
          className={styles.lightbox}
          onClick={() => setSelectedImage(null)}
        >
          <div className={styles.lightboxContent}>
            <img
              src={selectedImage}
              alt="Просмотр"
              className={styles.lightboxImage}
            />
            <button
              className={styles.closeButton}
              onClick={(e) => {
                e.stopPropagation();
                setSelectedImage(null);
              }}
              aria-label="Закрыть"
            >
              ×
            </button>
          </div>
        </div>
      )}
    </>
  )

}
