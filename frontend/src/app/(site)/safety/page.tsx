import type {Metadata} from "next";
import {Htag, Listtag, Ptag} from "@/components";
import Link from "next/link";
import React from "react";



export const metadata: Metadata = {
  title: "Документация по направлениям Охраны труда",
  description: "Собраны комплекты образцов документов по основным направлениями работы по охране труда и техники безопасности.",
};

export default function Safety(){
  return (
    <div>
      <Htag tag='h1'>Охрана труда — документация по направлениям</Htag>
      <Ptag> Вопросы безопасности на рабочем месте требуют скрупулезного подхода к бумажной работе. Чтобы вам было проще ориентироваться в мире нормативных актов и приказов по охране труда, я собрал полный список документации по охране труда, распределив её по основным направлениям работы организации.

        Все образцы документов, представленные на сайте, актуальны на 2026 год и могут быть адаптированы под специфику вашего производства.</Ptag>
      <Ptag>В каждом разделе добавлена памятка как организовать работу по направлению и перечень документов, которые необходимы</Ptag>
      <Ptag appearance='bold'>Документация: </Ptag>
      <hr/>
      <Listtag appearance='ol'>
        <Link href='/safety/service'>Служба охраны труда</Link>
        <Link href='/safety/suot'>СУОТ</Link>
        <Link href='/safety/education'>Обучение и Инструктажи</Link>
        <Link href='/safety/medical'>Медосмотр</Link>
        <Link href='/safety/firstaid'>Аптечки</Link>
        <Link href='/safety/rules'>Правила и инструкции</Link>
      </Listtag>
    </div>
  )
}
