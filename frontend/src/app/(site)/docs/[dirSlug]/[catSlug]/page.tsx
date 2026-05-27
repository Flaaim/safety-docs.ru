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
import {ArrowLeft, ChevronRight, FileText, SquareArrowOutUpRight} from "lucide-react";


const getCachedDirection = cache(async (slug: string) => {
  return await getDirectionBySlug(slug);
});


const CategoryView = ({
                        category,
                        dirSlug,
                        product,
                        allCategories
                      }: {
  category: CategoryDTO;
  dirSlug: string;
  product: ProductDTO | null;
  allCategories: CategoryDTO[];
}) => {

  // Ищем дочерние категории для текущей
  const children = allCategories.filter(c => c.parentId === category.id);

  // Проверяем, является ли категория родительской (нет родителя)
  const isParent = category.parentId === null;

  return (
    <div className="max-w-4xl mx-auto py-6 px-4">
      {/* Хлебные крошки / Назад */}
      <nav className="flex items-center gap-2 text-sm text-muted-foreground mb-6">
        {isParent ? (
          <Link href={`/docs/${dirSlug}`} className="flex items-center gap-1 hover:text-primary transition-colors">
            <ArrowLeft size={14} /> Назад к разделу
          </Link>
        ) : (
          <>
            <Link href={`/docs/${dirSlug}`} className="hover:text-primary transition-colors">{category.directionTitle}</Link>
            <ChevronRight size={14} />
            {/* Ищем название родителя для красивой ссылки назад */}
            {allCategories.find(c => c.id === category.parentId) && (
              <Link
                href={`/docs/${dirSlug}/${allCategories.find(c => c.id === category.parentId)?.slug}`}
                className="hover:text-primary transition-colors"
              >
                {allCategories.find(c => c.id === category.parentId)?.title}
              </Link>
            )}
          </>
        )}
      </nav>

      <Htag tag="h1" className="mb-6">{category.title}</Htag>

      {/* --- КЕЙС 1: ЭТО РОДИТЕЛЬ (Показываем вложенность) --- */}
      {isParent && children.length > 0 ? (
        <div className="space-y-8">
          <div className="prose prose-slate max-w-none text-muted-foreground">
            <p>{category.description}</p>
          </div>

          <div className="grid gap-4 sm:grid-cols-2">
            {children.map((child) => (
              <Link
                key={child.id}
                href={`/docs/${dirSlug}/${child.slug}`}
                className="flex items-center justify-between p-3 rounded-lg border bg-card hover:bg-accent hover:text-accent-foreground transition-colors shadow-sm">
                  <FileText className="text-primary/70 group-hover:text-primary" size={32} />
                  <span className="font-medium px-3">{child.title}</span>
                  <SquareArrowOutUpRight className="text-primary/70 group-hover:text-primary" size={32} />
              </Link>
            ))}
          </div>
        </div>
      ) : (
        /* --- КЕЙС 2: ЭТО РЕБЕНОК (Показываем контент и продукт) --- */
        <div className="space-y-10">
          {product && (
            <ProductInfo
              id={product.id}
              formattedPrice={product.formattedPrice}
              updatedAt={product.updatedAt}
              name={product.name}
              cipher={product.cipher}
              filename={product.filename}
              totalDocuments={product.totalDocuments}
              formatDocuments={product.formatDocuments}
              images={product.images}
            />
          )}

          <div className="prose prose-slate max-w-none border-t pt-8">
            <MarkdownRenderer content={normalizeMarkdown(category.text)} />
          </div>
        </div>
      )}
    </div>
  );
};

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

  return <CategoryView
    category={category}
    dirSlug={dirSlug}
    product={product}
    allCategories={direction.categories}
  />;
}
