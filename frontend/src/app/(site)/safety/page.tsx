import type {Metadata} from "next";
import {Htag, Listtag, Ptag} from "@/components";
import Link from "next/link";
import React from "react";
import Breadcrumbs from "@/components/Breadcrumb/Breadcrumbs";
import {ChevronRight} from "lucide-react";



export const metadata: Metadata = {
  title: "Документация по направлениям Охраны труда",
  description: "Собраны комплекты образцов документов по основным направлениями работы по охране труда и техники безопасности.",
};

export default function Safety(){

  const safetyLinks = [
    { href: '/safety/service', title: 'Служба охраны труда' },
    { href: '/safety/suot', title: 'СУОТ' },
    { href: '/safety/education', title: 'Обучение и Инструктажи' },
    { href: '/safety/medical', title: 'Медосмотр' },
    { href: '/safety/firstaid', title: 'Аптечки' },
    { href: '/safety/rules', title: 'Правила и инструкции' },
  ]

  return (
    <div>
      <Breadcrumbs path="/safety" />
      <Htag tag='h1'>Охрана труда — документация по направлениям</Htag>
      <Ptag> Вопросы безопасности на рабочем месте требуют скрупулезного подхода к бумажной работе. Чтобы вам было проще ориентироваться в мире нормативных актов и приказов по охране труда, я собрал полный список документации по охране труда, распределив её по основным направлениям работы организации.

        Все образцы документов, представленные на сайте, актуальны на 2026 год и могут быть адаптированы под специфику вашего производства.</Ptag>
      <Ptag>В каждом разделе добавлена памятка как организовать работу по направлению и перечень документов, которые необходимы</Ptag>
      <Htag tag='h2'>Разделы</Htag>

      <div className="grid gap-3 sm:grid-cols-2 mt-6">
        {safetyLinks.map((link) => (
          <Link
            key={link.href}
            href={link.href}
            className="flex items-center justify-between p-4 rounded-lg border bg-card hover:bg-accent hover:text-accent-foreground transition-colors shadow-sm"
          >
            <span className="font-medium">{link.title}</span>
            <ChevronRight className="h-4 w-4 opacity-50" />
          </Link>
        ))}
      </div>
    </div>
  )
}
