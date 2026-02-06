'use client'

import {JSX, useEffect, useState} from "react";
import {ProductInfoProps} from "@/components/ProductInfo/ProductInfo.props";

export interface ProductInfoData {
  name: string
  price: number,
  format: string
  count: number,
}

export const ProductInfo = ({productId}: ProductInfoProps): JSX.Element => {
  const [error, setError] = useState<string | null>(null)
  const [ProductInfoData, setProductInfoData] = useState<ProductInfoData | null>(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    const initProductInfo = async () => {
      setLoading(true)
      setError(null)

      const productInfo = await getProductInfo(productId)
      if(productInfo){
        setProductInfoData(productInfo)
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
        const errorMessage = data.message || `Ошибка ${response.status}: ${response.statusText}`

        switch (response.status){
          case 400 : setError(`Ошибка: ${errorMessage}`)
            break
          case 500 : setError(`Ошибка сервера, попробуйте позже`)
            break
          default : setError(errorMessage)
        }
        return null
      }
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Ошибка соединения с сервером')
      return null
    }
  }

  if(loading){
    return <div>Загрузка...</div>
  }

  return (<div  data={productId}>
    {error}
  </div>)
}
