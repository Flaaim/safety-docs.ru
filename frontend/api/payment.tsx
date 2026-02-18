import {PaymentData, ProcessedPayment} from "@/interfaces/payment.interface";
import {API} from "@/app/api";


export async function getPaymentByToken(token: string, signal?: AbortSignal): Promise<PaymentData> {
  const response = await fetch(API.payment.getByToken(token), {
    signal,
    method: 'GET',
    headers: {'Content-Type': 'application/json'},
  })
  if(!response.ok){
    console.error(`Ошибка получения данных о платеже: ${response.status} status text: ${response.statusText}`)
    throw new Error(`Ошибка получения данных о платеже`)
  }
  return response.json();
}

export async function createPayment(email: string, productId: string, signal?: AbortSignal): Promise<ProcessedPayment> {
  const response = await fetch(API.payment.create(), {
    signal,
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ email, productId }),
  })
  if(!response.ok){
    console.error(`Ошибка при создании платежа: ${response.status} status text: ${response.statusText}`)
    throw new Error(`Ошибка при создании платежа`)
  }
  return response.json()
}
