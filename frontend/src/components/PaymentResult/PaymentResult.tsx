'use client';

import {z} from 'zod';
import {JSX, useEffect, useState} from "react";
import {Status} from "@/components/Status/Status";
import {PaymentResultDTO} from "@/interfaces/payment.interface";
import { Suspense } from "react";
import {useSearchParams} from "next/navigation";
import {getPaymentByToken} from "@api/payment";

const tokenSchema = z.string().uuid('Неверный формат токена');

const PaymentResultContent = (): JSX.Element => {
  const searchParams = useSearchParams();

  const [error, setError] = useState<string | null>(null);
  const [paymentResult, setPaymentData] = useState<PaymentResultDTO | null>(null);
  const [loading, setLoading] = useState(true);

  const token = searchParams.get('token');

  useEffect(() => {
    const controller = new AbortController();

    const initPayment = async () => {
      setLoading(true);
      setError(null);
      setPaymentData(null);

      const parsed = tokenSchema.safeParse(token);

      if (!token || !parsed.success) {
        setError(parsed.success ? 'Токен отсутствует' : parsed.error.issues[0].message);
        setLoading(false);
        return;
      }

      try{
        const paymentResult = await getPaymentByToken(token, controller.signal);
        if(!controller.signal.aborted){
          setPaymentData(paymentResult);
        }
      }catch (err: unknown){
        if (err instanceof Error && err.name === 'AbortError') return;

        if(!controller.signal.aborted){
          const errorMessage = err instanceof Error ? err.message : 'Неизвестная ошибка';
          setError(errorMessage);
        }
      }finally {
        if (!controller.signal.aborted) {
          setLoading(false);
        }
      }
    };
    initPayment();
    return () => controller.abort();
  }, [token]);




  if(loading){
    return <Status appearance='loading'>Загрузка...</Status>;
  }
  if(error || paymentResult === null){
    return <Status appearance='error'>{error}</Status>;
  }

  switch (paymentResult.status) {
    case 'failed' :
      return <Status appearance='failed'> Не удалось получить информацию о платеже</Status>;
    case 'pending':
      return <Status appearance='pending'>Платеж обрабатывается</Status>;
    case 'succeeded':
      return <Status appearance='success'>✅ Оплата прошла успешно!</Status>;
    default:
      return <Status appearance='error'>Неизвестный статус платежа</Status>;
  }
};

export default function PaymentResultPage() {
  return <Suspense fallback={<Status appearance='loading'>Загрузка...</Status>}>
    <PaymentResultContent />
  </Suspense>;
}
