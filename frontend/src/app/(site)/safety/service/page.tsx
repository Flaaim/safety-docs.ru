import type {Metadata} from "next";
import {Htag, Section, Navigation, Ptag, Listtag} from "@/components";
import Link from "next/link";
import React from "react";
import {ProductInfo} from "@/components/ProductInfo/ProductInfo";



export const metadata: Metadata = {
  title: "Образцы документов службы охраны труда",
  description: "Собраны комплекты образцов документов по организации на предприятии службы охраны труда",
};

export default function Service(){
  return (
    <div>
      <Htag tag='h1'>Служба охраны труда - образцы документов</Htag>
      <ProductInfo
        slug='service'
        countFiles='3 файла'
        formatFiles='docx'
        description='Комлект документов'>
      </ProductInfo>
      <Ptag appearance='bold'>Как организовать:</Ptag>
      <Ptag>
        Оцените численность штата. Если в организации более 50 человек — создайте службу охраны труда или введите должность специалиста, оформив приказ и утвердив Положение о службе.
      </Ptag>
      <Htag tag='h4'>Образцы документов</Htag>
      <Listtag appearance='ol'>
        <span>Приказ о назначении специалиста по охране труда</span>
        <span>Приказ о создании службы охраны труда</span>
        <span>Положение о службе охраны труда в организации</span>
      </Listtag>

      <hr/>
    </div>
  )
}
