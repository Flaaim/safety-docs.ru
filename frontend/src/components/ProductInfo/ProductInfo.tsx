'use client'

import {JSX, useEffect, useState} from "react";
import {ProductInfoProps} from "@/components/ProductInfo/ProductInfo.props";
import {Roboto_Mono} from "next/font/google";

import {DefItem, Deflisttag, DownloadButton, Spantag} from "@/components";
import cn from "classnames";
import styles from './ProductInfo.module.css'
import {Status} from "@/components/Status/Status";
import {ProductInfoData} from "@/interfaces/product.interface";
import {getProductBySlug} from "../../../api/product";

const robotoMono = Roboto_Mono({
  variable: "--font-roboto-mono",
  subsets: ["cyrillic"]
})


export const ProductInfo = ({slug, countFiles, formatFiles, description}: ProductInfoProps): JSX.Element => {
  const [error, setError] = useState<string | null>(null)
  const [productInfoData, setProductInfoData] = useState<ProductInfoData | null>(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    const controller = new AbortController();
    const initProductInfo = async () => {
      setLoading(true)
      setError(null)

      try{
        const data = await getProductBySlug(slug);
        if (!controller.signal.aborted) {
          setProductInfoData(data);
        }
      }catch (err: unknown){
        if (!controller.signal.aborted) {
          const errorMessage = err instanceof Error ? err.message : 'Неизвестная ошибка';
          setError(errorMessage);
        }
      }finally {
        if (!controller.signal.aborted) {
          setLoading(false);
        }
      }
    }
    initProductInfo();
    return () => controller.abort()
  }, [slug])

  if(loading){
    return <Status appearance='loading'>Загрузка...</Status>
  }
  if(error || productInfoData === null){
    return <Status appearance='error'>{error}</Status>
  }
  return (<div className={cn(styles.productInfo, robotoMono.variable)}>
    <Deflisttag >
      <DefItem term='Название' definition={productInfoData.name} />
      <DefItem term='Стоимость' definition={productInfoData.formattedPrice} />
      <DefItem term='Количество' definition={countFiles} />
      <DefItem term='Формат' definition={formatFiles} />
      <DefItem term='Описание' definition={description} />
    </Deflisttag>

    <DownloadButton
      headline={productInfoData.name}
      productId={productInfoData.productId}>
      <Spantag size='s' > Скачать </Spantag> <br />
      <Spantag appearance='bold' size='m'>RAR Архив</Spantag>
    </DownloadButton>
  </div>)
}
