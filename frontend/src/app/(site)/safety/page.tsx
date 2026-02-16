import type {Metadata} from "next";
import {Htag, Section, Navigation} from "@/components";
import Link from "next/link";
import {ShieldUser} from "lucide-react";
import React from "react";



export const metadata: Metadata = {
  title: "Документация по направлениям Охраны труда",
  description: "Собраны комплекты образцов документов по основным направлениями работы по охране труда и техники безопасности.",
};

export default function Safety(){
  return (
    <div>
      <Htag tag='h1'>Охрана труда — документация по направлениям</Htag>
      <Navigation>

        <Link href='/safety/service'><Section description='Служба охраны труда ведет на предприятии работу по охране труда' title='Служба охраны труда' /></Link>
        <Link href='/safety/suot'><Section description='Система управления охраной труда.' title='СУОТ' /></Link>

      </Navigation>
    </div>
  )
}
