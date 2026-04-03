import type {Metadata} from "next";
import {Htag, Ptag, Listtag, StaticGallery} from "@/components";
import React from "react";
import {ProductInfo} from "@/components/ProductInfo/ProductInfo";
import {getImagesFromFolder} from "@/utils/galleryUtils";
import Breadcrumbs from "@/components/Breadcrumb/Breadcrumbs";
import {ImageCarousel} from "@/components/Gallery/Carusel/ImageCarousel";



export const metadata: Metadata = {
  title: "Образцы документов службы охраны труда",
  description: "Собраны комплекты образцов документов по организации на предприятии службы охраны труда",
};

export default async function Service() {



  const images = await getImagesFromFolder('service');

  return (
    <div>
      <Breadcrumbs path="/safety/service" />
      <Htag tag='h1'>Служба охраны труда - образцы документов</Htag>
      <ProductInfo
        slug='service'
        countFiles='3 файла'
        formatFiles='docx'
        description='Комлект документов'>
      </ProductInfo>
      <ImageCarousel images={images} title='Примеры документов службы охраны труда'></ImageCarousel>
      <Htag tag='h2'>Описание</Htag>
      <Ptag appearance='bold'>Как организовать:</Ptag>
      <Ptag>
        Оцените численность штата. Если в организации более 50 человек — создайте службу охраны труда или введите
        должность специалиста, оформив приказ и утвердив Положение о службе.
      </Ptag>
      <Htag tag='h4'>Что в комплекте?</Htag>
      <Listtag appearance='ol'>
        <span>Приказ о назначении специалиста по охране труда</span>
        <span>Приказ о создании службы охраны труда</span>
        <span>Положение о службе охраны труда в организации</span>
      </Listtag>
    </div>
  );
}
