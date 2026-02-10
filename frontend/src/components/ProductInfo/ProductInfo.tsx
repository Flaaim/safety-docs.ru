'use client'

import {JSX, useEffect, useState} from "react";
import {ProductInfoProps} from "@/components/ProductInfo/ProductInfo.props";
import styles from './ProductInfo.module.css'
import {DefItem, Deflisttag, DownloadButton, Spantag} from "@/components";
import {Status} from "@/components/Status/Status";
import {Roboto_Mono} from "next/font/google";
import cn from 'classnames'

export interface ProductInfoData {
  name: string
  price: number,
  format: string
  quantity: number,
}

const robotoMono = Roboto_Mono({
  variable: "--font-roboto-mono",
  subsets: ["cyrillic"]
})


export const ProductInfo = ({productId}: ProductInfoProps): JSX.Element => {
  const [error, setError] = useState<string | null>(null)
  const [ProductInfoData, setProductInfoData] = useState<ProductInfoData | null>(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    const initProductInfo = async () => {
      setLoading(true)
      setError(null)

      const productInfoData = await getProductInfo(productId)
      if(productInfoData){
        setProductInfoData(productInfoData)
      }

      setLoading(false)
    }

    initProductInfo();
  }, [productId])

  const getProductInfo = async (productId: string): Promise<ProductInfoData | null> => {
    try {
      const response = await fetch(`${process.env.NEXT_PUBLIC_BACKEND_URL}/payment-service/products/get?id=${productId}`, {
        method: "GET",
        headers: {'Content-Type': 'application/json'},
      })

      const data = response.json();

      if(!response.ok){
        const errorMessage = `Ошибка ${response.status}: ${response.statusText}`

        switch (response.status){
          case 400 : setError(`Ошибка: ${errorMessage}`)
            break
          case 500 : setError(`Ошибка сервера, попробуйте позже`)
            break
          default : setError(errorMessage)
        }
        return null
      }
      return data
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Ошибка соединения с сервером')
      return null
    }
  }

  if(loading){
    return <Status appearance='loading'>Загрузка...</Status>
  }
  if(error){
    return <Status appearance='error'>{error}</Status>
  }
  return (<div className={cn(styles.productInfo, robotoMono.variable)}>
    <Deflisttag >
      <DefItem term='Название' definition={ProductInfoData?.name} />
      <DefItem term='Стоимость' definition={ProductInfoData?.price + ` рублей`} />
      <DefItem term='Количество' definition={ProductInfoData?.quantity + ` файлов`} />
      <DefItem term='Формат файлов' definition={ProductInfoData?.format} />
    </Deflisttag>

    <DownloadButton
      headline={ProductInfoData?.name}
      productId={productId}>
      <Spantag size='s' > Скачать </Spantag> <br />
      <Spantag appearance='bold' size='m'>RAR Архив</Spantag>
    </DownloadButton>
  </div>)
}
