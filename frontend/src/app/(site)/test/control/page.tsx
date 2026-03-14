import type {Metadata} from "next";
import {getImagesFromFolder} from "@/utils/galleryUtils";
import Breadcrumbs from "@/components/Breadcrumb/Breadcrumbs";
import React from "react";
import {Htag, Listtag, Ptag, Spantag} from "@/components";
import {ProductInfo} from "@/components/ProductInfo/ProductInfo";
import {ImageCarousel} from "@/components/Gallery/Carusel/ImageCarousel";



export const metadata: Metadata = {
  title: "Контроль охраны труда - образцы документов",
  description: "Образцы документов для организации контроля за соблюдением требований охраны труда в организации.",
};

export default async function Control() {

  const images = await getImagesFromFolder('control');

  return (
    <div>
      <Breadcrumbs path="/safety/control"/>
      <Htag tag='h1'>Контроль за соблюдением охраны труда - комплект документов</Htag>
      <ProductInfo
        slug='control'
        countFiles='7 файлов'
        formatFiles='docx'
        description='Комплект документов'>
      </ProductInfo>
      <ImageCarousel images={images} title='Примеры документов по контролю за соблюдением требований охраны труда'></ImageCarousel>
      <Ptag appearance='bold'>Как организовать:</Ptag>
      <Ptag>Выберите вид контроля в зависимости от специфики организации. При любой системе контроля при выявлении нарушений требований охраны труда выдавайте предписания об их устранении.</Ptag>

        <Ptag>Для постоянного, периодического и реагирующего контроля:
          Оформляйте акты проверки состояния охраны труда или ведите журнал проверки условий труда. Это обеспечит обратную связь от подразделений и повысит эффективность СУОТ.</Ptag>

      <Ptag>Для трёхступенчатого контроля:
        Распределите приказом ответственность по трём уровням: работники, руководители подразделений, работодатель. Утвердите Положение, где закрепите структуру и обязанности каждого уровня. Результаты каждой ступени оформляйте актами и фиксируйте в журнале.</Ptag>
      <Htag tag='h4'>Образцы документов</Htag>
      <Listtag appearance='ol'>
        <span>Акт проведения трехступенчатого контроля за состоянием охраны труда</span>
        <span>Журнал проверки состояния условий труда</span>
        <span>Журнал трехступенчатого контроля за соблюдением требований охраны труда</span>
        <span>Положение об осуществлении контроля за охраной труда</span>
        <span>Предписание специалиста службы охраны труда</span>
        <span>Приказ о введении трехступенчатого контроля над состоянием условий и безопасности труда</span>
      </Listtag>
    </div>
  )
}
