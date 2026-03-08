'use client'

import * as React from "react"
import { useState } from "react";
import { Card, CardContent } from "@/components/ui/card"
import {
  Carousel,
  CarouselContent,
  CarouselItem,
  CarouselNext,
  CarouselPrevious,
} from "@/components/ui/carousel"
import {ImageCarouselProps} from "@/components/Gallery/Carusel/ImageCarousel.props";
import styles from "./ImageCarousel.module.css";



export function ImageCarousel({images, title}: ImageCarouselProps) {
  const [selectedImg, setSelectedImg] = useState<string | null>(null);

  if (!images || images.length === 0) {
    return <div className={styles.emptyState}>Нет изображений для отображения</div>;
  }

  return (
    <div className={styles.wrapper}>
      {title && <h3 className={styles.title}>{title}</h3>}
    <Carousel
      opts={{ align: "start", loop: true }}
      className="w-full max-w-4xl mx-auto"
    >
      <CarouselContent className="-ml-2 md:-ml-4">
        {images.map((image, index) => (
          <CarouselItem key={index} className="pl-2 md:pl-4 basis-full sm:basis-1/2 lg:basis-1/3">
            <div className="p-1">
              <Card
                className={styles.imageCard}
                onClick={() => setSelectedImg(image.src)}
              >
                <CardContent className="flex aspect-video items-center justify-center p-0 overflow-hidden rounded-md">
                  <img
                    src={image.src}
                    alt={image.name}
                    className={styles.image}
                    loading="lazy"
                  />
                </CardContent>
              </Card>
              <p className={styles.imageCaption}>{image.name}</p>
            </div>
          </CarouselItem>
        ))}
      </CarouselContent>
      <CarouselPrevious className="-left-12 hidden md:flex" />
      <CarouselNext className="-right-12 hidden md:flex" />
    </Carousel>

      {selectedImg && (
        <div className={styles.lightbox} onClick={() => setSelectedImg(null)}>
          <button className={styles.closeButton}>&times;</button>
          <div className={styles.lightboxContent}>
            <img src={selectedImg} className={styles.lightboxImage} alt="Zoomed" />
          </div>
        </div>
      )}
    </div>
  )
}
