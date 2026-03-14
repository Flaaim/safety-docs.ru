import type {Metadata} from "next";
import {getImagesFromFolder} from "@/utils/galleryUtils";
import Breadcrumbs from "@/components/Breadcrumb/Breadcrumbs";
import React from "react";
import {Htag, Listtag, Ptag} from "@/components";
import {ProductInfo} from "@/components/ProductInfo/ProductInfo";
import {ImageCarousel} from "@/components/Gallery/Carusel/ImageCarousel";



export const metadata: Metadata = {
  title: "Электробезопасность 1 группа - образцы документов",
  description: "Образцы документов для организации присвоения 1 группы по электробезопасности для неэлектротехнического персонала",
};

export default async function Electrical() {

  const images = await getImagesFromFolder('electrical');

  return (
    <div>
      <Breadcrumbs path="/safety/electrical"/>
      <Htag tag='h1'>Электробезопасность 1 группа - комплект документов</Htag>
      <ProductInfo
        slug='electrical'
        countFiles='4 файлов'
        formatFiles='docx'
        description='Журнал, программа, приказы'>
      </ProductInfo>
      <ImageCarousel images={images} title='Примеры документов по присвоению 1 группы по электробезопасности'></ImageCarousel>
      <Ptag appearance='bold'>Как организовать:</Ptag>
      <Ptag>Определите перечень должностей и профессий, работникам которых необходимо присвоить I группу по электробезопасности, и утвердите его приказом. Назначьте ответственного за проведение инструктажа, разработайте программу обучения. Результаты присвоения I группы фиксируйте в журнале учёта.</Ptag>
      <Htag tag='h4'>Образцы документов</Htag>
      <Listtag appearance='ol'>
        <span>Журнал учета присвоения группы I по электробезопасности неэлектротехническому персоналу</span>
        <span>Приказ о назначении ответственного за присвоение неэлектротехническому персоналу I группы по ЭБ</span>
        <span>Приказ об утверждении перечня должностей и профессий для присвоения персоналу I группы по электробезопасности</span>
        <span>Программа проведения инструктажа неэлектротехнического персонала на группу I по электробезопасности</span>
      </Listtag>
    </div>
  )
}
