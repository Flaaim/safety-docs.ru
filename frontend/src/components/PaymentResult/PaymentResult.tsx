'use client'

import {z} from 'zod'
import {JSX, useEffect, useState} from "react";
import {Status} from "@/components/Status/Status";
import {getPaymentByToken} from "../../../api/payment";
import {PaymentData} from "@/interfaces/payment.interface";

const tokenSchema = z.string().uuid('Неверный формат токена')

export const PaymentResult = (): JSX.Element => {
  const [error, setError] = useState<string | null>(null)
  const [paymentData, setPaymentData] = useState<PaymentData| null>(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    const controller = new AbortController();
    const initPayment = async () => {
      setLoading(true)
      setError(null)

      const token = getToken()
      if(token === null){
        setLoading(false)
        return
      }
      try{
        const data = await getPaymentByToken(token)
        if(!controller.signal.aborted){
          setPaymentData(data)
        }
      }catch (err: unknown){
        if(!controller.signal.aborted){
          const errorMessage = err instanceof Error ? err.message : 'Неизвестная ошибка';
          setError(errorMessage);
        }
      }finally {
        if (!controller.signal.aborted) {
          setLoading(false);
        }
      }
    }
    initPayment()
    return () => controller.abort()
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

  if(loading){
    return <Status appearance='loading'>Загрузка...</Status>
  }
  if(error || paymentData === null){
    return <Status appearance='error'>{error}</Status>
  }

  switch (paymentData.status) {
    case 'failed' : return <Status appearance='failed'> Не удалось получить информацию о платеже</Status>
    case 'pending': return <Status appearance='pending'>Платеж обрабатывается</Status>
    case 'succeeded': return <Status appearance='success'>✅ Оплата прошла успешно!</Status>
    default: return <Status appearance='error'>{error}</Status>
  }

}
