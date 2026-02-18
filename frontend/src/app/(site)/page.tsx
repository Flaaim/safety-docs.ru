import {
  Htag,
  Navigation,
  Ptag, Section,
} from "@/components";
import Link from "next/link";


export default function Home() {
  return (
    <div >
      <Htag tag='h1'>Полный комплект ЛНА по охране труда на 2026 год</Htag>
      <Ptag>
        Локальные нормативные акты (ЛНА) — это внутренние документы вашей организации, которые регламентируют порядок работы, распределяют обязанности и обеспечивают выполнение требований закона в сфере охраны труда, промышленной, энергетической и пожарной безопасности.
      </Ptag>
      <Ptag>
        Разделяют обязательные ЛНА (их наличие прямо требуется законодательством) и рекомендуемые (которые работодатель вводит для эффективного управления и снижения рисков).
      </Ptag>
      <Ptag>
        Все документы разбиты по следующим категориям:
      </Ptag>
      <Navigation>
        <Link href='/safety'><Section
          title='Охрана труда'
          description=''
        /></Link>
        <Section
          title='Пожарная безопасность'
          description=''
        />
        <Section
          title='Промбезопасность'
          description=''
        />
        <Section
          title='Энергобезопасность'
          description=''
        />
      </Navigation>
    </div>
  );
}
