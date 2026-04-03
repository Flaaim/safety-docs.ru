import type {Metadata} from "next";
import {Htag, Ptag, Listtag} from "@/components";
import React from "react";
import {ProductInfo} from "@/components/ProductInfo/ProductInfo";
import Breadcrumbs from "@/components/Breadcrumb/Breadcrumbs";



export const metadata: Metadata = {
  title: "Образцы документов Аптечкам",
  description: "Документы по организации обеспечения рабочих мест аптечками для оказания первой помощи пострадавшим",
};

export default function FirstAid(){
  return (
    <div>
      <Breadcrumbs path="/safety/firstaid" />
      <Htag tag='h1'>Аптечки - комплект документов</Htag>
      <ProductInfo
        slug='firstaid'
        countFiles=' файла'
        formatFiles='docx, excel'
        description='
        '>
      </ProductInfo>
      <Htag tag='h2'>1. Аптечки первой помощи</Htag>
      <Ptag appearance='bold'>Как организовать:</Ptag>
      <Ptag>
        Назначьте приказом работника, ответственного за приобретение, хранение и комплектацию аптечек. Отдельным приказом утвердите требования к их комплектации, местам хранения и периодичности проверок. Для контроля заполняйте заявки на приобретение недостающих изделий и фиксируйте выдачу аптечек в подразделения в журнале учёта. Утвердите инструкцию по оказанию первой помощи с пояснениями по применению каждого изделия.
      </Ptag>
      <Htag tag='h3'>Образцы документов</Htag>
      <Listtag appearance='ol'>
        <span>Журнал регистрации использования изделий медицинского назначения при оказании первой помощи</span>
        <span>Журнал учета выдачи аптечек для оказания первой помощи</span>
        <span>Заявка о приобретении изделий медицинского назначения</span>
        <span>Инструкция по оказанию первой помощи с применением аптечки для оказания первой помощи работникам</span>
        <span>Приказ о назначении лица, ответственного за медицинскую аптечку первой помощи в организации</span>
        <span>Приказ о создании санитарных постов с аптечками</span>
        <span>Приказ об аптечках первой помощи</span>
        <span>Приказ об утверждении инструкции по оказанию первой помощи с применением аптечки</span>
      </Listtag>
      <hr/>
    </div>
  );
}
