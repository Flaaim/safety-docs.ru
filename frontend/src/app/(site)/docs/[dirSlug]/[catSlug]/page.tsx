import { notFound } from "next/navigation";
import { getAllDirections, getDirectionBySlug } from "@api/direction";
import { getProductById } from "@api/product";
import { cache } from 'react';
import Link from "next/link";
import { Htag } from "@/components";
import MarkdownRenderer from "@/components/MarkdownRenderer";
import normalizeMarkdown from "@/utils/normalizeMarkdown";
import { ProductInfo } from "@/components/ProductInfo/ProductInfo";
import { CategoryDTO } from "@/interfaces/category.interface";
import { ProductDTO } from "@/interfaces/product.interface";
import {Metadata} from "next";


const getCachedDirection = cache(async (slug: string) => {
  return await getDirectionBySlug(slug);
});


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
      name={product.name}
      cipher={product.cipher}
      filename={product.filename}
      slug={product.slug}
      totalDocuments={product.totalDocuments}
      formatDocuments={product.formatDocuments}
      images={product.images}/>)}

    <MarkdownRenderer content={normalizeMarkdown(category.text)} />
  </>
);

export const dynamicParams = true;

export async function generateStaticParams() {
  try {
    const data = await getAllDirections();
    const paths: { dirSlug: string; catSlug: string }[] = [];

    data.directions.forEach((dir) => {
      dir.categories.forEach((cat) => {
        paths.push({ dirSlug: dir.slug, catSlug: cat.slug });
      });
    });
    return paths;
  } catch {
    return [];
  }
}

export async function generateMetadata({ params }: { params: Promise<{ dirSlug: string; catSlug: string }>}): Promise<Metadata> {
  const { dirSlug, catSlug } = await params;

  try{
    const direction = await getCachedDirection(dirSlug);
    const category = direction.categories.find(c => c.slug === catSlug);

    if(!category){
      return {
        title: "Категория не найдена",
        description: "Запрашиваемая категория не существует.",
      };
    }

    return {
      title: category.title,
      description: category.description
    };
  }catch (error){
    console.error(`Ошибка загрузки метаданных категории ${catSlug}:`, error);
    return {
      title: "Ошибка загрузки",
      description: "Произошла ошибка при загрузке данных о направлении.",
    };
  }
}


export default async function CategoryPage({ params }: { params: Promise<{ dirSlug: string; catSlug: string }> }) {
  const { dirSlug, catSlug } = await params;

  let direction;
  try {
    direction = await getCachedDirection(dirSlug);
  } catch {
    notFound();
  }

  if (!direction) notFound();

  const category = direction.categories.find(c => c.slug === catSlug);
  if (!category) notFound();

  let product = null;
  if (category.productId !== null) {
    product = await getProductById(category.productId);
  }

  return <CategoryView category={category} dirSlug={dirSlug} product={product} />;
}
