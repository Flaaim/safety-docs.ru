import {Blockquote, Htag, Listtag, Ptag} from "@/components";


export default function Home() {
  return (
    <div >
      <Htag tag='h2'>Полный комплект ЛНА по охране труда на 2026 год</Htag>
      <Ptag>
        Локальные нормативные акты (ЛНА) — это внутренние документы вашей организации, которые регламентируют порядок работы, распределяют обязанности и обеспечивают выполнение требований закона в сфере охраны труда.
      </Ptag>
      <Ptag>
        Разделяют обязательные ЛНА (их наличие прямо требуется законодательством) и рекомендуемые (которые работодатель вводит для эффективного управления и снижения рисков).
      </Ptag>
      <Listtag appearance='ul'>
        <span>Item 1</span>
        <span>Item 2</span>
      </Listtag>
      <Listtag appearance='ol'>
        <span>Item 1</span>
        <span>Item 2</span>
      </Listtag>
    </div>
  );
}
