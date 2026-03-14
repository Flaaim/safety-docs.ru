
import {getDirectionBySlug, getAllDirections } from "../../../../../api/direction";
import {notFound} from "next/navigation";
import {Htag} from "@/components";
import React from "react";
import MarkdownRenderer from "@/components/MarkdownRenderer";
import normalizeMarkdown  from "@/utils/normalizeMarkdown";

export  async function generateStaticParams() {
  const data = await getAllDirections()

  return data.directions.map((item) => ({
    slug: item.slug,
  }));
}


export default async function DirectionPage({ params }: { params: Promise< {slug:string} > }) {
  const { slug } = await params;

  try{
    const data = await getDirectionBySlug(slug);

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
