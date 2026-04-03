
import {getDirectionBySlug, getAllDirections } from "../../../../../api/direction";
import {notFound} from "next/navigation";
import {Htag} from "@/components";
import React from "react";
import MarkdownRenderer from "@/components/MarkdownRenderer";
import normalizeMarkdown  from "@/utils/normalizeMarkdown";
import { cache } from 'react';
import Link from "next/link";
import {ChevronRight} from "lucide-react";
import {DirectionDTO} from "@/interfaces/direction.interface";
import {CategoryDTO} from "@/interfaces/category.interface";


type Props = {
  params: Promise<{ slug: string[] }>;
};

const getCachedDirection = cache(async (slug: string) => {
  return await getDirectionBySlug(slug);
});

const DirectionView = ({ direction }: { direction: DirectionDTO }) => (
  <>
    <Htag tag='h1'>{direction.title}</Htag>
    <MarkdownRenderer
      content={normalizeMarkdown(direction.text.replace(/\\n/g, '\n'))}
    />
    <Htag tag='h2'>Разделы:</Htag>
    <div className="grid gap-3 sm:grid-cols-2 mt-6">
      {direction.categories.map((category) => (
        <Link
          key={category.slug}
          href={`/docs/${direction.slug}/${category.slug}`}
          className="flex items-center justify-between p-4 rounded-lg border bg-card hover:bg-accent hover:text-accent-foreground transition-colors shadow-sm"
        >
          <span className="font-medium">{category.title}</span>
          <ChevronRight className="h-4 w-4 opacity-50" />
        </Link>
      ))}
    </div>
  </>
);

const CategoryView = ({ category, dirSlug }: { category: CategoryDTO; dirSlug: string }) => (
  <>
    <Link href={`/docs/${dirSlug}`} className="text-sm text-muted-foreground hover:underline mb-4 block">
      ← Назад
    </Link>
    <Htag tag="h1">{category.title}</Htag>
      <MarkdownRenderer content={normalizeMarkdown(category.text.replace(/\\n/g, '\n'))} />
  </>

);
export  async function generateStaticParams() {
  const data = await getAllDirections();

  const paths: { slug: string[] }[] = [];

  data.directions.forEach((dir) => {

    paths.push({ slug: [dir.slug] });

    dir.categories.forEach((cat) => {
      paths.push({ slug: [dir.slug, cat.slug] });
    });
  });

  return paths;
}


export async function generateMetadata({ params }: Props) {
  const { slug } = await params;

  const [dirSlug, catSlug] = slug;

  try{
    const direction  = await getCachedDirection(dirSlug);
    if(catSlug){
      const category = direction.categories.find(c => c.slug === catSlug);

      return {
        title: `${category?.title || 'Категория'} | Охрана труда`,
        description: category?.description
      };
    }

    return {
      title: `${direction.title} | Образцы документов`,
      description: `Комплекты документов по направлению: ${direction.title}.`,
    };
  }catch (error){
    return {
      title: "Не найдено",
    };
  }
}

export default async function DirectionPage({ params }: Props) {
  const { slug } = await params;

  const [dirSlug, catSlug] = slug;

  try{
    const direction = await getCachedDirection(dirSlug);

    if(catSlug){
      const category = direction.categories.find(c => c.slug === catSlug);
      if (!category) notFound();

      return <CategoryView category={category} dirSlug={dirSlug} />;
    }

    return <DirectionView direction={direction} />;
  }catch (error){
    console.error(error);
    return;
    notFound();
  }


}
