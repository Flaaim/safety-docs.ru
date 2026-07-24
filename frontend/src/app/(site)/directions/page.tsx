import { Htag, Navigation, Ptag } from "@/components";
import SimpleCard from "@/components/SimpleCard/SimpleCard";
import { HardHat } from "lucide-react";
import { IconsMap } from "@/types/direction";
import { cache } from "react";
import { getAllDirections } from "@api/direction";
import { Metadata } from "next";
import SiteBreadcrumbs from "@/components/Breadcrumb/SiteBreadcrumbs";

const getCachedDirections = cache(async () => {
  return await getAllDirections();
});

export const metadata: Metadata = {
  title: "Направления документации",
  description: "Выберите направление: охрана труда, пожарная безопасность и другие.",
};

export default async function DirectionsPage() {
  const directions = await getCachedDirections();

  return (
    <>
      <SiteBreadcrumbs items={[{ title: "Направления" }]} />
      <Htag tag="h1">Направления документации</Htag>
      <Ptag>Выберите направление, чтобы перейти к категориям и готовым шаблонам документов.</Ptag>
      <Navigation>
        {directions.map((direction) => {
          const IconComponent = IconsMap[direction.slug] || HardHat;
          return (
            <SimpleCard
              key={direction.id}
              icon={<IconComponent className="inline-block" size={24} />}
              title={direction.title}
              short_description="Подборки документов"
              description={direction.description}
              link={"/directions/" + direction.slug}
            />
          );
        })}
      </Navigation>
    </>
  );
}
