
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
import {getAllDirections, getDirectionBySlug} from "@api/direction";
import {ProductInfo} from "@/components/ProductInfo/ProductInfo";
import {getProductById} from "@api/product";
import {ProductDTO} from "@/interfaces/product.interface";

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
      content={normalizeMarkdown(direction.text)}
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

const CategoryView = ({ category, dirSlug, product }: { category: CategoryDTO; dirSlug: string, product: ProductDTO | null }) => (
  <>
    <Link href={`/docs/${dirSlug}`} className="text-sm text-muted-foreground hover:underline mb-4 block">
      ← Назад
    </Link>
    <Htag tag="h1">{category.title}</Htag>
      {product && (<ProductInfo
        id={product.id}
        formattedPrice={product.formattedPrice}
        updatedAt={product.updatedAt}
        file={product.file}
        name={product.name}
        cipher={product.cipher}
        filename={product.filename}
        slug={product.slug}
        totalDocuments={product.totalDocuments}
        formatDocuments={product.formatDocuments}
      />)}

      <MarkdownRenderer content={normalizeMarkdown(category.text)} />
  </>

);

export const dynamicParams = true;
export  async function generateStaticParams() {
  try{
    const data = await getAllDirections();

    const paths: { slug: string[] }[] = [];

    data.directions.forEach((dir) => {

      paths.push({ slug: [dir.slug] });

      dir.categories.forEach((cat) => {
        paths.push({ slug: [dir.slug, cat.slug] });
      });
    });

    return paths;
  }catch {
    return [];
  }

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
  }catch {
    return {
      title: "Не найдено",
    };
  }
}

export default async function DirectionPage({ params }: Props) {
  const { slug } = await params;

  const [dirSlug, catSlug] = slug;
  let direction;

  try {
    direction = await getCachedDirection(dirSlug);
  }catch {
    notFound();
  }

  if(catSlug){
    const category = direction.categories.find(c => c.slug === catSlug);
    if (!category) notFound();

    let product = null

    if(category.productId !== null){
      product = await getProductById(category.productId)
    }

    return <CategoryView
      category={category}
      dirSlug={dirSlug}
      product={product}
     />;
  }

  return <DirectionView direction={direction} />;

}
