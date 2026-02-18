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
