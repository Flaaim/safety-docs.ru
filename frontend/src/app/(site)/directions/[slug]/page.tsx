
import {getDirectionBySlug, getAllDirections } from "../../../../../api/direction";
import {notFound} from "next/navigation";
import {Htag} from "@/components";
import React from "react";
import MarkdownRenderer from "@/components/MarkdownRenderer";
import normalizeMarkdown  from "@/utils/normalizeMarkdown";
import { cache } from 'react';


const getCachedDirection = cache(async (slug: string) => {
  return await getDirectionBySlug(slug);
})

export  async function generateStaticParams() {
  const data = await getAllDirections()

  return data.directions.map((item) => ({
    slug: item.slug,
  }));
}


export async function generateMetadata({ params }: { params: Promise< {slug:string} >}) {
  const { slug } = await params;
  try{
    const data = await getCachedDirection(slug);
    return {
      title: `${data.title} | Образцы документов`,
      description: `Комплекты документов по направлению: ${data.title}.`,
    };
  }catch (error){
    return {
      title: "Направление не найдено",
    };
  }
}

export default async function DirectionPage({ params }: { params: Promise< {slug:string} > }) {
  const { slug } = await params;

  try{
    const data = await getCachedDirection(slug);

      return (
        <>
          <Htag tag='h1'>{data.title}</Htag>
          <MarkdownRenderer
            content={normalizeMarkdown(data.text)}
          />
        </>
      )
  }catch (error){
    notFound();
  }
}
