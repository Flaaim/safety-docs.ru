'use client'

import {z} from 'zod'
import {JSX, useEffect, useState} from "react";
import {Status} from "@/components/Status/Status";


const tokenSchema = z.string().uuid('Неверный формат токена')
export interface PaymentData {
  status: 'succeeded' | 'pending' | 'failed'
  email?: string
  returnToken?: string
  message?: string
}

export const PaymentResult = (): JSX.Element => {
  const [error, setError] = useState<string | null>(null)
  const [PaymentData, setPaymentData] = useState<PaymentData | null>(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    const initPayment = async () => {
      setLoading(true)
      setError(null)


      const token = getToken()
      if(token === null){
        setLoading(false)
        return
      }
      const data = await checkPaymentStatus(token)
      if(data){
        setPaymentData(data)
      }

      setLoading(false)
    }

    initPayment()
  }, []);


  const getToken = (): string | null => {
    if (typeof window === 'undefined') return null
    setLoading(true)
    const urlParams = new URLSearchParams(window.location.search)
    const token = urlParams.get('token')

    if (!token) {
      setError('Ошибка получения токена')
      return null
    }

    const parsed = tokenSchema.safeParse(token)
    if (!parsed.success) {
      setError(parsed.error.issues[0].message)
      return null
    }

    return parsed.data
  }

  const checkPaymentStatus = async (returnToken: string): Promise<PaymentData | null> => {
    try{
      const response = await fetch(`${process.env.NEXT_PUBLIC_BACKEND_URL}/payment-service/result`, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({returnToken})
      })
      const data = await response.json()

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

      return data as PaymentData

    }catch (err){
      setError(err instanceof Error ? err.message : 'Ошибка соединения с сервером')
      return null
    }
  }
  if(loading){
    return <Status appearance='loading'>Загрузка...</Status>
  }
  switch (PaymentData?.status) {
    case 'failed' : return <Status appearance='failed'> Не удалось получить информацию о платеже</Status>
    case 'pending': return <Status appearance='pending'>Платеж обрабатывается</Status>
    case 'succeeded': return <Status appearance='success'>✅ Оплата прошла успешно!</Status>
    default: return <Status appearance='error'>{error}</Status>
  }



}
