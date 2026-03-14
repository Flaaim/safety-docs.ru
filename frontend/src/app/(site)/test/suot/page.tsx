import type {Metadata} from "next";
import {Htag, Section, Navigation, Ptag, Listtag} from "@/components";
import Link from "next/link";
import React from "react";
import {ProductInfo} from "@/components/ProductInfo/ProductInfo";
import {getImagesFromFolder} from "@/utils/galleryUtils";
import {ImageCarousel} from "@/components/Gallery/Carusel/ImageCarousel";



export const metadata: Metadata = {
  title: "Образцы документов Системы управления охраной труда",
  description: "Документы по организации системы управления охраной труда.",
};

export default async function Suot() {

  const images = await getImagesFromFolder('suot');

  return (
    <div>
      <Htag tag='h1'>СУОТ - образцы документов</Htag>
      <ProductInfo
        slug='suot'
        countFiles='7 файлов'
        formatFiles='docx'
        description='
      ✅ Приказы (назначение специалиста, создание службы, утверждение СУОТ, ответственные по подразделениям, утверждение Политики)
      ✅ Положения (о службе охраны труда, о СУОТ, для микропредприятий)
      ✅ Политика в области охраны труда
      ✅ Комплект нормативно-правовых актов с требованиями охраны труда
        '>
      </ProductInfo>
      <ImageCarousel images={images} title='Примеры документов Системы управления охраной труда'></ImageCarousel>
      <Ptag appearance='bold'>Как организовать:</Ptag>
      <Ptag>
        Разработайте и утвердите Положение о СУОТ, опираясь на примерное положение. Закрепите процедуры по охране труда
        в локальных нормативных актах. Приказом распределите обязанности между руководителями всех уровней — это снизит
        нагрузку на службу охраны труда. Сформулируйте Политику по охране труда (отдельно или в составе Положения),
        включив в неё цели, задачи и ключевые показатели на текущий год.
      </Ptag>
      <Htag tag='h4'>Образцы документов</Htag>
      <Listtag appearance='ol'>
        <span>Положение о системе управления охраной труда</span>
        <span>Положение о системе управления охраной труда для микропредприятий и организаций с офисными работника</span>
        <span>Приказ об утверждении Положения о системе управления охраной труда</span>
        <span>Приказ о назначении ответственных за охрану труда по подразделениям</span>
        <span>Политика в области охраны труда</span>
        <span>Приказ об утверждении Политики в области охраны труда</span>
        <span>Комплект нормативно-правовых актов с требованиями охраны труда</span>
      </Listtag>

      <hr/>
    </div>
  )
}
