import {
  Htag,
  Navigation,
  Ptag,
} from "@/components";
import SimpleCard from "@/components/SimpleCard/SimpleCard";
import {HardHat,} from "lucide-react";
import {IconsMap} from "@/interfaces/direction.interface";
import {cache} from "react";
import {getAllDirections} from "@api/direction";


const getCachedDirections = cache(async () => {
  return await getAllDirections();
});

export default async function Home() {
  const data = await getCachedDirections();

  return (
    <>
      <Htag tag='h1'>Полный комплект ЛНА на 2026 год</Htag>
      <Ptag>
        Локальные нормативные акты (ЛНА) — это внутренние документы вашей организации, которые регламентируют порядок
        работы, распределяют обязанности и обеспечивают выполнение требований закона в сфере охраны труда, промышленной,
        энергетической и пожарной безопасности.
      </Ptag>
      <Ptag>
        Разделяют обязательные ЛНА (их наличие прямо требуется законодательством) и рекомендуемые (которые работодатель
        вводит для эффективного управления и снижения рисков).
      </Ptag>
      <Ptag>
        Все документы разбиты по следующим направлениям:
      </Ptag>
      <div>
        <Htag tag='h2'>Направления документации:</Htag>
        <Navigation>
        {data.directions.map((direction) => {
          const IconComponent = IconsMap[direction.slug] || HardHat;
          return (
            <SimpleCard
            key={direction.id}
            icon={<IconComponent className="inline-block" size={24}/>}
            title={direction.title}
            short_description='Подборки документов'
            description={direction.description}
            link={'/docs/' + direction.slug}
          />
          );
        })}
        </Navigation>
      </div>
    </>
  );
}
