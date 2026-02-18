import {PaymentData} from "@/interfaces/payment.interface";
import {API} from "@/app/api";


export async function getPaymentByToken(token: string): Promise<PaymentData> {
  const response = await fetch(API.payment.getByToken(token), {
    method: 'GET',
    headers: {'Content-Type': 'application/json'},
  })
  if(!response.ok){
    console.error(`HTTP error! status: ${response.status} status text: ${response.statusText}`)
    throw new Error(`Ошибка получения данных`)
  }
  return response.json();
}
